<?php
session_start();
require 'db.php';
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT balance FROM account_balances WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->store_result();
$stmt->bind_result($balance);
$stmt->fetch();
$stmt->close();

$query = "SELECT id, username FROM users ORDER BY id ASC";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Home</title>
    <style>
        .vuln-btn { background-color: red; color: white; }

        table {
            width: 50%;
            border-collapse: collapse;
            margin-top: 20px;
            margin: auto;
        }

        th, td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: left;
        }

        th {
            background-color: #f4f4f4;
        }

        div {
            margin: auto;
            width: 50%;
        }
        body { font-family: Arial, sans-serif; margin: 20px; }
        label { display: block; margin-top: 10px; }
        button { margin-top: 15px; padding: 10px; background-color: blue; color: white; border: none; }
        form {
            margin: auto;
            background-color: beige;
            display: flex;
            flex-direction: column;
            width: 50%;
            align-items: center;
        }
        input{
            margin-top: 4px;
            margin-bottom: 10px;
        }
    </style>
</head>

<body>
    <h2 style="margin-left: 10%;">
        Welcome, <?=$_SESSION['username']?>!
    </h2>
    <h3 style="margin-left: 10%;"> 
        Id: <?=$_SESSION['user_id']?>
    </h3>
    <a style="margin-left: 10%;" href="logout.php?csrf_token=<?=$_SESSION['csrf_token']?>">Logout</a>
    <h2 style="text-align: center;">Transfer Funds</h2>
    <div>
        <h3>Current Balance:
            <?=$balance?>
        </h3>
    </div>
    <table>
        <tr>
            <th>ID</th>
            <th>Name</th>
        </tr>
        
        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['id']; ?></td>
                    <td><?php echo $row['username']; ?></td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="4">No users found.</td></tr>
        <?php endif; ?>
    </table>
    <form action="transact.php" method="get">
        <input type="hidden" name="csrf_token" value="<?=$_SESSION['csrf_token']?>">
        <label for="bId">Beneficiary ID:</label><input name="bId" type="text">
        <label for="amt">Amount to transact:</label><input type="text" name="amt">
        <button>Transact</button>
        <button type="submit" class="vuln-btn" onclick="document.getElementById('registerForm').action='vuln_transact.php';">
            Transact (Vulnerable)
        </button>
    </form>
</body>

</html>