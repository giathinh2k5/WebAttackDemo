<?php
session_start();
if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
if (!empty($_SESSION['input'])){
    $input = $_SESSION['input'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        form { max-width: 400px; padding: 20px; border: 1px solid #ccc; }
        label { display: block; margin-top: 10px; }
        input, select { width: 100%; padding: 8px; margin-top: 5px; }
        button { margin-top: 15px; padding: 10px; color: white; border: none; }
        .real-login { background-color: blue; }
        .fake-login { background-color: red; }
        div { background-color: red; color: white; }
    </style>
</head>
<body>
    <h2>Login</h2>
    <form action="authenticate.php" method="post">
        <input type="hidden" name="csrf_token" value="<?=$_SESSION['csrf_token']?>">

        <label>Username:</label>
        <input type="text" name="username" value="<?=$input['username'] ?? ''?>" required>
        <?php if (!empty($_SESSION['name_error'])) { echo "<div>".$_SESSION['name_error']."</div>"; } ?>
        <br>

        <label>Password:</label>
        <input type="password" name="password" value="<?=$input['password'] ?? ''?>" required>
        <?php if (!empty($_SESSION['pass_error'])) { echo "<div>".$_SESSION['pass_error']."</div>"; } ?>
        <br>

        <!-- Real login button -->
        <button type="submit" class="real-login">Login</button>

        <!-- Fake vulnerable login button -->
        <button type="submit" formaction="vuln_authenticate.php" class="fake-login">Login (Vulnerable)</button>

        <br><br>
        <a href='register.php'>Register for an account</a>
    </form>
    
    <?php 
        $_SESSION['bal_error'] = ''; 
        $_SESSION['name_error'] = ''; 
        $_SESSION['pass_error'] = ''; 
    ?>
</body>
</html>
