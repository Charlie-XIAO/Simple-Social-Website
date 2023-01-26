<?php

// check if user is logged in
if (!isset($_SESSION['project_rrmd100zsyff_userid'])) {
    die;
}

$query_string = explode("?", $data['link']);
$query_string = end($query_string);
$str = explode("&", $query_string);

foreach ($str as $value) {
    $value = explode("=", $value);
    $_GET[$value[0]] = $value[1];
}

if (isset($_GET['type']) && isset($_GET['id'])) {

    $post = new Post();
    $user_class = new User();

    if (is_numeric($_GET['id'])) {
        $post -> like_post($_GET['id'], $_GET['type'], $_SESSION['project_rrmd100zsyff_userid']);
        if ($_GET['type'] == "user") {
            $user_class -> follow_user($_GET['id'], $_GET['type'], $_SESSION['project_rrmd100zsyff_userid']);
        }
    }

    // ========== READ LIKES ========== //
    $likes = $post -> get_likes($_GET['id'], $_GET['type']);
    $likes_count = count($likes);
    $info = "<a id='info_$_GET[id]' href='likes.php?type=post&id=$_GET[id]' style='text-decoration: none;'><span style='color: #999;'>";
    // check if I liked the post
    $i_liked = false;
    $userids = array_column($likes, "userid");
    $i_liked = in_array($_SESSION['project_rrmd100zsyff_userid'], $userids);
    // distinguish comments and posts
    $item = "动态";
    if ($_GET['type'] == "post") {
        $DB = new Database();
        $sql = "select parent from posts where postid = '$_GET[id]' limit 1";
        $result = $DB -> read($sql);
        if (is_array($result)) {
            if ($result[0]['parent'] != 0) {
                $item = "评论";
            }
        }
    }

    if ($likes_count == 1) {
        $info .= "<br>";
        if ($i_liked) {
            $info .= "我赞了该$item";
        }
        else {
            $info .= "1人赞了该$item";
        }
    }
    else if ($likes_count > 0) {
        $info .= "<br>";
        if ($i_liked) {
            $info .= "我和其他" . ($likes_count - 1) . "人赞了该$item";
        }
        else {
            $info .= $likes_count . "人赞了该$item";
        }
    }

    $info .= "</span></a>";
    // ========== END READ LIKES ========== /

    $arr = Array();
    $arr['id'] = "info_$_GET[id]";
    $arr['likes'] = $likes_count;
    $arr['action'] = "like_post";
    $arr['info'] = $info;

    echo json_encode($arr);

}