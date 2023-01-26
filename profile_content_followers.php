<br>
<div style="min-height: 400px; width: 100%; background-color: white; text-align: center;">

    <?php

        $post_class = new Post();
        $followers = $post_class -> get_likes($user_data['userid'], "user");

        $image_class = new Image();
        $user_class = new User();

        echo "<br><br><b>粉丝</b><hr style='width: 400px;'>";
        if (is_array($followers)) {

            foreach ($followers as $follower) {
                $FRIEND_ROW = $user_class -> get_user($follower['userid']);
                
                // following is an alternative for user.php
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
                
                echo "<div style='font-size: 12px; font-weight: bold; color: #405d9b; display: inline-block;'>";
                echo "<a href='profile.php?id=$FRIEND_ROW[userid]&page=1' style='text-decoration: none; color: #405d9b;'>";
                echo "<img src='$image' style='width: 100px; height: 100px; border-radius: 10%; margin-top: 10px; margin-left: 10px; margin-right: 10px;'><br>";
                echo $FRIEND_ROW['username'];
                echo "</a>";
                echo "</div>";
                // above is an alternative for user.php
            }
        }
        else {
            echo "<br>暂无粉丝";
        }

    ?>

</div>