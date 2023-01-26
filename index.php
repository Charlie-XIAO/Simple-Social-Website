<?php

    include("./classes/autoload.php");

    // check if user is logged in
    $login = new Login();
    $user_data = $login -> check_login($_SESSION['project_rrmd100zsyff_userid']);
    $USER = $user_data;

    // check profile to access
    // <-- is_numeric($_GET['id']) --> as variable escaping security
    if (isset($_GET['id']) && is_numeric($_GET['id'])) {
        $profile = new Profile();
        $profile_data = $profile -> get_profile($_GET['id']);
        if (is_array($profile_data)) {
            $user_data = $profile_data[0];
        }
    }

    // post action
    if ($_SERVER['REQUEST_METHOD'] == "POST") {

        $post = new Post();
        $id = $_SESSION['project_rrmd100zsyff_userid'];
        $result = $post -> create_post($id, $_POST, $_FILES);
        
        if ($result == "") {
            header("Location: index.php?page=1");
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
    
?>

<!DOCTYPE html>
<html>

<head>
    <title>动态 | 肉肉米的100种食用方法</title>
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
    <?php include("header.php") ?>

    <!-- cover area -->
    <div style="width: 800px; margin: auto; min-height: 400px;">

        <!-- below cover area -->
        <div style="display: flex;">

            <!-- friends area -->
            <div style="min-height: 400px; flex: 1;">
                <div id="friends_bar">
                    
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
                    ?>

                    <img src="<?php echo $image ?>" id="profile_pic"><br>
                    <a style="color: #405d9b; font-weight: bold; text-decoration: none;" href="profile.php?page=1"><?php echo $user_data['username'] ?></a>
                </div>
            </div>

            <!-- posts area -->
            <div style="min-height: 400px; flex: 2.5; padding: 20px; padding-right: 0;">
            
                <div style="border: solid thin #aaa; padding: 10px; background-color: white;">
                    <form method="post" enctype="multipart/form-data">
                        <?php
                            if (in_array($user_data['id'], get_rm_ids())) {
                                echo "<textarea name='post' style='resize: none;' placeholder='肉肉米大人现在心情如何呀！（期待脸 ヾ(✿ﾟ▽ﾟ)ノ'></textarea>";
                            }
                            else {
                                echo "<textarea name='post' style='resize: none;' placeholder='在此发布您的动态'></textarea>";
                            }
                        ?>
                        <input type="file" name="file">
                        <input id="post_button" type="submit" value="发布"><br>
                    </form>
                </div>

                <!-- posts -->
                <div id="post_bar">

                    <?php

                        $DB = new Database();
                        $user_class = new User();
                        $image_class = new Image();

                        $followers = $user_class -> get_followings($_SESSION['project_rrmd100zsyff_userid'], "user");
                        $followerids = false;
                        if (is_array($followers)) {
                            $followerids = array_column($followers, "userid");
                            $followerids = "'" . implode("', '", $followerids) . "'";
                        }
                        $myuserid = $_SESSION['project_rrmd100zsyff_userid'];

                        // for pagination
                        // set basic variables
                        $limit = 10;
                        if ($followerids) {
                            $sql = "select count(*) from posts where parent = 0 && (userid = '$myuserid' || userid in($followerids))";
                        }
                        else {
                            $sql = "select count(*) from posts where parent = 0 && userid = '$myuserid'";
                        }
                        $post_count = $DB -> read($sql)[0]['count(*)'];
                        // get current page number
                        $page_number = 1;
                        if (isset($_GET['page'])) {
                            $page_number = (int)$_GET['page'];
                        }
                        $offset = ($page_number - 1) * $limit;
                        $pg = pagination_link($page_number, $post_count, $limit);

                        if ($followerids) {
                            $sql = "select * from posts where parent = 0 && (userid = '$myuserid' || userid in($followerids)) order by id desc limit $limit offset $offset";
                        }
                        else {
                            $sql = "select * from posts where parent = 0 && userid = '$myuserid' order by id desc limit $limit offset $offset";
                        }
                        $posts = $DB -> read($sql);

                        if ($posts) {
                            foreach ($posts as $ROW) {
                                $user = new User();
                                $ROW_USER = $user -> get_user($ROW['userid']);
                                include("post.php");
                            }
                        }

                        if (!($pg['is_first'] || $pg['is_last'])) {
                            echo "<div style='padding: 4px; font-size: 13px; display: flex; justify-content: space-between;'>";
                            echo "<a href='$pg[prev_page]'><input id='post_button' type='button' value='上一页' style='width: 75px; cursor: pointer;'></a>";
                            echo "<a href='$pg[next_page]'><input id='post_button' type='button' value='下一页' style='width: 75px; cursor: pointer;'></a>";
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