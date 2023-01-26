<?php

function unicode_encode($str) {

    return substr(json_encode($str), 1, -1);

}

function unicode_decode($str) {

    $str = str_replace("u", "\u", $str);
    $jsonStr = '{"str":"'.$str.'"}';
    $arr = json_decode($jsonStr, true);

    if($arr) {
        return $arr['str'];
    }
    return null;

}

function get_rm_ids($includeAdmin=false) {

    $rm_ids = array();

    if ($includeAdmin) {
        $rm_ids[] = 1;
    }
    $rm_ids[] = 12;

    return $rm_ids;

}

function set_online($id) {

    if (!is_numeric($id)) {
        return;
    }

    $online = time();
    $DB = new Database();

    $query = "update users set online = '$online' where userid = '$id' limit 1";
    $DB -> save($query);

}

function pagination_link($page_number, $post_count, $limit, $last_page=null) {

    if ($post_count <= $limit) {
        $arr['next_page'] = "";
        $arr['prev_page'] = "";
        $arr['is_first'] = true;
        $arr['is_last'] = true;
        return $arr;
    }

    $arr['next_page'] = "";
    $arr['prev_page'] = "";
    $arr['is_first'] = false;
    $arr['is_last'] = false;

    $url = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['SCRIPT_NAME'];
    $next_page_link = $url;
    $prev_page_link = $url;
    $page_found = false;
    $query_exist = false;

    $i = 0;
    foreach ($_GET as $key => $value) {
        $i ++;
        if ($i == 1) {
            $next_page_link .= "?";
            $prev_page_link .= "?";
            $query_exist = true;
        }
        else {
            $next_page_link .= "&";
            $prev_page_link .= "&";
        }
        $next_page_link .= $key . "=";
        $prev_page_link .= $key . "=";
        if ($key == "page") {

            if ($page_number == 1) {
                $next_page_link .= 2;
                $prev_page_link .= 1;
                $arr['is_first'] = true;
            }
            else if ($page_number == floor(($post_count - 1) / $limit) + 1 || $page_number == $last_page) {
                $next_page_link .= $page_number;
                $prev_page_link .= ($page_number - 1);
                $arr['is_last'] = true;
            }
            else {
                $next_page_link .= ($page_number + 1);
                $prev_page_link .= ($page_number - 1);
            }
            $page_found = true;
        }
        else {
            $next_page_link .= $value;
            $prev_page_link .= $value;
        }
    }

    if (!$page_found) {
        if ($query_exist) {
            $next_page_link .= "&";
            $prev_page_link .= "&";
        }
        else {
            $next_page_link .= "?";
            $prev_page_link .= "?";
        }
        $next_page_link .= "page=2";
        $prev_page_link .= "page=1";
    }

    $arr['next_page'] = $next_page_link;
    $arr['prev_page'] = $prev_page_link;
    return $arr;

}

function i_own_content($row) {

    $myid = $_SESSION['project_rrmd100zsyff_userid'];

    // if content is information from users
    if (isset($row['gender']) && $myid == $row['userid']) {
        return true;
    }
    // if content is information from posts or comments
    else if (isset($row['postid'])) {

        if ($myid == $row['userid']) {
            return true;
        }
        else {
            $Post = new Post();
            $one_post = $Post -> get_one_post($row['parent']);
            if ($myid == $one_post['userid']) {
                return true;
            }
        }

    }

}

function add_notification($userid, $activity, $row, $tagged_user="") {

    $content_owner = $row['userid'];
    if ($tagged_user != "") {
        $content_owner = $tagged_user;
    }

    // REMOVE THE CONDITION IF ALLOW TO SEE SELF ACTION ON SELF
    if ($content_owner != $_SESSION['project_rrmd100zsyff_userid']) {

        // set timezone
        // access time string with $dt -> format("Y-m-d H:i:s")
        $timezone = "Asia/Hong_Kong";
        $timestamp = time();
        $dt = new DateTime("now", new DateTimeZone($timezone));
        $dt -> setTimestamp($timestamp);
        $date = $dt -> format("Y-m-d H:i:s");

        $contentid = 0;
        $content_type = "";
        if (isset($row['postid'])) {
            $contentid = $row['postid'];
            if ($row['parent'] > 0) {
                $content_type = "comment";
            }
            else {
                $content_type = "post";
            }
        }
        else if (isset($row['gender'])) {
            $contentid = $row['userid'];
            $content_type = "profile";
        }

        $DB = new Database();

        $query = "select * from notifications where userid = '$userid' && content_owner = '$content_owner' && contentid = '$contentid' limit 1";
        $check = $DB -> read($query);

        //UPDATE `users` SET `likes` = '1' WHERE `users`.`id` = 12;
        if (!is_array($check)) {
            $query = "insert into notifications (userid, activity, content_owner, date, contentid, content_type) values ('$userid', '$activity', '$content_owner', '$date', '$contentid', '$content_type')";
            $DB -> save($query);
        }
        else {
            $query = "update notifications set date = '$date' where userid = '$userid' && content_owner = '$content_owner' && contentid = '$contentid' limit 1";
            $DB -> save($query);
        }

    }

}

function add_followed_content($userid, $row) {

    // set timezone
    // access time string with $dt -> format("Y-m-d H:i:s")
    $timezone = "Asia/Hong_Kong";
    $timestamp = time();
    $dt = new DateTime("now", new DateTimeZone($timezone));
    $dt -> setTimestamp($timestamp);
    $date = $dt -> format("Y-m-d H:i:s");

    $contentid = 0;
    $content_type = "";
    if (isset($row['postid'])) {
        $contentid = $row['postid'];
        if ($row['parent'] > 0) {
            $content_type = "comment";
        }
        else {
            $content_type = "post";
        }
    }
    else if (isset($row['gender'])) {
        $contentid = $row['userid'];
        $content_type = "profile";
    }

    $query = "insert into followed_contents (userid, date, contentid, content_type) values ('$userid', '$date', '$contentid', '$content_type')";
    $DB = new Database();
    $DB -> save($query);

}

function notification_seen($notificationid) {

    $userid = $_SESSION['project_rrmd100zsyff_userid'];
    $DB = new Database();

    $query = "select * from notification_seens where userid = '$userid' && notificationid = '$notificationid' limit 1";
    $check = $DB -> read($query);

    if (!is_array($check)) {
        $query = "insert into notification_seens (userid, notificationid) values ('$userid', '$notificationid')";
        $DB -> save($query);
    }

}

function count_notifications() {

    $number = 0;
    $userid = $_SESSION['project_rrmd100zsyff_userid'];
    $DB = new Database();

    $query = "select * from followed_contents where disabled = 0 && userid = '$userid' limit 100";
    $followed_data = $DB -> read($query);
    $follow = Array();
    if (is_array($followed_data)) {
        $follow = array_column($followed_data, "contentid");
    }

    if (count($follow) > 0) {
        $str = "'" . implode("'.'", $follow) . "'";
        $query = "select * from notifications where content_owner = '$userid' || contentid in($str) order by id desc limit 30";
    }
    else {
        $query = "select * from notifications where content_owner = '$userid' order by id desc limit 30";
    }
    $data = $DB -> read($query);

    if (is_array($data)) {

        foreach ($data as $row) {
            $query = "select * from notification_seens where userid = '$userid' && notificationid = '$row[id]' limit 1";
            $check = $DB -> read($query);
            if (!is_array($check)) {
                $number ++;
            }
        }

    }
    return $number;

}

function check_tags($text) {

    $str = "";
    $words = explode(" ", $text);
    if (is_array($words) && count($words) > 0) {

        $DB = new Database();
        foreach ($words as $word) {

            if (preg_match("/@[a-zA-Z_0-9]+/", $word)) {

                $tag_name = addslashes(trim($word, "@,.;:?~()[]{}<>/*&"));
                $query = "select * from users where LOWER(tag_name) = LOWER('$tag_name') limit 1";
                $user_row = $DB -> read($query);

                if (is_array($user_row)) {
                    $user = $user_row[0];
                    $str .= "<a href='profile.php?id=$user[userid]&page=1' style='color: #405d9b;'>$word</a>";
                }
                else {
                    $str .= htmlspecialchars($word);
                }

            }
            else {
                $str .= htmlspecialchars($word);
            }
            $str .= " ";

        }
    }

    $str = rtrim($str);
    if (!empty($str)) {
        return $str;
    }
    return $text;

}

function get_tags($text) {

    $tags = array();
    $words = explode(" ", $text);
    if (is_array($words) && count($words) > 0) {

        $DB = new Database();
        foreach ($words as $word) {

            if (preg_match("/@[a-zA-Z_0-9]+/", $word)) {

                $tag_name = addslashes(trim($word, "@,.;:?~()[]{}<>/*&"));
                $query = "select * from users where LOWER(tag_name) = LOWER('$tag_name') limit 1";
                $user_row = $DB -> read($query);
                if (is_array($user_row)) {
                    $tags[] = $user_row[0]['tag_name'];
                }

            }
        }
    }
    return $tags;

}

function tag($postid, $new_post_text="") {

    $DB = new Database();
    $sql = "select * from posts where postid = '$postid' limit 1";
    $mypost = $DB -> read($sql);

    if (is_array($mypost)) {

        $mypost = $mypost[0];
        if ($new_post_text != "") {
            $old_post = $mypost;
            $mypost['post'] = $new_post_text;
        }
        
        $tags = get_tags($mypost['post']);
        foreach ($tags as $tag) {
            $sql = "select * from users where tag_name = '$tag' limit 1";
            $tagged_user = $DB -> read($sql);
            if (is_array($tagged_user)) {

                $tagged_user = $tagged_user[0];
                if ($new_post_text != "") {
                    if (!in_array($tagged_user['tag_name'], get_tags($old_post['post']))) {
                        add_notification($_SESSION['project_rrmd100zsyff_userid'], "tag", $mypost, $tagged_user['userid']);
                    }
                }
                else {
                    add_notification($_SESSION['project_rrmd100zsyff_userid'], "tag", $mypost, $tagged_user['userid']);
                }

            }
        }

    }

}

function showArray($arr) {
    
    echo "<pre>";
    print_r($arr);
    echo "</pre>";

}