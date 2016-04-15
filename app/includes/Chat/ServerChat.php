<?php
namespace Chat;

class ServerChat extends Chat {

    private $activeChats;
    private $lastAccess;

    public function __construct($db) {
        parent::__construct($db);
        $this->setActiveChats();
    }

    public function removeChat($id) {
        return $this->db->remove($id);
    }

    public function setActiveChats() {
        $db_res = $this->db->getActiveChats();
        $this->activeChats = $db_res['chats'];
        $this->lastAccess = $db_res['lastAccess'];
    }

    public function getNew($chats = array()) {
        $this->setActiveChats();
        $response = [];
        if (!$this->lastAccess) {
            return json_encode($response);
        }
        foreach ($this->lastAccess as $id => $la) {
            if (!$chats[$id] || $chats[$id] < $la) {
                $messages = $this->getArchive($id, CLIENT, $chats[$id]);
                $response[$id]["messages"] = $messages;
            }
            $response[$id]["online"] = $this->isOnline(CLIENT, $id);
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
            $response[$id]["online"] = $this->isOnline(CLIENT, $id);
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
        $this->setOnline(SERVER);
        
        if ($action === "check") {
            $lastTimes = json_decode(filter_input(INPUT_POST, "lastTime"), true);
            return  $this->getNew($lastTimes);
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
