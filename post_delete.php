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
                $image_class = new Image();
                $image = $image_class -> get_thumb_profile($ROW_USER['profile_image']);
            }
        ?>
        <img src="<?php echo $image ?>" style="width: 75px; margin-right: 10px; border-radius: 10%;">
    </div>
    <div style="width: 100%;">
        <div style="font-weight: bold; color: #405d9b;"><?php echo htmlspecialchars($ROW_USER['username']) ?></div>
        <?php echo check_tags(htmlspecialchars($ROW['post'])) ?><br><br>
        <?php
            if (file_exists($ROW['image'])) {
                $post_image = $image_class -> get_thumb_post($ROW['image']);
                echo "<img src='$post_image' style='width: 80%;'>";
            }
        ?>
    </div>
</div>