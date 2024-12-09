<?php
session_start();
if (!isset($_GET['invoice'])) {
    header('Location: index.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Success</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <?php include "../includes/header.php"; ?>
    <div class="container">
        <h1>Order Successful!</h1>
        <p>Thank you for your purchase. You can download your invoice below:</p>
        <a href="<?= htmlspecialchars($_GET['invoice']) ?>" target="_blank" class="btn">Download Invoice</a>
        <a href="index.php" class="btn">Back to Home</a>
    </div>
    <?php include "../includes/footer.php"; ?>
</body>
</html>