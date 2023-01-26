<?php

    include("./classes/autoload.php");

    // check if user is logged in
    $login = new Login();
    $user_data = $login -> check_login($_SESSION['project_rrmd100zsyff_userid']);
    $USER = $user_data;

    // check if coming from notification
    if (isset($_GET['notif'])) {
        notification_seen($_GET['notif']);
    }

    // check profile to access
    // <-- is_numeric($_GET['id']) --> as variable escaping security
    if (isset($_GET['id']) && is_numeric($_GET['id'])) {
        $profile = new Profile();
        $profile_data = $profile -> get_profile($_GET['id']);
        if (is_array($profile_data)) {
            $user_data = $profile_data[0];
        }
    }

    // ========== POSTING STARTS HERE ========== //

    // post action
    if ($_SERVER['REQUEST_METHOD'] == "POST") {

        if (isset($_POST['name'])) {
            $settings_class = new Settings();
            $settings_class -> save_settings($_POST, $_SESSION['project_rrmd100zsyff_userid']);
            if ($_POST['password'] != $_POST['confirmpassword']) {
                echo "<div style='text-align: center; font-size: 12px; color: white; background-color: rgb(59, 89, 152);'>";
                echo "<br>发生以下错误:<br><br>";
                echo "密码不一致，修改失败";
                echo "<br>";
                echo "</div>";
            }
        }
        else {
            $post = new Post();
            $id = $_SESSION['project_rrmd100zsyff_userid'];
            $result = $post -> create_post($id, $_POST, $_FILES);
            
            if ($result == "") {
                header("Location: profile.php?page=1");
                die;
            }
            else {
                echo "<div style='text-align: center; font-size: 12px; color: white; background-color: rgb(59, 89, 152);'>";
                echo "<br>发生以下错误:<br><br>";
                echo $result;
                echo "<br>";
                echo "</div>";
            }
        }

    }

    // collect posts
    $post = new Post();
    $id = $user_data['userid'];
    $posts = $post -> get_posts($id);

    // collect friends
    $user = new User();
    $friends = $user -> get_followings($user_data['userid'], "user");

    // ========== END OF POSTING ========== //

    // image class
    $image_class = new Image();

?>

<!DOCTYPE html>
<html>

<head>
    <title>档案 | 肉肉米的100种食用方法</title>
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
    <?php include("header.php") ?>

    <!-- cover area -->
    <div style="width: 800px; margin: auto; min-height: 400px;">

        <div style="background-color: white; text-align: center; color: #405d9b;">
            <!-- cover image -->
            <?php

                $image = "./images/placeholder_cover.jpg";
                if (file_exists($user_data['cover_image'])) {
                    $image = $image_class -> get_thumb_cover($user_data['cover_image']);
                }

                if (i_own_content($user_data)) {
                    echo "<a href='cp_image.php?change=cover'><img src='$image' style='width: 100%;'></a>";
                }
                else {
                    echo "<img src='$image' style='width: 100%;'>";
                }

            ?>

            <!-- follow button -->
            <a href="like.php?type=user&id=<?php echo $user_data['userid'] ?>">
                <input id="post_button" type="button" value="关注(<?php echo $user_data['likes'] ?>)" style="width: 75px; margin-right: 10px; background-color: #9b409a;">
            </a><br><br><br>

            <!-- profile image -->
            <?php

                if ($user_data['gender'] == "男") {
                    $image = "./images/placeholder_male.jpg";
                }
                else {
                    $image = "./images/placeholder_female.jpg";
                }
                if (file_exists($user_data['profile_image'])) {
                    $image = $image_class -> get_thumb_profile($user_data['profile_image']);
                }

                if (i_own_content($user_data)) {
                    echo "<a href='cp_image.php?change=profile'><img src='$image' id='profile_pic'></a><br>";
                }
                else {
                    echo "<img src='$image' id='profile_pic'><br>";
                }

            ?>
            
            <!-- user info and options -->
            <a style="color: #405d9b; text-decoration: none;" href="profile.php?id=<?php echo $user_data['userid'] ?>&page=1">
                <div style="font-size: 20px; font-weight: bold;"><?php echo $user_data['username'] ?></div>
            </a>
            <div style="font-size: 10px;"><?php echo $user_data['name'] ?> | @<?php echo $user_data['tag_name'] ?></div><br>
            <?php
                if ($user_data['userid'] == $_SESSION['project_rrmd100zsyff_userid']) {
                    echo "<a style='color: #405d9b; text-decoration: none;' href='index.php?page=1'><div id='menu_buttons'>动态</div></a>";
                }
            ?>
            <a style="color: #405d9b; text-decoration: none;" href="profile.php?section=about&id=<?php echo $user_data['userid'] ?>"><div id="menu_buttons">关于</div></a>
            <a style="color: #405d9b; text-decoration: none;" href="profile.php?section=followers&id=<?php echo $user_data['userid'] ?>"><div id="menu_buttons">粉丝</div></a>
            <a style="color: #405d9b; text-decoration: none;" href="profile.php?section=followings&id=<?php echo $user_data['userid'] ?>"><div id="menu_buttons">关注</div></a>
            <a style="color: #405d9b; text-decoration: none;" href="profile.php?section=photos&id=<?php echo $user_data['userid'] ?>"><div id="menu_buttons">相册</div></a>
            <?php
                if ($user_data['userid'] == $_SESSION['project_rrmd100zsyff_userid']) {
                    echo "<a style='color: #405d9b; text-decoration: none;' href='profile.php?section=settings&id=" . $user_data['userid'] . "'><div id='menu_buttons'>设置</div></a>";
                }
            ?>
        </div>

        <!-- below cover area -->
        <?php

            $section = "default";
            if (isset($_GET['section'])) {
                $section = $_GET['section'];
            }

            if ($section == "default") {
                include("profile_content_default.php");
            }
            else if ($section == "about") {
                include("profile_content_about.php");
            }
            else if ($section == "followers") {
                include("profile_content_followers.php");
            }
            else if ($section == "followings") {
                include("profile_content_followings.php");
            }
            else if ($section == "photos") {
                include("profile_content_photos.php");
            }
            else if ($section == "settings") {
                include("profile_content_settings.php");
            }

        ?>

    </div>

</body>

</html>