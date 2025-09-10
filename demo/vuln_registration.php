<?php
session_start();
require 'db.php';

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$name = $_POST['name'];
$password = $_POST['password'];
$confirm_password = $_POST['confirm_password'];
$bal = $_POST['bal'];

// Check if username already exists
$query = "SELECT * FROM users WHERE username = '$name'";
$result = $conn->query($query);
if ($result->num_rows > 0) {
    $_SESSION['name_error'] = "Name already registered!";
    header("Location: register.php");
    exit;
}

// Validate password
if ($password !== $confirm_password) {
    $_SESSION['pass_error'] = "Passwords don't match!";
    header("Location: register.php");
    exit;
}

// Validate balance
if ($bal < 0) {
    $_SESSION['bal_error'] = "Invalid balance!";
    header("Location: register.php");
    exit;
}

// insert into database
$query = "INSERT INTO users (id, username, password) VALUES (NULL, '$name', '$password')";
$conn->query($query);

$user_id = $conn->insert_id;

$query = "INSERT INTO account_balances (user_id, balance) VALUES ($user_id, $bal)";
$conn->query($query);

unset($_SESSION['input']);

echo "Vulnerable Registration successful! <a href='login.php'>Log in here</a>";
?>
