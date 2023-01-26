<?php

    include("./classes/autoload.php");

    // check if user is logged in
    $login = new Login();
    $user_data = $login -> check_login($_SESSION['project_rrmd100zsyff_userid']);
    $USER = $user_data;

    if (isset($_SERVER['HTTP_REFERER']) && !strstr($_SERVER['HTTP_REFERER'], "edit.php")) {
        $_SESSION['return_to'] = $_SERVER['HTTP_REFERER'];
    }

    $Post = new Post();
    $ERROR = "";
    if (isset($_GET['id'])) {
        $ROW = $Post -> get_one_post($_GET['id']);
        if (!$ROW) {
            $ERROR = "该动态不存在";
        }
        else if ($ROW['userid'] != $_SESSION['project_rrmd100zsyff_userid']) {
            $ERROR = "您没有删除该动态的权限";
        }
    }
    else {
        $ERROR = "该动态不存在";
    }

    // if something was posted
    if ($_SERVER['REQUEST_METHOD'] == "POST") {

        $Post -> edit_post($ROW['userid'], $_POST, $_FILES);

        if (isset($_SESSION['return_to'])) {
            header("Location: " . $_SESSION['return_to']);
        }
        else {
            header("Location: profile.php?page=1");
        }
        die;
    }

?>

<!DOCTYPE html>
<html>

<head>
    <title>删除 | 肉肉米的100种食用方法</title>
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

    #profile_pic {
        width: 150px;
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
        min-height: 400px;
        margin-top: 20px;
        color: #405d9b;
        padding: 8px;
        text-align: center;
        font-size: 20px;
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
    <?php
        
        if ($ERROR != "") {
            echo "<div style='text-align: center; font-size: 12px; color: white; background-color: #405d9b;'>";
            echo "<br>发生以下错误:<br><br>";
            echo $ERROR;
            echo "<br><br>";
            echo "</div>";
        }
        include("header.php");

    ?>

    <!-- cover area -->
    <div style="width: 800px; margin: auto; min-height: 400px;">

        <!-- below cover area -->
        <div style="display: flex;">

            <!-- posts area -->
            <div style="min-height: 400px; flex: 2.5; padding: 20px; padding-right: 0;">
            
                <div style="border: solid thin #aaa; padding: 10px; background-color: white;">
                    <form method="post" enctype="multipart/form-data">
                        <?php
                            if ($ROW && $ERROR == "") {
                                $user = new User();
                                $ROW_USER = $user -> get_user($ROW['userid']);
                                if (in_array($ROW_USER['id'], get_rm_ids())) {
                                    $msg = "肉肉米大人可以在此随意编辑该动态哦~ (◕ᴗ◕✿)";
                                }
                                else {
                                    $msg = "请在此处编辑该动态";
                                }
                                echo $msg . "<br><br>";
                                echo "<textarea name='post' style='resize: none;' placeholder='$msg'>" . $ROW['post'] . "</textarea><input type='file' name='file'>";

                                echo "<input type='hidden' name='postid' value='$ROW[postid]'>";
                                echo "<input id='post_button' type='submit' value='修改' style='cursor: pointer;'><br>";

                                if (file_exists($ROW['image'])) {
                                    $image_class = new Image();
                                    $post_image = $image_class -> get_thumb_post($ROW['image']);
                                    echo "<br><div style='text-align: center;'><img src='$post_image' style='width: 80%;'></div>";
                                }
                            }
                        ?>
                    </form>
                </div>

            </div>
        </div>

    </div>

</body>

</html>