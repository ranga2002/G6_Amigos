<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<header>
    <div class="navbar">
        <div class="logo">
            <a href="index.php">Wizard's Store</a>
        </div>
        <nav>
            <a href="index.php">Home</a>
            <a href="laptops.php">Laptops</a>
            <a href="cart.php">Cart</a>
            <a href="contact.php">Contact Us</a>
            <?php if (isset($_SESSION['user_id'])) { ?>
                <a href="profile.php">Welcome, <?= htmlspecialchars($_SESSION['username']); ?></a>
                <a href="logout.php">Logout</a>
            <?php } else { ?>
                <a href="login.php">Login</a>
                <a href="signup.php">Signup</a>
            <?php } ?>
        </nav>
    </div>
</header>
