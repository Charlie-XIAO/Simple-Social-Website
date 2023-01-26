<?php

session_start();

unset($_SESSION['project_rrmd100zsyff_userid']);
header("Location: login.php");
die;