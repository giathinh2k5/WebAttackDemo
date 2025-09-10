<?php
session_start();
require 'db.php'; 
$_SESSION['input']=$_POST;
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("CSRF token validation failed.");
    }
    
    $username = htmlspecialchars($_POST['username'], ENT_QUOTES, 'UTF-8');
    $password = htmlspecialchars($_POST['password'], ENT_QUOTES, 'UTF-8');

    $stmt = $conn->prepare("SELECT id, password FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($id, $db_password);
    $stmt->fetch();

    if ($stmt->num_rows < 1){
        $_SESSION['name_error'] = "Invalid user!";
        header("Location: login.php");
        exit;
    } else if ($password !== $db_password) {
        $_SESSION['pass_error'] = "Password is incorrect!";
        header("Location: login.php");
        exit;
    }
    $_SESSION['user_id'] = $id;  
    $_SESSION['username'] = $username;
    header("Location: index.php"); 
    exit;
    unset($_SESSION['input']);
    $stmt->close();
}
$conn->close();
?>