<?php
namespace Chat;
abstract class Chat {

    protected $archDir;
    private $archives;
    protected $who;
    const SERVER = "server";
    const CLIENT = "client";
    const PASSWORD = "748159263";

    public function __construct() {
        $this->archDir = filter_input(INPUT_SERVER, "DOCUMENT_ROOT") . "/app/chat/data/archive";
        $this->statusDir = filter_input(INPUT_SERVER, "DOCUMENT_ROOT") . "/app/chat/data/status";
        $this->archives = [];
    }

    public function comp($a, $b) {
        if ($a["time"] > $b["time"]) {
            return 1;
        } elseif ($a["time"] < $b["time"]) {
            return - 1;
        }
        return 0;
    }

    public function getChat($id, $time = false) {
        $clientFile = Chat::CLIENT . $id;
        $serverFile = Chat::SERVER . $id;

        $client = $this->getArchive($clientFile, Chat::CLIENT, $time);
        $server = $this->getArchive($serverFile, Chat::SERVER, $time);

        $messages = array_merge($client, $server);
        usort($messages, array($this, 'comp'));
        
        return $messages;
    }
    
    abstract public function getResponse($action);
    
    public function getArchive($filename, $who, $fromTime = 0) {
        
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
        $fname = $this->statusDir. "/" . $who.$id."status";
        if(!file_exists($fname)) {
            return false;
        }
        $content = intval(file_get_contents($fname));
        if(time() - $content > 5) {
            return false;
        }
        return true;
    }
    public function setOnline($who, $id = "") {
        $fname = $this->statusDir. "/" . $who.$id."status";
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
}
