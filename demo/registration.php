<?php
session_start();
require 'db.php';
$_SESSION['input'] = $_POST;

// Get form data
$name = htmlspecialchars($_POST['name'], ENT_QUOTES, 'UTF-8');
$password = htmlspecialchars($_POST['password'], ENT_QUOTES, 'UTF-8');
$confirm_password = htmlspecialchars($_POST['confirm_password'], ENT_QUOTES, 'UTF-8');
$bal = htmlspecialchars($_POST['bal'], ENT_QUOTES, 'UTF-8');

// Check input formatting
if (!preg_match("/^[a-zA-Z0-9_]+$/", $name)) {
    $_SESSION['name_error'] = "Tên chỉ được chứa chữ cái, số và dấu gạch dưới (_)";
    header("Location: register.php");
    exit;
}

// Check CSRF Token
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    die("CSRF token validation failed.");
}

// Check if username already exists
$query = "SELECT COUNT(*) FROM users WHERE username = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $name);
$stmt->execute();
$stmt->bind_result($count);
$stmt->fetch();
if ($count > 0) {
    $_SESSION['name_error'] = "Name already registered!";
    header("Location: register.php");
    exit;
}

// Validate passwords
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

$stmt->close();

// Insert into database
$query = "INSERT INTO users (id, username, password) VALUES (NULL, ?, ?)";
$stmt = $conn->prepare($query);
$stmt->bind_param("ss", $name, $password);
$stmt->execute();
$stmt->close();

$stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
$stmt->bind_param("s", $name);
$stmt->execute();
$stmt->store_result();
$stmt->bind_result($id);
$stmt->fetch();
$stmt->close();

$query = "INSERT INTO account_balances (id, user_id, balance) VALUES (NULL, ?, ?)";
$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $id, $bal);
$stmt->execute();
$stmt->close();
unset($_SESSION['input']);
echo "Registration successful! <a href='login.php'>Log in here</a>";

?>
