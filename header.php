<?php
    $corner_image = "./images/placeholder_male.jpg";
    if (isset($USER)) {
        $image_class = new Image();
        if ($USER['gender'] == "男") {
            $corner_image = "./images/placeholder_male.jpg";
        }
        else {
            $corner_image = "./images/placeholder_female.jpg";
        }
        if (file_exists($USER['profile_image'])) {
            $corner_image = $image_class -> get_thumb_profile($USER['profile_image']);
        }
    }
?>

<div id="blue_bar">
    <form method="get" action="search.php">
        <div style="width: 800px; margin: auto; font-size: 24px;">
            <a style="color: white; text-decoration: none;" href="index.php?page=1">肉肉米的100种食用方法</a> &nbsp
            <input type="text" id="search_box" name="find" placeholder="查找姓名或用户名">
            <a href="profile.php?page=1"><img src="<?php echo $corner_image ?>" style="width: 40px; border-radius: 50%; float: right; margin: 5px;"></a>
            <a href="logout.php"><span style="font-size: 12px; float: right; margin: 15px; color: white;">登出</span></a>
            <a href="notifications.php?page=1" style="text-decoration: none; font-size: 12px; color: white; position: relative; margin-right: 25px;">
                <img src="./images/notif.svg" style="width: 25px; float: right; margin-top: 12.5px;">
                <?php
                    $notif_count = count_notifications();
                    if ($notif_count > 0) {
                        echo "<span style='position: absolute; left: 40px; top: -12px; font-size: 13px; background: hsl(0, 80%, 60%); color: white; border-radius: 15%; padding-left: 3px; padding-right: 3px;'>$notif_count</span>";
                    }
                ?>
            </a>
        </div>
    </form>
</div>