<div id="post">
    <div>
        <?php
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
        ?>
        <img src="<?php echo $image ?>" style="width: 75px; margin-right: 10px; border-radius: 10%;">
    </div>
    <div style="width: 100%;">
        <div style="font-weight: bold; color: #405d9b;"><?php echo "<a href='profile.php?id=$COMMENT[userid]&page=1' style='text-decoration: none; color: #405d9b;'>" . htmlspecialchars($ROW_USER['username']) . "</a>" ?></div>
        <?php echo check_tags(htmlspecialchars($COMMENT['post'])) ?><br><br>
        <?php
            if (file_exists($COMMENT['image'])) {
                $post_image = $image_class -> get_thumb_post($COMMENT['image']);
                echo "<img src='$post_image' style='width: 80%;'>";
            }
        ?>
        <br><br>
        <a onclick="like_post(event)" href="like.php?type=post&id=<?php echo $COMMENT['postid'] ?>" style="text-decoration: none; color: #405d9b;">
            点赞(<?php echo $COMMENT['likes'] ?>)
        </a> &nbsp
        <span style="color: #999;"><?php echo $COMMENT['date'] ?></span> &nbsp
        <?php
            if ($COMMENT['has_image']) {
                echo "<a href='image_view.php?id=$COMMENT[postid]' style='text-decoration: none; color: #405d9b;'>查看原图</a>";
            }
        ?>
        <span style="color: #999; float: right;">
            <?php
                $post = new Post();
                if ($post -> own_post($COMMENT['postid'], $_SESSION['project_rrmd100zsyff_userid'])) {
                    echo "<a href='edit.php?id=$COMMENT[postid]' style='text-decoration: none; color: #405d9b;'>编辑</a> &nbsp";
                }
                if (i_own_content($COMMENT)) {
                    echo "<a href='delete.php?id=$COMMENT[postid]' style='text-decoration: none; color: #405d9b;'>删除</a> &nbsp";
                }
            ?>
        </span>
        <a id="info_<?php echo $COMMENT['postid'] ?>" href="likes.php?type=post&id=<?php echo $COMMENT['postid'] ?>" style="text-decoration: none;">
            <span style="color: #999;">
                <?php

                    $i_liked = false;
                    if (isset($_SESSION['project_rrmd100zsyff_userid'])) {

                        $sql = "select likes from likes where type = 'post' && contentid = '$COMMENT[postid]' limit 1";
                        $DB = new Database();
                        $result = $DB -> read($sql);
                        
                        // check if I liked the post
                        if (is_array($result)) {
                            $curlikes = json_decode($result[0]['likes'], true);
                            $userids = array_column($curlikes, "userid");
                            $i_liked = in_array($_SESSION['project_rrmd100zsyff_userid'], $userids);
                        }
                    }

                    if ($COMMENT['likes'] == 1) {
                        echo "<br>";
                        if ($i_liked) {
                            echo "我赞了该评论";
                        }
                        else {
                            echo "1人赞了该评论";
                        }
                    }
                    else if ($COMMENT['likes'] > 0) {
                        echo "<br>";
                        if ($i_liked) {
                            echo "我和其他" . ($COMMENT['likes'] - 1) . "人赞了该评论";
                        }
                        else {
                            echo $COMMENT['likes'] . "人赞了该评论";
                        }
                    }

                ?>
            </span>
        </a>
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