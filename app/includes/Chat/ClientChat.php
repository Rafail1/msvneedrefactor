<?php
namespace Chat;
class ClientChat extends Chat {

    private $client_id;

    public function __construct($client_id) {
        parent::__construct();
        $this->client_id = $client_id;
    }

    public function getMessage($time) {
        $fname = Chat::SERVER . $this->client_id;
       
        $response["messages"] = $this->getArchive($fname, Chat::SERVER, $time);
        $response["online"] = $this->isOnline(Chat::SERVER);
        return json_encode($response);
    }

    public function getResponse($action) {
        
        $this->setOnline(Chat::CLIENT, $this->client_id);
        if($action === "check") {
            return $this->getMessage(filter_input(INPUT_POST, "lastTime"));
        } else if ($action === "getArchive") {
            return $this->getStory();
        } else if($action === "send") {
            return $this->send(filter_input(INPUT_POST, "message"), 
                    filter_input(INPUT_POST, "who"),
                    filter_input(INPUT_POST, "client_id"));
        }
        return json_encode(["error" => "wrong action"]);
    }
    
    public function getStory() {
        $messages = $this->getChat($this->client_id);
        return json_encode(["messages" => $messages, "online" => $this->isOnline(Chat::SERVER)]);
    }
}
