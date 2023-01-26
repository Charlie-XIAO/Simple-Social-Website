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
        <div style="font-weight: bold; color: #405d9b;"><?php echo "<a href='profile.php?id=$ROW[userid]&page=1' style='text-decoration: none; color: #405d9b;'>" . htmlspecialchars($ROW_USER['username']) . "</a>" ?></div>
        <?php echo check_tags($ROW['post']) ?><br><br>
        <?php
            if (file_exists($ROW['image'])) {
                $post_image = $image_class -> get_thumb_post($ROW['image']);
                echo "<img src='$post_image' style='width: 80%;'>";
            }
        ?>
        <br><br>
        <a onclick="like_post(event)" href="like.php?type=post&id=<?php echo $ROW['postid'] ?>" style="text-decoration: none; color: #405d9b;">
            点赞(<?php echo $ROW['likes'] ?>)
        </a> &nbsp
        <a href="single_post.php?id=<?php echo $ROW['postid'] ?>&page=1" style="text-decoration: none; color: #405d9b;">
            评论(<?php echo $ROW['comments'] ?>)
        </a> &nbsp
        <span style="color: #999;"><?php echo $ROW['date'] ?></span> &nbsp
        <!-- alternatively count time difference, need to change in autoload.php to include ./classes/time.php -->
        <!-- ?php $Time = new Time(); echo $Time -> get_time($ROW['date']); ? -->
        <?php
            if ($ROW['has_image']) {
                echo "<a href='image_view.php?id=$ROW[postid]' style='text-decoration: none; color: #405d9b;'>查看原图</a>";
            }
        ?>
        <span style="color: #999; float: right;">
            <?php
                $post = new Post();
                if ($post -> own_post($ROW['postid'], $_SESSION['project_rrmd100zsyff_userid'])) {
                    echo "<a href='edit.php?id=$ROW[postid]' style='text-decoration: none; color: #405d9b;'>编辑</a> &nbsp";
                    echo "<a href='delete.php?id=$ROW[postid]' style='text-decoration: none; color: #405d9b;'>删除</a> &nbsp";
                }
            ?>
        </span>
        <a id="info_<?php echo $ROW['postid'] ?>" href="likes.php?type=post&id=<?php echo $ROW['postid'] ?>" style="text-decoration: none;">
            <span style="color: #999;">
                <?php

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

                ?>
            </span>
        </a>
    </div>
</div>

<!--

            /* readyState
               0 - [uninitialized]   send() method is not called yet
               1 - [loading]         send() method is called, request is being sent
               2 - [load complete]   send() method is completed, all reponses are received
               3 - [interation]      responses are being analyzed
               4 - [complete]        responses are analyzed and results can be called by client */

            /* status
               1xx - [Informational]       【承受的申请正在解决】
               2xx - [Success]             【申请失常处理完毕】
               200 - <OK>                  示意从客户端发送给服务器的申请被失常解决并返回
               204 - <No Content>          示意客户端发送给客户端的申请失去了生理解决 但在返回的响应报文中不含实体的主体局部（没有资源能够返回）
               206 - <Patial Content>      示意客户端进行了范畴申请，并且服务器胜利执行了这部分的GET申请，响应报文中蕴含由Content-Range指定范畴的试题内容
               3xx - [Redirection]         【需要进行附加操作以实现申请】
               301 - <Moved Permanently>   永久性重定向，示意申请的资源被调配了新的URL，之后应应用更改的URL
               302 - <Found>               临时性重定向，示意申请的资源被调配了新的URL，希望本次拜访应用新的URL
               303 - <See Other>           示意申请的资源被调配了新的URL，应应用GET办法定向获取申请的资源
               304 - <Not Modified>        示意客户端发送附带条件的申请时，服务器端容许拜访资源，然而申请为满足条件的状况下返回该状态码
               307 - <Temporary Redirect>  长期重定向，与303有着雷同的含义，307会遵循浏览器规范不会从POST变成GET（不同浏览器可能会呈现不同的状况）
               4xx - [Client Error]        【客户端申请出错，服务器无奈解决申请】
               400 - <Bad Request>         示意申请报文中存在语法错误
               401 - <Unauthorized>        未经许可，需要通过HTTP认证
               403 - <Forbidden>           服务器回绝该次访问（摆放权限呈现问题）
               404 - <Not Found>           示意服务器上无奈找到申请的资源，除此之外，也能够在服务器拒绝请求但不想给回绝起因时使用
               5xx - [Server Error]        【服务器解决申请出错】
               500 - <Inter Server Error>  示意服务器在执行申请时产生了谬误，也有可能是web利用存在的bug或某些长期的谬误时
               503 - <Server Unavailable>  示意服务器临时处于超负载或正在进行停机保护，无奈解决申请 */

-->

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