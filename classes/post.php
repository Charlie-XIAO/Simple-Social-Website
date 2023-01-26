<?php

class Post {

    private $error = "";

    public function create_post($userid, $data, $files) {

        if (!empty($data['post']) || !empty($files['file']['name'])) {

            $myimage = "";
            $has_image = 0;
            $allowed[] = "image/jpeg";            

            if (in_array($files['file']['type'], $allowed)) {
                
            }
            if (!empty($files['file']['name'])) {
                
                if (in_array($files['file']['type'], $allowed)) {
                    $folder = "./uploads/" . $userid . "/";
                    if (!file_exists($folder)) {
                        mkdir($folder, 0777, true);
                    }
                    $image_class = new Image();
                    $myimage = $folder . $image_class -> generate_filename(15) . ".jpg";
                    move_uploaded_file($files['file']['tmp_name'], $myimage);
                    $image_class -> resize_image($myimage, $myimage, 1500, 1500);
                    $has_image = 1;
                }
                else {
                    $this -> error .= "不支持的文件类型: " . $files['file']['type'] . "<br>";
                }
            }

            if ($this -> error == "") {

                $post = $data['post'];
                $postid = $this -> create_postid();
                $DB = new Database();

                // add tagged users
                $tags = json_encode(get_tags($post));
    
                $parent = 0;
                if (isset($data['parent']) && is_numeric($data['parent'])) {
                    
                    $parent = $data['parent'];
                    $mypost = $this -> get_one_post($parent);
    
                    if (is_array($mypost)) {
                        add_notification($_SESSION['project_rrmd100zsyff_userid'], "comment", $mypost);
                        if ($mypost['userid'] != $userid) {
                            add_followed_content($userid, $mypost);
                        }
                    }
    
                    $sql = "update posts set comments = comments + 1 where postid = '$parent' limit 1";
                    $DB -> save($sql);
                }
    
                $query = "insert into posts (userid, postid, post, image, tags, has_image, parent) values ('$userid', '$postid', '$post', '$myimage', '$tags', '$has_image', '$parent')";
                $DB -> save($query);

                // notify tagged users
                tag($postid);

            }

        }
        else {
            $this -> error .= "发布内容不能为空<br>";
        }

        return $this -> error;
    }

    public function edit_post($userid, $data, $files) {

        if (!empty($data['post']) || !empty($files['file']['name'])) {

            $myimage = "";
            $has_image = 0;
            if (!empty($files['file']['name'])) {
                
                $folder = "./uploads/" . $userid . "/";
                if (!file_exists($folder)) {
                    mkdir($folder, 0777, true);
                }
                $image_class = new Image();
                $myimage = $folder . $image_class -> generate_filename(15) . ".jpg";
                move_uploaded_file($_FILES['file']['tmp_name'], $myimage);
                $image_class -> resize_image($myimage, $myimage, 1500, 1500);
                $has_image = 1;
            }

            $post = $data['post'];
            $postid = $data['postid'];
            // notify tagged users
            tag($postid, $post);

            if ($has_image) {
                $query = "update posts set post = '$post', image = '$myimage' where postid = '$postid' limit 1";
            }
            else {
                $query = "update posts set post = '$post' where postid = '$postid' limit 1";
            }

            $DB = new Database();
            $DB -> save($query);

        }
        else {
            $this -> error = "发布内容不能为空<br>";
        }

        return $this -> error;
    }

    public function get_posts($id) {

        $page_number = 1;
        if (isset($_GET['page'])) {
            $page_number = (int)$_GET['page'];
        }
        if ($page_number < 1) {
            $page_number = 1;
        }
        $limit = 10;
        $offset = ($page_number - 1) * $limit;

        $DB = new Database();

        $query = "select * from posts where parent = 0 && userid = '$id' order by id desc limit $limit offset $offset";
        return $DB -> read($query);

    }

    public function get_one_post($postid) {

        if (!is_numeric($postid)) {
            return false;
        }
        $query = "select * from posts where postid = '$postid' limit 1";

        $DB = new Database();
        $result = $DB -> read($query);

        if ($result) {
            return $result[0];
        }
        else {
            return false;
        }
    }

    public function delete_post($postid) {

        if (!is_numeric($postid)) {
            return false;
        }

        $DB = new Database();

        $sql = "select parent from posts where postid = '$postid' limit 1";
        $result = $DB -> read($sql);
        if (is_array($result)) {
            if ($result[0]['parent'] > 0) {
                $parent = $result[0]['parent'];
                $sql = "update posts set comments = comments - 1 where postid = '$parent' limit 1";
                $DB -> save($sql);
            }
        }

        $query = "delete from posts where postid = '$postid' limit 1";
        $DB -> save($query);

        $query = "delete from likes where contentid = '$postid' limit 1";
        $DB -> save($query);

        $sql = "select * from posts where parent = '$postid'";
        $childs_data = $DB -> read($sql);
        if (is_array($childs_data)) {
            foreach ($childs_data as $child) {
                $this -> delete_post($child['postid']);
            }
        }

    }

    public function own_post($postid, $projname_userid) {

        if (!is_numeric($postid)) {
            return false;
        }
        $query = "select * from posts where postid = '$postid' limit 1";

        $DB = new Database();
        $result = $DB -> read($query);

        return is_array($result) && ($result[0]['userid'] == $projname_userid);
    }

    public function like_post($id, $type, $projname_userid) {

        // set timezone
        // access time string with $dt -> format("Y-m-d H:i:s")
        $timezone = "Asia/Hong_Kong";
        $timestamp = time();
        $dt = new DateTime("now", new DateTimeZone($timezone));
        $dt -> setTimestamp($timestamp);
            
        $DB = new Database();

        // save likes details
        $sql = "select likes from likes where type = '$type' && contentid = '$id' limit 1";
        $results = $DB -> read($sql);
        $result = false;
        if (is_array($results)) {
            $result = $results[0];
        }

        // check if post already liked then update, or else insert
        if (is_array($result)) {

            if (is_array(json_decode($result['likes'], true))) {
                $curlikes = json_decode($result['likes'], true);
                $userids = array_column($curlikes, "userid");
            }
            else {
                $curlikes = Array();
                $userids = Array();
            }

            // check if user already liked post then like, or else unlike
            if (!in_array($projname_userid, $userids)) {
                $arr["userid"] = $projname_userid;
                $arr["date"] = $dt -> format("Y-m-d H:i:s");
                $curlikes[] = $arr;

                $likes = json_encode($curlikes);
                $sql = "update likes set likes = '$likes' where type = '$type' && contentid = '$id' limit 1";
                $DB -> save($sql);

                // increment the corresponding table
                $sql = "update {$type}s set likes = likes + 1 where {$type}id = '$id' limit 1";
                $DB -> save($sql);

                // add notification
                if ($type != "user") {
                    $single_post = $this -> get_one_post($id);
                    add_notification($projname_userid, "like", $single_post);
                }
            }
            else {
                $key = array_search($projname_userid, $userids);
                unset($curlikes[$key]);

                $likes = json_encode($curlikes);
                $sql = "update likes set likes = '$likes' where type = '$type' && contentid = '$id' limit 1";
                $DB -> save($sql);

                // decrement the corresponding table
                $sql = "update {$type}s set likes = likes - 1 where {$type}id = '$id' limit 1";
                $DB -> save($sql);
            }
        }
        else {
            $arr["userid"] = $projname_userid;
            $arr["date"] = $dt -> format("Y-m-d H:i:s");
            $array[] = $arr;

            $likes = json_encode($array);
            $sql = "insert into likes (type, contentid, likes) values ('$type', '$id', '$likes')";
            $DB -> save($sql);

            // increment the corresponding table
            $sql = "update {$type}s set likes = likes + 1 where {$type}id = '$id' limit 1";
            $DB -> save($sql);

            // add notification
            if ($type != "user") {
                $single_post = $this -> get_one_post($id);
                add_notification($projname_userid, "like", $single_post);
            }
        }

    }

    public function get_likes($id, $type) {

        $DB = new Database();
        if (is_numeric($id)) {

            $sql = "select likes from likes where type = '$type' && contentid = '$id' limit 1";
            $result = $DB -> read($sql);

            if (is_array($result)) {
                $likes = json_decode($result[0]['likes'], true);
                return $likes;
            }
        }

        return false;
    }

    public function get_comments($id) {

        $page_number = 1;
        if (isset($_GET['page'])) {
            $page_number = (int)$_GET['page'];
        }
        if ($page_number < 1) {
            $page_number = 1;
        }
        $limit = 10;
        $offset = ($page_number - 1) * $limit;

        $query = "select * from posts where parent = '$id' order by id asc limit $limit offset $offset";
        
        $DB = new Database();
        return $DB -> read($query);

    }

    private function create_postid() {
        $length = rand(4, 19);
        $number = "";
        for ($i = 0; $i < $length; $i ++) {
            $new_rand = rand(0, 9);
            $number .= $new_rand;
        }
        return $number;
    }

}