<?php
session_start();
require 'db.php'; 

$_SESSION['input'] = $_POST;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    //NO PREPARED STATEMENT
    $sql = "SELECT id FROM users WHERE username = '$username' AND password = '$password'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $_SESSION['user_id'] = $row['id'];
        $_SESSION['username'] = $username;

        header("Location: index.php");
        exit();
    } else {
        $_SESSION['name_error'] = "Invalid username or password!";
        header("Location: login.php");
        exit();
    }
}

$conn->close();
?>
