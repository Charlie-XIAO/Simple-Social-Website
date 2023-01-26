<?php

    include("./classes/autoload.php");

    // check if user is logged in
    $login = new Login();
    $user_data = $login -> check_login($_SESSION['project_rrmd100zsyff_userid']);
    $USER = $user_data;

    $Post = new Post();
    $likes = false;

    $ERROR = "";
    if (isset($_GET['id']) && isset($_GET['type'])) {
        $likes = $Post -> get_likes($_GET['id'], $_GET['type']);
    }
    else {
        $ERROR = "您请求的信息不存在";
    }

?>

<!DOCTYPE html>
<html>

<head>
    <title>点赞的人 | 肉肉米的100种食用方法</title>
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
                    
                    <?php

                        $User = new User();
                        $image_class = new Image();

                        if (is_array($likes)) {
                            
                            foreach ($likes as $row) {
                                $FRIEND_ROW = $User -> get_user($row['userid']);
                                
                                // should have kept accordance with user.php, but make some changes here
                                // include("user.php");
                                echo "<div id='friends'>";

                                $image = "";
                                if ($FRIEND_ROW['gender'] == '男') {
                                    $image = "./images/placeholder_male.jpg";
                                }
                                else if ($FRIEND_ROW['gender'] == '女') {
                                    $image = "./images/placeholder_female.jpg";
                                }
                                if (file_exists($FRIEND_ROW['profile_image'])) {
                                    $image = $image_class -> get_thumb_profile($FRIEND_ROW['profile_image']);
                                }

                                echo "<a href='profile.php?id=$FRIEND_ROW[userid]&page=1' style='text-decoration: none; color: #405d9b;'>";
                                echo "<img src=$image style='width: 40px; margin: 10px; border-radius: 10%; vertical-align: middle;'>";
                                echo $FRIEND_ROW['username'] . "<span style='color: black; font-weight: normal;'> 于 $row[date] 赞了该动态</span>";
                                echo "</a>";
                                echo "</div>";

                            }
                        }
                    ?>
                    <br style="clear: both;">
                        
                </div>

            </div>
        </div>

    </div>

</body>

</html>