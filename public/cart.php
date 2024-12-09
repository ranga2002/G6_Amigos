<?php
session_start();
require_once "../utils/db_connection.php";
require_once "../classes/Laptop.php";
require_once "../classes/Cart.php";

$conn = getDBConnection();
$laptop = new Laptop($conn);
$cart = new Cart($conn);

$userId = $_SESSION['user_id'] ?? 0;

// Handle Remove Action
if (isset($_GET['action']) && $_GET['action'] === 'remove' && isset($_GET['id'])) {
    $laptopId = intval($_GET['id']);

    if ($userId) {
        // Remove item for logged-in user
        $cart->removeFromCart($userId, $laptopId);
    } else {
        // Remove item for non-logged-in user (session-based cart)
        if (isset($_SESSION['cart'][$laptopId])) {
            unset($_SESSION['cart'][$laptopId]);
        }
    }

    $_SESSION['success_message'] = "Item removed from cart.";
    header('Location: cart.php');
    exit();
}

// Fetch cart items
if ($userId) {
    // Fetch cart items for logged-in user from database
    $cartItems = $cart->getCartItems($userId);
} else {
    // Fetch cart items for non-logged-in user from session
    $cartItems = [];
    if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
        foreach ($_SESSION['cart'] as $laptopId => $quantity) {
            $laptopDetails = $laptop->getLaptopById($laptopId);
            if ($laptopDetails) {
                $laptopDetails['quantity'] = $quantity;
                $cartItems[] = $laptopDetails;
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Cart</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <?php include "../includes/header.php"; ?>
    <div class="container">
        <h1>Cart</h1>
        <?php if (isset($_SESSION['success_message'])) { ?>
            <div class="alert success">
                <?= htmlspecialchars($_SESSION['success_message']) ?>
                <?php unset($_SESSION['success_message']); ?>
            </div>
        <?php } ?>
        <?php if (empty($cartItems)) { ?>
            <p>Your cart is empty.</p>
        <?php } else { ?>
            <table>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Total</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($cartItems as $item) { ?>
                        <tr>
                            <td><?= htmlspecialchars($item['name']) ?></td>
                            <td>$<?= htmlspecialchars($item['price']) ?></td>
                            <td><?= htmlspecialchars($item['quantity']) ?></td>
                            <td>$<?= htmlspecialchars($item['price'] * $item['quantity']) ?></td>
                            <td>
                                <a href="cart.php?action=remove&id=<?= htmlspecialchars($item['laptop_id']) ?>">Remove</a>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
            <a href="<?= $userId ? 'checkout.php' : 'signup.php' ?>" class="btn">
                <?= $userId ? 'Proceed to Checkout' : 'Register to Checkout' ?>
            </a>
        <?php } ?>
    </div>
    <?php include "../includes/footer.php"; ?>
</body>
</html>