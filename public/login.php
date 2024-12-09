<?php
session_start();
require_once "../utils/db_connection.php";
require_once "../classes/User.php";
require_once "../classes/Cart.php";

$conn = getDBConnection();
$user = new User($conn);
$cart = new Cart($conn);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    $authenticatedUser = $user->login($email, $password);

    if ($authenticatedUser) {
        $_SESSION['user_id'] = $authenticatedUser['user_id'];
        $_SESSION['username'] = $authenticatedUser['username'];
    
        // Merge session cart with database cart
        if (isset($_SESSION['cart'])) {
            foreach ($_SESSION['cart'] as $laptopId => $quantity) {
                $cart->addToCart($authenticatedUser['user_id'], $laptopId, $quantity);
            }
            unset($_SESSION['cart']); // Clear session cart after merging
        }
    
        $_SESSION['success_message'] = "Login successful! Welcome, " . $authenticatedUser['username'] . ".";
        header('Location: index.php');
        exit();
    } else {
        $_SESSION['error_message'] = "Invalid email or password.";
        header('Location: login.php');
        exit();
    }    
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Login</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <?php include "../includes/header.php"; ?>

    <div class="container">
        <h1>Login</h1>
        <?php if (isset($_SESSION['error_message'])) { ?>
            <div class="alert error">
                <?= $_SESSION['error_message'] ?>
                <?php unset($_SESSION['error_message']); ?>
            </div>
        <?php } ?>
        <form method="POST" action="login.php">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" required>
            
            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>
            
            <button type="submit">Login</button>
        </form>
        <p>Don't have an account? <a href="signup.php">Sign up here</a>.</p>
    </div>

    <?php include "../includes/footer.php"; ?>
</body>
</html>