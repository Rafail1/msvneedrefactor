<?php
namespace Chat;
class Helper {
    
     public static function compTime($a, $b) {
        if ($a["time"] > $b["time"]) {
            return 1;
        } elseif ($a["time"] < $b["time"]) {
            return - 1;
        }
        return 0;
    }
    
}