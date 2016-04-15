<?php

error_reporting(E_ALL);

use Exception;

define("SERVER", "server");
define("CLIENT", "client");
define("SEP", "|||");
define("SEP_TIME", "||");
define('CLASS_DIR', filter_input(INPUT_SERVER, "DOCUMENT_ROOT").'/app/includes');
set_include_path(get_include_path() . PATH_SEPARATOR . CLASS_DIR);

spl_autoload_extensions(".php");
spl_autoload_register('autoload');

function autoload($className) {
    $fileName = str_replace("\\", "/", $className) . '.php';
    include $fileName;
}
try {
    $chat = Chat\ChatFactory::getInstance()->getChat(
        filter_input(INPUT_POST, "who", FILTER_SANITIZE_STRING), filter_input(INPUT_POST, "client_id", FILTER_SANITIZE_STRING)
    );
    echo $chat->getResponse(filter_input(INPUT_POST, "action", FILTER_SANITIZE_STRING));
} catch (Exception $e) {
    echo $e->getMessage();
}