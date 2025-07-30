<?php
session_start();
include 'fungsi.php';

logout();
header("Location: login.php");
exit;
?>