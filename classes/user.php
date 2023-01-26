<?php

class User {

    public function get_data($id) {

        $query = "select * from users where userid = '$id' limit 1";

        $DB = new Database();
        $result = $DB -> read($query);

        if ($result) {
            $row = $result[0];
            return $row;
        }
        else {
            return false;
        }
    }

    public function get_user($id) {

        $query = "select * from users where userid = '$id' limit 1";
        $DB = new Database();
        $result = $DB -> read($query);

        if ($result) {
            return $result[0];
        }
        else {
            return false;
        }
    }

    public function get_friends($id) {

        $query = "select * from users where userid != '$id'";
        $DB = new Database();
        $result = $DB -> read($query);

        if ($result) {
            return $result;
        }
        else {
            return false;
        }

    }

    public function follow_user($id, $type, $projname_userid) {

        // set timezone
        // access time string with $dt -> format("Y-m-d H:i:s")
        $timezone = "Asia/Hong_Kong";
        $timestamp = time();
        $dt = new DateTime("now", new DateTimeZone($timezone));
        $dt -> setTimestamp($timestamp);
            
        $DB = new Database();

        // save likes details
        $sql = "select following from likes where type = '$type' && contentid = '$projname_userid' limit 1";
        $results = $DB -> read($sql);
        $result = false;
        if (is_array($results)) {
            $result = $results[0];
        }

        // check if post already liked then update, or else insert
        if (is_array($result)) {

            if (is_array(json_decode($result['following'], true))) {
                $curfollowing = json_decode($result['following'], true);
                $userids = array_column($curfollowing, "userid");
            }
            else {
                $curfollowing = Array();
                $userids = Array();
            }

            // check if user already liked post then like, or else unlike
            if (!in_array($id, $userids)) {
                $arr["userid"] = $id;
                $arr["date"] = $dt -> format("Y-m-d H:i:s");
                $curfollowing[] = $arr;

                $following = json_encode($curfollowing);
                $sql = "update likes set following = '$following' where type = '$type' && contentid = '$projname_userid' limit 1";
                $DB -> save($sql);
                // users table incremented in post class like_post, no need to perform again
            
                // add notification
                $single_user = $this -> get_user($id);
                add_notification($projname_userid, "follow", $single_user);
            }
            else {
                $key = array_search($id, $userids);
                unset($curfollowing[$key]);

                $following = json_encode($curfollowing);
                $sql = "update likes set following = '$following' where type = '$type' && contentid = '$projname_userid' limit 1";
                $DB -> save($sql);
                // users table decremented in post class like_post, no need to perform again
            }
        }
        else {
            $arr["userid"] = $id;
            $arr["date"] = $dt -> format("Y-m-d H:i:s");
            $array[] = $arr;

            $following = json_encode($array);
            $sql = "insert into likes (type, contentid, following) values ('$type', '$projname_userid', '$following')";
            $DB -> save($sql);
            // users table incremented in post class like_post, no need to perform again

            // add notification
            $single_user = $this -> get_user($id);
            add_notification($projname_userid, "follow", $single_user);
        }

    }

    public function get_followings($id, $type) {

        $DB = new Database();
        if (is_numeric($id)) {

            $sql = "select following from likes where type = '$type' && contentid = '$id' limit 1";
            $result = $DB -> read($sql);

            if (is_array($result)) {
                $followings = json_decode($result[0]['following'], true);
                return $followings;
            }
        }

        return false;
    }

}