<?php
namespace Chat;

class ChatFactory  {

    static private $_instance;
    
    private function __construct() {}

    protected function __clone() {}

    protected function __sleep() {}

    protected function __wakeup() {}
    
    static public function getInstance() {
       
        if(is_null(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
    
    public function getChat($who, $client_id = false) {
       
        $db = new DB\FileDB();
        if($who === CLIENT) {
            $chat = new ClientChat($db,$client_id);
        } elseif($who === SERVER) {
            $chat = new ServerChat($db);
        }
        return $chat;
    }
}
