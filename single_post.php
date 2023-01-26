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

    if (isset($_GET['id'])) {
        $jumpid = "?id=" . $_GET['id'] . "&page=1";
    }
    else {
        $jumpid = "?page=1";
    }

    // post action
    if ($_SERVER['REQUEST_METHOD'] == "POST") {

        $post = new Post();
        $id = $_SESSION['project_rrmd100zsyff_userid'];
        $result = $post -> create_post($id, $_POST, $_FILES);
            
        if ($result == "") {
            header("Location: single_post.php$jumpid");
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

    $Post = new Post();
    $ROW = false;

    $ERROR = "";

    if (isset($_GET['id'])) {
        $ROW = $Post -> get_one_post($_GET['id']);
    }
    else {
        $ERROR = "消息请求错误";
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
                            
                            // below should have been include("post.php");
                            // but we need to remove the comment button so forced to redo

                            echo "<div id='post'>";
                            echo "<div>";
                            
                            $image = "";
                            if ($ROW_USER['gender'] == '男') {
                                $image = "./images/placeholder_male.jpg";
                            }
                            else if ($ROW_USER['gender'] == '女') {
                                $image = "./images/placeholder_female.jpg";
                            }
                            if (file_exists($ROW_USER['profile_image'])) {
                                $image = $image_class -> get_thumb_profile($ROW_USER['profile_image']);
                            }
                            
                            echo "<img src='$image' style='width: 75px; margin-right: 10px; border-radius: 10%;'>";
                            echo "</div>";
                            echo "<div style='width: 100%;'>";
                            echo "<div style='font-weight: bold; color: #405d9b;'>";
                            echo "<a href='profile.php?id=$ROW[userid]&page=1' style='text-decoration: none; color: #405d9b;'>" . htmlspecialchars($ROW_USER['username']) . "</a>";
                            echo "</div>";
                            echo check_tags(htmlspecialchars($ROW['post'])) . "<br><br>";
                            
                            if (file_exists($ROW['image'])) {
                                $post_image = $image_class -> get_thumb_post($ROW['image']);
                                echo "<img src='$post_image' style='width: 80%;'>";
                            }
                            
                            echo "<br><br>";
                            echo "<a onclick='like_post(event)' href='like.php?type=post&id=$ROW[postid]' style='text-decoration: none; color: #405d9b;'>";
                            echo "点赞($ROW[likes])";
                            echo "</a> &nbsp";
                            echo "<span style='color: #999;'>$ROW[date]</span> &nbsp";

                            if ($ROW['has_image']) {
                                echo "<a href='image_view.php?id=$ROW[postid]' style='text-decoration: none; color: #405d9b;'>查看原图</a>";
                            }
                            $post = new Post();
                            
                            echo "</span>";
                            echo "<a id='info_$ROW[postid]' href='likes.php?type=post&id=$ROW[postid]' style='text-decoration: none;'>";
                            echo "<span style='color: #999;'>";
                            
                            $i_liked = false;
                            if (isset($_SESSION['project_rrmd100zsyff_userid'])) {
                            
                                $sql = "select likes from likes where type = 'post' && contentid = '$ROW[postid]' limit 1";
                                $DB = new Database();
                                $result = $DB -> read($sql);
                                                    
                                // check if I liked the post
                                if (is_array($result)) {
                                    $curlikes = json_decode($result[0]['likes'], true);
                                    $userids = array_column($curlikes, "userid");
                                    $i_liked = in_array($_SESSION['project_rrmd100zsyff_userid'], $userids);
                                }
                            }
                            
                            if ($ROW['likes'] == 1) {
                                echo "<br>";
                                if ($i_liked) {
                                    echo "我赞了该动态";
                                }
                                else {
                                    echo "1人赞了该动态";
                                }
                            }
                            else if ($ROW['likes'] > 0) {
                                echo "<br>";
                                if ($i_liked) {
                                    echo "我和其他" . ($ROW['likes'] - 1) . "人赞了该动态";
                                }
                                else {
                                    echo $ROW['likes'] . "人赞了该动态";
                                }
                            }
                            
                            echo "</span>";
                            echo "</a>";
                            echo "</div>";
                            echo "</div>";

                            // above should have been include("post.php");
                        }
                    ?>
                    <br style="clear: both;">
                    
                    <div style="border: solid thin #aaa; padding: 10px; background-color: white;">
                        <form method="post" enctype="multipart/form-data">
                            <?php
                                if (in_array($user_data['id'], get_rm_ids())) {
                                    echo "<textarea name='post' style='resize: none;' placeholder='肉肉米大人有何指示！（认真脸 (づ●─●)づ'></textarea>";
                                }
                                else {
                                    echo "<textarea name='post' style='resize: none;' placeholder='在此发布您的评论'></textarea>";
                                }
                            ?>
                            <!--<textarea name="post" style="resize: none;" placeholder="肉肉米大人有何指示！（认真脸 (づ●─●)づ"></textarea>-->
                            <input type="hidden" name="parent" value="<?php echo $ROW['postid'] ?>">
                            <input type="file" name="file">
                            <input id="post_button" type="submit" value="评论" style="cursor: pointer;"><br>
                        </form>
                    </div>
                    <br style="clear: both;"><br>
                    
                    <div style="background-color: #f6f6f6; border-radius: 10px;">
                        <?php

                            $comments = $Post -> get_comments($ROW['postid']);
                            if (is_array($comments)) {
                                foreach ($comments as $COMMENT) {
                                    $ROW_USER = $user -> get_user($COMMENT['userid']);
                                    include("comment.php");
                                }
                            }

                            // for pagination
                            // set basic variables
                            $limit = 10;
                            $DB = new Database();
                            $sql = "select count(*) from posts where parent = '$ROW[postid]'";
                            $comment_count = $DB -> read($sql)[0]['count(*)'];
                            // get current page number
                            $page_number = 1;
                            if (isset($_GET['page'])) {
                                $page_number = (int)$_GET['page'];
                            }
                            $offset = ($page_number - 1) * $limit;
                            $pg = pagination_link($page_number, $comment_count, $limit);
                            
                            echo "<br>";
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
                        <br style="clear: both;">
                    </div>

                </div>

            </div>
        </div>

    </div>

    <script type="text/javascript">

        function like_post(e) {

            e.preventDefault();
            var link = e.target.href;

            var data = {};
            data.link = link;
            data.action = "like_post";
            ajax_send(data, e.target);

        }

        function ajax_send(data, element) {

            var ajax = new XMLHttpRequest();

            ajax.addEventListener('readystatechange', function() {
                if (ajax.readyState == 4 && ajax.status == 200) {
                    response(ajax.responseText, element);
                }
            });

            ajax.open("post", "ajax.php", true);
            ajax.send(JSON.stringify(data));

        }

        function response(result, element) {

            if (result != "") {
                var obj = JSON.parse(result);

                if (typeof obj.action != "undefined") {

                    if (obj.action == "like_post") {
                        var likes = "点赞(" + parseInt(obj.likes) + ")";
                        element.innerHTML = likes;
                        var info_element = document.getElementById(obj.id);
                        info_element.innerHTML = obj.info;
                    }

                }
            }
        }

    </script>

</body>

</html>