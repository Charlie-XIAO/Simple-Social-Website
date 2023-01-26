<?php

    session_start();

    include("./classes/connect.php");
    include("./classes/login.php");

    $username = "";
    $password = "";
    
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $login = new Login();
        $result = $login -> evaluate($_POST);

        if ($result != "") {
            echo "<div style='text-align: center; font-size: 12px; color: white; background-color: rgb(59, 89, 152);'>";
            echo "<br>请修改以下信息:<br><br>";
            echo $result;
            echo "<br>";
            echo "</div>";
        }
        else {
            header("Location: profile.php?page=1");
            die;
        }

        $username = $_POST['username'];
        $password = $_POST['password'];
    }

?>

<html>

<head>
    <title>肉肉米的100种食用方法 | 登录</title>
</head>

<style>
    #bar {
        height: 80px;
        background-color: rgb(59, 89, 152);
        color: white;
        padding: 4px;
    }

    #signup_button {
        background-color: #42b72a;
        width: 70px;
        text-align: center;
        padding: 4px;
        border-radius: 4px;
        float: right;
    }

    #bar2 {
        background-color: white;
        width: 800px;
        height: 240px;
        margin: auto;
        margin-top: 50px;
        padding: 10px;
        padding-top: 50px;
        text-align: center;
        font-weight: bold;
    }

    #text {
        height: 40px;
        width: 300px;
        border-radius: 4px;
        border: solid 1px #aaa;
        padding: 4px;
        font-size: 14px;
    }

    #button {
        width: 300px;
        height: 40px;
        border-radius: 4px;
        border: none;
        background-color: rgb(59, 89, 152);
        color: white;
    }

</style>

<body style="font-family: tahoma; background-color: #e9ebee;">

    <div id="bar">
        <div style="font-size: 28px;">肉肉米的100种食用方法</div>
        <a href="signup.php" style="text-decoration: none; color: white;"><div id="signup_button">注册</div></a>
    </div>

    <div id="bar2">
        <form method="post">
            登录肉肉米护理观察站<br><br>
            <input name="username" value="<?php echo $username ?>" type="text" id="text" placeholder="用户名"><br><br>
            <input name="password" value="<?php echo $password ?>" type="password" id="text" placeholder="密码"><br><br>
            <input type="submit" id="button" value="登录">
        </form>
    </div>

</body>

</html>