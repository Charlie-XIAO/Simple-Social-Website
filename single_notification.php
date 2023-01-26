<?php

$actor = $User -> get_user($notif_row['userid']);
$owner = $User -> get_user($notif_row['content_owner']);
if ($actor['userid'] == $_SESSION['project_rrmd100zsyff_userid']) {
    $sub = "你";
    $obj = "自己";
}
else {
    $sub = $actor['username'];
    $obj = "你";
}

$description = "";
$ERROR = "";
$link = "";
$content_exists = true;

if ($notif_row['content_type'] == "post") {
    $content_row = $Post -> get_one_post($notif_row['contentid']);
    $content_exists = is_array($content_row);
    if ($content_exists) {
        $link = "single_post.php?id=" . $notif_row['contentid'] . "&page=1&notif=" . $notif_row['id'];
    }
    if ($notif_row['activity'] == "like") {
        if ($owner['userid'] == $_SESSION['project_rrmd100zsyff_userid']) {
            $description = "赞了" . $obj . "的动态";
        }
        else {
            $description = "赞了" . $obj . "关注的动态";
        }
    }
    else if ($notif_row['activity'] == "comment") {
        if ($owner['userid'] == $_SESSION['project_rrmd100zsyff_userid']) {
            $description = "评论了" . $obj . "的动态";
        }
        else {
            $description = "评论了" . $obj . "关注的动态";
        }
    }
    else if ($notif_row['activity'] == "tag") {
        if ($owner['userid'] == $_SESSION['project_rrmd100zsyff_userid']) {
            $description = "在动态中@了" . $obj;
        }
        else {
            $description = "在关注的动态中@了" . $obj;
        }
    }
    else {
        $ERROR = "动态不支持该操作: " . $notif_row['activity'];
    }
}

else if ($notif_row['content_type'] == "profile") {
    $link = "profile.php?id=" . $notif_row['userid'] . "&page=1&notif=" . $notif_row['id'];
    if ($notif_row['activity'] == "follow") {
        $description = "关注了$obj";
    }
    else {
        $ERROR = "用户档案不支持该操作: " . $notif_row['activity'];
    }
}

else if ($notif_row['content_type'] == "comment") {
    $content_row = $Post -> get_one_post($notif_row['contentid']);
    $content_exists = is_array($content_row);
    if ($content_exists) {
        $link = "single_post.php?id=" . $content_row['parent'] . "&page=1&notif=" . $notif_row['id'];
    }
    if ($notif_row['activity'] == "like") {
        $description = "赞了" . $obj . "的评论";
    }
    else if ($notif_row['activity'] == "tag") {
        $description = "在评论中@了" . $obj;
    }
    else {
        $ERROR = "评论不支持该操作: " . $notif_row['activity'];
    }
}

if ($ERROR != "") {
    echo "<span style='color: black; font-weight: normal;'>$ERROR</span>";
}

$query = "select * from notification_seens where userid = '$id' && notificationid = '$notif_row[id]' limit 1";
$seen = $DB -> read($query);
if ($content_exists) {
    echo "<a href='$link' style='text-decoration: none; color: #405d9b;'>";
}
if (is_array($seen)) {
    echo "<div id='friends' style='background-color: #f6f6f6; border-radius: 10px; margin: 15px;'>";
}
else {
    echo "<div id='friends' style='background-color: #e3ecff; border-radius: 10px; margin: 15px;'>";
}

$actor_image = "";
if ($actor['gender'] == '男') {
    $actor_image = "./images/placeholder_male.jpg";
}
else if ($actor['gender'] == '女') {
    $actor_image = "./images/placeholder_female.jpg";
}
if (file_exists($actor['profile_image'])) {
    $actor_image = $image_class -> get_thumb_profile($actor['profile_image']);
}

echo "<img src='$actor_image' style='width: 40px; margin: 10px; border-radius: 10%; vertical-align: middle;'>";
echo $sub . "<span style='color: black; font-weight: normal;'> $description</span>";

echo "<span style='float: right; margin: 10px; margin-top: 20px; font-weight: normal; color: #999;'>$notif_row[date]</span>";
if ($notif_row['content_type'] == "post" || $notif_row['content_type'] == "comment") {
    if ($content_exists) {
        if ($content_row['has_image'] && file_exists($content_row['image'])) {
            $post_image = $image_class -> get_thumb_post($content_row['image']);
            echo "<img src='$post_image' style='float: right; width: 40px; height: 40px; object-fit: cover; margin: 10px; border-radius: 10%; vertical-align: middle;'>";
        }
    }
}
if ($notif_row['content_type'] == "post" || $notif_row['content_type'] == "comment") {
    if ($content_exists) {
        echo "&nbsp;&nbsp;&nbsp; <span style='float: right; margin: 10px; margin-top: 20px; font-weight: normal; color: #999;'>";
        echo htmlspecialchars(mb_substr($content_row['post'], 0, 10, "UTF-8"));
        if (mb_strlen($content_row['post']) > 10) {
            echo "...";
        }
        echo "</span>";
    }
    else {
        echo "&nbsp;&nbsp;&nbsp; <span style='float: right; margin: 10px; margin-top: 20px; font-weight: bold; color: black;'>内容已删除</span>";
    }
}

echo "</div>";
if ($content_exists) {
    echo "</a>";
}