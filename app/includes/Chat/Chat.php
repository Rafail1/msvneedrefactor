<?php

namespace Chat;

abstract class Chat {
    
    private $archives;

    const PASSWORD = "748159263";
    public $db;

    public function __construct($db) {
        $this->setDB($db);
        $this->archives = [];
    }
    
    public function setDB($db) {
        $this->db = $db;
    }

    public function getChat($id, $time = false) {
        return $this->db->getById($id, $time);
    }

    abstract public function getResponse($action);

    public function getArchive($id, $who, $fromTime = 0) {
        return $this->db->getArchive($id, $who, $fromTime);
    }


    public function isOnline($who, $id = "") {
        return $this->db->isOnline($who, $id);
    }

    public function setOnline($who, $id = "") {
        $this->db->setOnline($who, $id);
    }

    public function send($message, $who, $id) {
        return $this->db->send($message, $who, $id);
    }

}
