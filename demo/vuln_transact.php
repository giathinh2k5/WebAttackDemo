<?php
session_start();
require 'db.php'; 

if (!isset($_GET['bId']) || !isset($_GET['amt'])) {
    die("Invalid transaction request. <a href='index.php'>Return</a>");
}

$beneficiary_id = htmlspecialchars($_GET['bId'], ENT_QUOTES, 'UTF-8');
$amount = htmlspecialchars($_GET['amt'], ENT_QUOTES, 'UTF-8');

if (!is_numeric($beneficiary_id) || !is_numeric($amount) || $amount <= 0) {
    die("Invalid input. Please enter valid numbers. <a href='index.php'>Return</a>");
}

if (!isset($_SESSION['user_id'])) {
    die("You must be logged in to perform transactions. <a href='index.php'>Return</a>");
}
$user_id = $_SESSION['user_id'];

$conn->begin_transaction();

try {
    $stmt = $conn->prepare("SELECT balance FROM account_balances WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($balance);
    $stmt->fetch();

    if ($stmt->num_rows == 0) {
        throw new Exception("User not found.");
    }

    if ($balance < $amount) {
        throw new Exception("Insufficient balance.");
    }
    $stmt->close();

    $stmt = $conn->prepare("UPDATE account_balances SET balance = balance - ? WHERE user_id = ?");
    $stmt->bind_param("di", $amount, $user_id);
    $stmt->execute();
    if ($stmt->affected_rows == 0) {
        throw new Exception("Failed to deduct balance.");
    }

    $stmt = $conn->prepare("UPDATE account_balances SET balance = balance + ? WHERE user_id = ?");
    $stmt->bind_param("di", $amount, $beneficiary_id);
    $stmt->execute();
    if ($stmt->affected_rows == 0) {
        throw new Exception("Failed to credit beneficiary.");
    }

    $conn->commit();
    $conn->commit();
    header("Location: index.php");
    exit();

} catch (Exception $e) {
    $conn->rollback(); 
    echo "Transaction failed: " . $e->getMessage();
    echo "<a href='index.php'>Return</a>";
}

$conn->close();
?>
