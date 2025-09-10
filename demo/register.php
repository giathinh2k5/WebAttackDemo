<?php 
session_start(); 
$_SESSION['csrf_token'] = bin2hex(random_bytes(32));
if (!empty($_SESSION['input'])){
    $input = $_SESSION['input'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        form { max-width: 400px; padding: 20px; border: 1px solid #ccc; }
        label { display: block; margin-top: 10px; }
        input { width: 100%; padding: 8px; margin-top: 5px; }
        button { margin-top: 15px; padding: 10px; border: none; cursor: pointer; }
        .secure-btn { background-color: blue; color: white; }
        .vuln-btn { background-color: red; color: white; }
        div { background-color: red; color: white; padding: 5px; margin-top: 5px; }
    </style>
</head>
<body>
    <h2>Bank Registration</h2>
    <form id="registerForm" action="registration.php" method="POST">
        <label>Name</label>
        <input type="text" name="name" value="<?=$input['name'] ?? ''?>" required>
        <?php if (!empty($_SESSION['name_error'])) { echo "<div>".$_SESSION['name_error']."</div>"; } ?>

        <label>Password</label>
        <input type="password" name="password" value="<?=$input['password'] ?? ''?>" required>

        <label>Confirm Password</label>
        <input type="password" name="confirm_password" required>
        <?php if (!empty($_SESSION['pass_error'])) { echo "<div>".$_SESSION['pass_error']."</div>"; } ?>

        <input type="hidden" name="csrf_token" value="<?=$_SESSION['csrf_token']?>">

        <label>Starting Balance</label>
        <input type="number" name="bal" min="0" value="<?=$input['bal'] ?? ''?>" required>
        <?php if (!empty($_SESSION['bal_error'])) { echo "<div>".$_SESSION['bal_error']."</div>"; } ?>

        <button type="submit" class="secure-btn">
            Register (Secure)
        </button>

        <button type="submit" class="vuln-btn" onclick="document.getElementById('registerForm').action='vuln_registration.php';">
            Register (Vulnerable)
        </button>

        <a href='login.php'>Log in instead</a>
    </form>

    <?php 
        $_SESSION['bal_error'] = ''; 
        $_SESSION['name_error'] = ''; 
        $_SESSION['pass_error'] = ''; 
    ?>
</body>
</html>

