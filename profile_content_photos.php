<br>
<div style="min-height: 400px; width: 100%; background-color: white; text-align: center;">

    <?php

        $DB = new Database();

        $sql = "select image, postid from posts where has_image = 1 && userid = $user_data[userid] order by id desc limit 30";
        $images = $DB -> read($sql);
        $image_class = new Image();

        echo "<br><br><b>相册</b><hr style='width: 400px;'>";
        if (is_array($images)) {
            echo "<br>";
            foreach ($images as $image_row) {
                echo "<a href='image_view.php?id=$image_row[postid]'>";
                echo "<img src='" . $image_class -> get_thumb_post($image_row['image']) . "' style='width: 120px; height: 120px; object-fit: cover; margin: 10px;'>";
                echo "</a>";
            }
        }
        else {
            echo "<br>相册为空";
        }

    ?>

</div>