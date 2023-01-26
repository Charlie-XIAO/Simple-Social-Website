<div id="friends">
    <?php
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
    ?>
    <a href="profile.php?id=<?php echo $FRIEND_ROW['userid'] ?>&page=1" style="text-decoration: none; color: #405d9b;">
        <img id="friends_img" src="<?php echo $image ?>" style="border-radius: 10%;"><br>
        <?php
            echo $FRIEND_ROW['username'] . "<br>";
            $last_online = "从未登陆";
            if ($FRIEND_ROW['online'] > 0) {
                if (time() - $FRIEND_ROW['online'] < 60 * 2) {
                    echo "<span style='color: green; font-size: 12px; font-weight: normal;'>在线</span>";
                }
                else {
                    $time_class = new Time();
                    $timezone = "Asia/Hong_Kong";
                    $dt = new DateTime("now", new DateTimeZone($timezone));
                    $dt -> setTimestamp($FRIEND_ROW['online']);
                    $last_online = $time_class -> get_time($dt -> format("Y-m-d H:i:s"));
                    echo "<span style='color: #999; font-size: 12px; font-weight: normal;'>" . $last_online . "</span>";
                }
            }
        ?>
    </a>
</div>