<?php
namespace Chat;

class ServerChat extends Chat {

    private $activeChats;
    private $lastAccess;

    public function __construct() {
        parent::__construct();
        $this->setActiveChats();
    }

    public function removeChat($id) {
        $clientFile = $this->archDir . "/" . Chat::CLIENT . $id;
        $serverFile = $this->archDir . "/" . Chat::SERVER . $id;
        $onlineFile = $this->statusDir . "/" . Chat::CLIENT . $id . "status";

        $remove = [$clientFile, $serverFile, $onlineFile];

        foreach ($remove as $file) {
            if (file_exists($file)) {
                unlink($file);
            }
        }
        
        return json_encode(["removed" => true]);
    }

    public function setActiveChats() {
        $iterator = new \DirectoryIterator($this->archDir);
        $chats = [];
        foreach ($iterator as $info) {
            if (!$info->isFile()) {
                continue;
            }
            
            if (strpos($info->getFilename(), Chat::SERVER) === 0) {
                $who = Chat::SERVER;
            } else {
                $who = Chat::CLIENT;
            }

            $fid = str_replace($who, "", $info->getFilename());
            if (!$this->lastAccess[$fid] || $this->lastAccess[$fid] < $info->getMTime()) {
                $this->lastAccess[$fid] = $info->getMTime();
            }
            if (!in_array($fid, $chats)) {
                $chats[] = $fid;
            }
        }
        $this->activeChats = $chats;
    }

    public function getNew($chats = array()) {
        $this->setActiveChats();
        $response = [];
        if (!$this->lastAccess) {
            return json_encode($response);
        }
        foreach ($this->lastAccess as $id => $la) {
            if (!$chats[$id] || $chats[$id] < $la) {
                $messages = $this->getArchive(Chat::CLIENT . $id, "client", $chats[$id]);
                $response[$id]["messages"] = $messages;
            }
            $response[$id]["online"] = $this->isOnline(Chat::CLIENT, $id);
        }
        return json_encode($response);
    }

    public function getActiveChats() {
        return $this->activeChats;
    }

    public function getArchives() {
        return $this->archives;
    }

    public function getStory() {
        $response = [];
        
        foreach ($this->activeChats as $id) {
            $messages = $this->getChat($id);
            $response[$id]["messages"] = $messages;
            $response[$id]["online"] = $this->isOnline(Chat::CLIENT, $id);
        }
        return json_encode($response);
    }

    public function setChats() {
        foreach ($this->activeChats as $id) {
            $messages = $this->getChat($id);
            $this->archives[$id] = $messages;
        }
    }

    public function getResponse($action) {
        $this->setOnline(Chat::SERVER);
        
        if ($action === "check") {
            $chats = json_decode(filter_input(INPUT_POST, "lastTime"), true);
            return  $this->getNew($chats);
        } else if($action === "getArchive") {
            return $this->getStory();
        } else if($action === "send") {
            return $this->send(filter_input(INPUT_POST, "message"),
                    filter_input(INPUT_POST, "who"),
                    filter_input(INPUT_POST, "client_id"));
        } else if($action === "remove") {
            return $this->removeChat(filter_input(INPUT_POST, "id"));
        }
        
        return json_encode(["error" => "wrong action"]);
    }

}
