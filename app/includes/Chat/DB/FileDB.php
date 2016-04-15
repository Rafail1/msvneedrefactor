<?php

namespace Chat\DB;

class FileDB {

    private $archDir;
    private $statusDir;

    public function __construct() {
        $this->archDir = filter_input(INPUT_SERVER, "DOCUMENT_ROOT") . "/app/chat/data/archive";
        $this->statusDir = filter_input(INPUT_SERVER, "DOCUMENT_ROOT") . "/app/chat/data/status";
    }

    public function getById($id, $time) {
        $clientFile = CLIENT . $id;
        $serverFile = SERVER . $id;

        $client = $this->getArchive($clientFile, CLIENT, $time);
        $server = $this->getArchive($serverFile, SERVER, $time);

        $messages = array_merge($client, $server);
        usort($messages, '\Chat\Helper::compTime');

        return $messages;
    }

    public function remove($id) {

        $clientFile = $this->archDir . "/" . CLIENT . $id;
        $serverFile = $this->archDir . "/" . SERVER . $id;
        $onlineFile = $this->statusDir . "/" . CLIENT . $id . "status";

        $remove = [$clientFile, $serverFile, $onlineFile];

        foreach ($remove as $file) {
            if (file_exists($file)) {
                unlink($file);
            }
        }

        return json_encode(["removed" => true]);
    }

    public function getArchive($id, $who, $fromTime = 0) {
        $filename = $who . $id;
        $fname = $this->archDir . "/" . $filename;
        $response = [];
        if (!file_exists($fname)) {
            return $response;
        }
        $content = file_get_contents($fname);
        $messages = explode(SEP, $content);

        foreach ($messages as $mess) {

            if (!$mess) {
                continue;
            }
            $mess = explode(SEP_TIME, $mess);
            $time = $mess[1];
            if ($fromTime && $time <= $fromTime) {
                continue;
            }
            $message = $mess[0];

            $response[] = ["id" => $time, "time" => $time, "text" => $message, "who" => $who];
        }

        return $response;
    }

    public function isOnline($who, $id = "") {
        $fname = $this->statusDir . "/" . $who . $id . "status";
        if (!file_exists($fname)) {
            return false;
        }
        $content = intval(file_get_contents($fname));
        if (time() - $content > 5) {
            return false;
        }
        return true;
    }

    public function setOnline($who, $id = "") {
        $fname = $this->statusDir . "/" . $who . $id . "status";
        $fp = fopen($fname, "w");
        fwrite($fp, time());
        fclose($fp);
    }

    public function send($message, $who, $id) {
        $fname = $this->archDir . "/" . $who . $id;
        $time = time();
        $fp = @fopen($fname, "a+");
        fwrite($fp, SEP . $message . SEP_TIME . $time);
        fclose($fp);
        return json_encode(["id" => $time, "time" => $time, "text" => $message, "who" => $who]);
    }

    public function getActiveChats() {
        
        $iterator = new \DirectoryIterator($this->archDir);
        $lastAccess = $chats = [];

        foreach ($iterator as $info) {
            if (!$info->isFile()) {
                continue;
            }

            if (strpos($info->getFilename(), SERVER) === 0) {
                $who = SERVER;
            } else {
                $who = CLIENT;
            }

            $fid = \str_replace($who, "", $info->getFilename());
            if (!$lastAccess[$fid] || $lastAccess[$fid] < $info->getMTime()) {
                $lastAccess[$fid] = $info->getMTime();
            }
            if (!in_array($fid, $chats)) {
                $chats[] = $fid;
            }
        }

        return ["lastAccess" => $lastAccess, "chats" => $chats];
    }

}
