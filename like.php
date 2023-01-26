<?php

include("./classes/autoload.php");

// check if user is logged in
$login = new Login();
$user_data = $login -> check_login($_SESSION['project_rrmd100zsyff_userid']);

if (isset($_SERVER['HTTP_REFERER'])) {
    $return_to = $_SERVER['HTTP_REFERER'];
}
else {
    $return_to = "profile.php?page=1";
}

if (isset($_GET['type']) && isset($_GET['id'])) {

    if (is_numeric($_GET['id'])) {

        $post = new Post();
        $user_class = new User();
        
        $post -> like_post($_GET['id'], $_GET['type'], $_SESSION['project_rrmd100zsyff_userid']);
        
        if ($_GET['type'] == "user") {
            $user_class -> follow_user($_GET['id'], $_GET['type'], $_SESSION['project_rrmd100zsyff_userid']);
        }
    }
}

header("Location: " . $return_to);
die;