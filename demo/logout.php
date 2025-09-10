<?php
session_start();

if (!isset($_GET['csrf_token']) || $_GET['csrf_token'] !== $_SESSION['csrf_token']) {
    die("CSRF validation failed.");
}

session_unset();
session_destroy();
header("Location: login.php");
exit;
?>