<?php

class Profile {

    public function get_profile($id) {

        $query = "select * from users where userid = '$id' limit 1";

        $DB = new Database();
        return $DB -> read($query);

    }

}