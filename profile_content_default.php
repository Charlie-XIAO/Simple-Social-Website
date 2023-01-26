<div style="display: flex;">

    <!-- friends area -->
    <div style="min-height: 400px; flex: 1;">
        <div id="friends_bar">
            <div style="margin-left: 10px; margin-top: 5px; margin-bottom: 5px;">关注列表</div>
            <?php
                if ($friends) {
                    foreach ($friends as $friend) {
                        $FRIEND_ROW = $user -> get_user($friend['userid']);
                        include("user.php");
                    }
                }
            ?>
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

                if ($posts) {
                    foreach ($posts as $ROW) {
                        $user = new User();
                        $ROW_USER = $user -> get_user($ROW['userid']);
                        include("post.php");
                    }
                }

                // for pagination
                // set basic variables
                $limit = 10;
                $DB = new Database();
                $sql = "select count(*) from posts where parent = 0 && userid = '$user_data[userid]'";
                $post_count = $DB -> read($sql)[0]['count(*)'];
                // get current page number
                $page_number = 1;
                if (isset($_GET['page'])) {
                    $page_number = (int)$_GET['page'];
                }
                $offset = ($page_number - 1) * $limit;
                $pg = pagination_link($page_number, $post_count, $limit);
                
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