<?php
    
    include("./classes/autoload.php");

    // check if user is logged in
    $login = new Login();
    $user_data = $login -> check_login($_SESSION['project_rrmd100zsyff_userid']);
    $USER = $user_data;

    // file upload
    if ($_SERVER['REQUEST_METHOD'] == "POST") {

        if (isset($_FILES['file']['name']) && $_FILES['file']['name'] != "") {
            
            if ($_FILES['file']['type'] == "image/jpeg") {
                
                $allowed_size = (1024 * 1024) * 5;
                if ($_FILES['file']['size'] < $allowed_size) {
                    
                    // file valid as profile image
                    $folder = "./uploads/" . $user_data['userid'] . "/";
                    if (!file_exists($folder)) {
                        mkdir($folder, 0777, true);
                    }

                    $image = new Image();
                    $filename = $folder . $image -> generate_filename(15) . ".jpg";
                    move_uploaded_file($_FILES['file']['tmp_name'], $filename);
                    
                    // check for mode
                    $change = "profile";
                    if (isset($_GET['change'])) {
                        $change = $_GET['change'];
                    }

                    if ($change == "cover") {
                        if (file_exists($user_data['cover_image'])) {
                            // if previous cover images need to be displayed somewhere, comment out the following line
                            unlink($user_data['cover_image']);
                        }
                        $image -> resize_image($filename, $filename, 1500, 1500);
                    }
                    else {
                        if (file_exists($user_data['profile_image'])) {
                            // if previous profile images need to be displayed somewhere, comment out the following line
                            unlink($user_data['profile_image']);
                        }
                        $image -> resize_image($filename, $filename, 1500, 1500);
                    }

                    if (file_exists($filename)) {
                        
                        $userid = $user_data['userid'];
                        
                        if ($change == "cover") {
                            $query = "update users set cover_image = '$filename' where userid = '$userid' limit 1";
                        }
                        else {
                            $query = "update users set profile_image = '$filename' where userid = '$userid' limit 1";
                        }

                        $DB = new Database();
                        $DB -> save($query);

                        header("Location: profile.php?page=1");
                        die;
                    }
                }
                else {
                    echo "<div style='text-align: center; font-size: 12px; color: white; background-color: rgb(59, 89, 152);'>";
                    echo "<br>发生以下错误:<br><br>";
                    echo "只允许5MB以下的文件";
                    echo "<br><br>";
                    echo "</div>";
                }
            }
            else {
                echo "<div style='text-align: center; font-size: 12px; color: white; background-color: rgb(59, 89, 152);'>";
                echo "<br>发生以下错误:<br><br>";
                echo "只允许JPEG类型的文件";
                echo "<br><br>";
                echo "</div>";
            }
        }
        else {
            echo "<div style='text-align: center; font-size: 12px; color: white; background-color: rgb(59, 89, 152);'>";
            echo "<br>发生以下错误:<br><br>";
            echo "无效文件";
            echo "<br><br>";
            echo "</div>";
        }
    }

?>

<!DOCTYPE html>
<html>

<head>
    <title>更换图片 | 肉肉米的100种食用方法</title>
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

            <!-- posts area -->
            <div style="min-height: 400px; flex: 2.5; padding: 20px; padding-right: 0;">
                <form method="post" enctype="multipart/form-data">
                    <div style="border: solid thin #aaa; padding: 10px; background-color: white;">
                        <input type="file" name="file">
                        <input id="post_button" type="submit" value="更换"><br>
                        <div style="text-align: center;"><br><br>
                            <?php
                                if (isset($_GET['change']) && $_GET['change'] == "cover") {
                                    $change = "cover";
                                    echo "<img src='$user_data[cover_image]' style='max-width: 500px;'>";
                                }
                                else {
                                    echo "<img src='$user_data[profile_image]' style='max-width: 500px;'>";
                                }
                            ?>
                        </div>
                    </div>
                </form>

            </div>
        </div>

    </div>

</body>

</html>