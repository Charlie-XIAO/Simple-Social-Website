<?php

    include("./classes/autoload.php");

    // check if user is logged in
    $login = new Login();
    $user_data = $login -> check_login($_SESSION['project_rrmd100zsyff_userid']);
    $USER = $user_data;

    if (isset($_GET['id']) && is_numeric($_GET['id'])) {
        $profile = new Profile();
        $profile_data = $profile -> get_profile($_GET['id']);
        if (is_array($profile_data)) {
            $user_data = $profile_data[0];
        }
    }

    $Post = new Post();
    $User = new User();
    $image_class = new Image();

?>

<!DOCTYPE html>
<html>

<head>
    <title>通知 | 肉肉米的100种食用方法</title>
</head>

<style type="text/css">

    #blue_bar {
        height: 50px;
        background-color: #405d9b;
        color: #d9dfeb;
    }

    #search_box {
        margin-top: 10px;
        width: 360px;
        height: 20px;
        border-radius: 5px;
        border: none;
        padding: 4px;
        font-size: 14px;
        background-image: url(./images/search-icon.png);
        background-repeat: no-repeat;
        background-position: right;
    }

    #textbox {
        margin: 8px;
        height: 25px;
        width: 300px;
        border-radius: 4px;
        border: solid 1px #aaa;
        padding: 4px;
        font-size: 14px;
    }

    #profile_pic {
        width: 150px;
        margin-top: -200px;
        border-radius: 50%;
        border: solid 2px white;
        overflow: hidden;
    }

    #menu_buttons {
        width: 100px;
        display: inline-block;
        margin: 2px;
    }

    #friends_img {
        width: 75px;
        float: left;
        margin: 8px;
    }

    #friends_bar {
        background-color: white;
        min-height: 400px;
        margin-top: 20px;
        color: #405d9b;
        padding: 8px;
        font-weight: bold;
    }

    #friends {
        clear: both;
        font-size: 12px;
        font-weight: bold;
        color: #405d9b;
    }

    textarea {
        width: 100%;
        border: none;
        font-family: tahoma;
        font-size: 14px;
        height: 60px;
    }

    textarea:focus {
        outline: none;
    }

    #post_button {
        float: right;
        background-color: #405d9b;
        border: none;
        color: white;
        padding: 4px;
        font-size: 14px;
        border-radius: 2px;
        width: 50px;
        min-width: 50px;
        cursor: pointer;
    }

    #post_bar {
        margin-top: 20px;
        background-color: white;
        padding: 10px;
    }

    #post {
        padding: 4px;
        font-size: 13px;
        display: flex;
        margin-bottom: 20px;
    }

</style>

<body style="font-family: tahoma; background-color: #d0d8e4;">
    
    <br>
    <!-- top bar -->
    <?php include("header.php"); ?>

    <!-- cover area -->
    <div style="width: 800px; margin: auto; min-height: 400px;">

        <!-- below cover area -->
        <div style="display: flex;">

            <!-- posts area -->
            <div style="min-height: 400px; flex: 2.5; padding: 20px; padding-right: 0;">

                <div style="border: solid thin #aaa; padding: 10px; background-color: white;">

                    <?php

                        $DB = new Database();
                        $id = $_SESSION['project_rrmd100zsyff_userid'];

                        $query = "select * from followed_contents where disabled = 0 && userid = '$id' limit 100";
                        $followed_data = $DB -> read($query);
                        $follow = Array();
                        if (is_array($followed_data)) {
                            $follow = array_column($followed_data, "contentid");
                        }

                        // for pagination
                        // set basic variables
                        $limit = 10;
                        if (count($follow) > 0) {
                            $str = "'" . implode("'.'", $follow) . "'";
                            $query = "select count(*) from notifications where content_owner = '$id' || contentid in($str) order by id desc limit 30";
                        }
                        else {
                            $query = "select count(*) from notifications where content_owner = '$id' order by id desc limit 30";
                        }
                        $notification_count = $DB -> read($query)[0]['count(*)'];
                        // get current page number
                        $page_number = 1;
                        if (isset($_GET['page'])) {
                            $page_number = (int)$_GET['page'];
                        }
                        $offset = ($page_number - 1) * $limit;
                        $pg = pagination_link($page_number, $notification_count, $limit, 3);

                        if (count($follow) > 0) {
                            $str = "'" . implode("'.'", $follow) . "'";
                            $query = "select * from notifications where content_owner = '$id' || contentid in($str) order by id desc limit $limit offset $offset";
                        }
                        else {
                            $query = "select * from notifications where content_owner = '$id' order by id desc limit $limit offset $offset";
                        }
                        $data = $DB -> read($query);
                        
                        if (is_array($data)) {
                            foreach ($data as $notif_row) {
                                include("single_notification.php");
                            }
                        }
                        else {
                            echo "暂无通知";
                        }

                        echo "<br>";
                        if (!($pg['is_first'] || $pg['is_last'])) {
                            echo "<div style='height: 40px; padding: 4px; font-size: 13px;'>";
                            echo "<a href='$pg[prev_page]'><input id='post_button' type='button' value='上一页' style='float: left; width: 75px; cursor: pointer;'></a>";
                            echo "<a href='$pg[next_page]'><input id='post_button' type='button' value='下一页' style='float: right; width: 75px; cursor: pointer;'></a>";
                            echo "</div>";
                        }
                        else if (!($pg['is_last'])) {
                            echo "<div style='padding: 4px; font-size: 13px; display: flex; justify-content: flex-end;'>";
                            echo "<a href='$pg[next_page]'><input id='post_button' type='button' value='下一页' style='width: 75px; cursor: pointer;'></a>";
                            echo "</div>";
                        }
                        else if (!($pg['is_first'])) {
                            echo "<div style='padding: 4px; font-size: 13px; display: flex; justify-content: flex-start;'>";
                            echo "<a href='$pg[prev_page]'><input id='post_button' type='button' value='上一页' style='width: 75px; cursor: pointer;'></a>";
                            echo "</div>";
                        }

                    ?>

                </div>

            </div>
        </div>

    </div>

</body>

</html>