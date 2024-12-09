<?php
session_start();
require_once "../utils/db_connection.php";
require_once "../classes/User.php";

$conn = getDBConnection();
$user = new User($conn);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $retypePassword = trim($_POST['retype_password']);

    // Server-side validation
    if ($password !== $retypePassword) {
        $_SESSION['error_message'] = "Passwords do not match.";
        header('Location: signup.php');
        exit();
    }

    if (!preg_match('/^(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/', $password)) {
        $_SESSION['error_message'] = "Password must be at least 8 characters, include at least one uppercase letter, one number, and one special character.";
        header('Location: signup.php');
        exit();
    }

    try {
        $user->register($username, $email, $password);
        $_SESSION['success_message'] = "Signup successful! You can now log in.";
        header('Location: login.php');
        exit();
    } catch (PDOException $e) {
        if ($e->getCode() == 23000) {
            $_SESSION['error_message'] = "Username or email already exists.";
        } else {
            $_SESSION['error_message'] = "Error: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Signup</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <?php include "../includes/header.php"; ?>

    <div class="container">
        <h1>Signup</h1>
        <?php if (isset($_SESSION['error_message'])) { ?>
            <div class="alert error">
                <?= $_SESSION['error_message'] ?>
                <?php unset($_SESSION['error_message']); ?>
            </div>
        <?php } ?>
        <form method="POST" action="signup.php" id="signup-form">
            <label for="username">Username</label>
            <input type="text" id="username" name="username" required>
            
            <label for="email">Email</label>
            <input type="email" id="email" name="email" required>
            
            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>
            <small>Password must be at least 8 characters long, contain an uppercase letter, a number, and a special character.</small>
            
            <label for="retype_password">Retype Password</label>
            <input type="password" id="retype_password" name="retype_password" required>
            
            <button type="submit">Signup</button>
        </form>
        <p>Already have an account? <a href="login.php">Log in here</a>.</p>
    </div>

    <?php include "../includes/footer.php"; ?>

    <script>
        document.getElementById('signup-form').addEventListener('submit', function (e) {
            const password = document.getElementById('password').value;
            const retypePassword = document.getElementById('retype_password').value;

            // Password validation regex
            const passwordRegex = /^(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/;

            if (!passwordRegex.test(password)) {
                alert("Password must be at least 8 characters long, contain an uppercase letter, a number, and a special character.");
                e.preventDefault();
                return false;
            }

            if (password !== retypePassword) {
                alert("Passwords do not match.");
                e.preventDefault();
                return false;
            }
        });
    </script>
</body>
</html>