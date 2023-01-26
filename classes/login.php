<?php

class Login {
    
    private $error = "";

    public function evaluate($data) {

        $username = $data['username'];
        $password = $data['password'];

        $query = "select * from users where username = '$username' limit 1";

        $DB = new Database();
        $result = $DB -> read($query);

        if ($result) {
            $row = $result[0];
            if ($this -> hash_text($password) == $row['password']) {
                // create session data
                $_SESSION['project_rrmd100zsyff_userid'] = $row['userid'];
            }
            else {
                $this -> error .= "用户名或密码错误<br>";
            }
        }
        else {
            $this -> error .= "用户名或密码错误<br>";
        }
        return $this -> error;
    }
    
    private function hash_text($text) {

        $text = hash("sha1", $text);
        return $text;

    }

    // the $redirect parameter is for sharing profile, for details see Part 118
    // URL: https://youtu.be/h13vK3fwRms
    public function check_login($id, $redirect=true) {

        if (is_numeric($_SESSION['project_rrmd100zsyff_userid'])) {

            $query = "select * from users where userid = '$id' limit 1";

            $DB = new Database();
            $result = $DB -> read($query); 

            if ($result) {
                $user_data = $result[0];
                return $user_data;
            }
            else if ($redirect) {
                header("Location: login.php");
                die;
            }
            else {
                $_SESSION['project_rrmd100zsyff_userid'] = 0;
            }
        }
        else if ($redirect) {
            header("Location: login.php");
            die;
        }
        else {
            $_SESSION['project_rrmd100zsyff_userid'] = 0;
        }
    }
    
}