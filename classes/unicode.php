<?php

class Unicode {

    public function unicode_encode($str) {

        return substr(json_encode($str), 1, -1);
        
    }

    public function unicode_decode($str) {

        $str = str_replace("u", "\u", $str);
        $jsonStr = '{"str":"'.$str.'"}';
        $arr = json_decode($jsonStr, true);

        if($arr) {
            return $arr['str'];
        }
        return null;

    }

}