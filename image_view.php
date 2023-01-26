<?php

    include("./classes/autoload.php");

    // check if user is logged in
    $login = new Login();
    $user_data = $login -> check_login($_SESSION['project_rrmd100zsyff_userid']);
    $USER = $user_data;

    $Post = new Post();
    $ROW = false;

    $ERROR = "";
    if (isset($_GET['id'])) {
        $ROW = $Post -> get_one_post($_GET['id']);
    }
    else {
        $ERROR = "原图像请求错误";
    }

?>

<!DOCTYPE html>
<html>

<head>
    <title>评论 | 肉肉米的100种食用方法</title>
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

                        $user = new User();
                        $image_class = new Image();

                        if (is_array($ROW)) {
                            $ROW_USER = $user -> get_user($ROW['userid']);
                            echo "<img src='$ROW[image]' style='width: 100%;'>";
                        }
                    ?>
                    <br style="clear: both;">
                        
                </div>

            </div>
        </div>

    </div>

</body>

</html>