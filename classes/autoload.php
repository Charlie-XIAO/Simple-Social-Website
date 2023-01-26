<?php

session_start();

include("./classes/functions.php");
include("./classes/connect.php");
include("./classes/login.php");
include("./classes/user.php");
include("./classes/post.php");
include("./classes/image.php");
include("./classes /profile.php");
include("./classes/settings.php");
include("./classes/time.php");

if (isset($_SESSION['project_rrmd100zsyff_userid'])) {
    set_online($_SESSION['project_rrmd100zsyff_userid']);
}