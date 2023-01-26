<?php

class Signup {

    private $error = "";

    public function evaluate($data) {
        foreach ($data as $key => $value) {

            // check empty slot
            if (empty($value)) {
                if ($key == "name") {
                    $this -> error .= "姓名为空<br>";
                }
                else if ($key == "gender") {
                    $this -> error .= "性别未选择<br>";
                }
                else if ($key == "username") {
                    $this -> error .= "用户名为空<br>";
                }
                else if ($key == "password") {
                    $this -> error .= "密码为空<br>";
                }
                else if ($key == "confirmpassword") {
                    $this -> error .= "确认密码为空<br>";
                }
            }

            // check password for invalid characters
            if ($key == "password") {
                if (preg_match("/[^a-zA-Z0-9_]/", $value) != 0) {
                    $this -> error .= "密码含无效字符<br>";
                }
            }

            // check name for numbers
            if ($key == "name") {
                if (is_numeric($value)) {
                    $this -> error .= "姓名含无效字符<br>";
                }
            }

            // check password confirmed
            if ($key == "confirmpassword") {
                if ($value != "" && $data['password'] != "" && $value != $data['password']) {
                    $this -> error .= "密码输入不一致<br>";
                }
            }

        }
        if ($this -> error == "") {
            $this -> create_user($data);
        }
        else {
            return $this -> error;
        }
    }

    public function create_user($data) {

        $name = $data['name'];
        $gender = $data['gender'];
        $username = $data['username'];
        $password = $data['password'];

        $url_address = $name . "_U:" . $username . "_P:" . $password;
        $userid = $this -> create_userid();
        $password = hash("sha1", $password);
        $tag_name = $username . rand(0, 9999);

        $query = "insert into users (userid, name, gender, username, password, url_address, tag_name)
         values ('$userid', '$name', '$gender', '$username', '$password', '$url_address', '$tag_name')";
        
        $DB = new Database();
        $DB -> save($query);
    }

    private function create_userid() {
        $length = rand(4, 19);
        $number = "";
        for ($i = 0; $i < $length; $i ++) {
            $new_rand = rand(0, 9);
            $number .= $new_rand;
        }
        return $number;
    }

}