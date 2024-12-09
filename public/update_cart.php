<?php
session_start();
require_once "../utils/db_connection.php";
require_once "../classes/Cart.php";

$conn = getDBConnection();
$cart = new Cart($conn);

$userId = $_SESSION['user_id'] ?? 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $laptopId = $_POST['laptop_id'] ?? null;
    $quantityDelta = isset($_POST['quantity']) ? intval($_POST['quantity']) : 1; // Default to 1

    // Validate laptop ID exists in the database
    if (!$laptopId) {
        $_SESSION['error_message'] = "Invalid laptop ID.";
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit();
    }

    $stmt = $conn->prepare("SELECT COUNT(*) FROM Laptops WHERE laptop_id = ?");
    $stmt->execute([$laptopId]);
    if ($stmt->fetchColumn() == 0) {
        $_SESSION['error_message'] = "Invalid laptop ID.";
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit();
    }

    // Handle logged-in users
    if ($userId) {
        $cart->updateCartQuantity($userId, $laptopId, $quantityDelta);
        $_SESSION['success_message'] = "Laptop added to cart successfully!";
    } else {
        // Handle non-logged-in users using session cart
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }

        if (isset($_SESSION['cart'][$laptopId])) {
            $_SESSION['cart'][$laptopId] += $quantityDelta;

            // Remove item if quantity goes to zero or below
            if ($_SESSION['cart'][$laptopId] <= 0) {
                unset($_SESSION['cart'][$laptopId]);
            }
        } else if ($quantityDelta > 0) {
            $_SESSION['cart'][$laptopId] = $quantityDelta;
        }

        $_SESSION['success_message'] = "Laptop added to cart successfully!";
    }

    header('Location: ' . $_SERVER['HTTP_REFERER']); // Redirect back to the previous page
    exit();
} else {
    $_SESSION['error_message'] = "Invalid request method.";
    header('Location: ' . $_SERVER['HTTP_REFERER']);
    exit();
}
?>