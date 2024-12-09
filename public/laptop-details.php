<?php
require_once "../utils/db_connection.php";
require_once "../classes/Laptop.php";

$conn = getDBConnection();
$laptop = new Laptop($conn);

if (isset($_GET['id'])) {
    $laptopId = intval($_GET['id']); // Ensure laptop ID is an integer
    $laptopDetails = $laptop->getLaptopById($laptopId);
    if (!$laptopDetails) {
        echo "Laptop not found!";
        exit;
    }
} else {
    echo "Invalid laptop ID!";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title><?= htmlspecialchars($laptopDetails['name']) ?></title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <?php include "../includes/header.php"; ?>

    <div class="container">
        <div class="laptop-details">
            <div class="laptop-image">
                <img src="../<?= htmlspecialchars($laptopDetails['image_path']) ?>" alt="<?= htmlspecialchars($laptopDetails['name']) ?>">
            </div>
            <div class="laptop-info">
                <h1><?= htmlspecialchars($laptopDetails['name']) ?></h1>
                <p><?= htmlspecialchars($laptopDetails['description']) ?></p>
                <p><strong>Brand:</strong> <?= htmlspecialchars($laptopDetails['brand']) ?></p>
                <p><strong>Processor:</strong> <?= htmlspecialchars($laptopDetails['processor']) ?></p>
                <p><strong>RAM:</strong> <?= htmlspecialchars($laptopDetails['RAM']) ?> GB</p>
                <p><strong>Storage:</strong> <?= htmlspecialchars($laptopDetails['storage']) ?></p>
                <p><strong>Price:</strong> $<?= htmlspecialchars($laptopDetails['price']) ?></p>
                <p><strong>Stock:</strong> <?= htmlspecialchars($laptopDetails['stock']) ?></p>
                
                <!-- Add to Cart Form -->
                <form method="POST" action="update_cart.php" class="add-to-cart-form">
                    <input type="hidden" name="laptop_id" value="<?= $laptopDetails['laptop_id'] ?>">
                    <input type="hidden" name="quantity" value="1"> <!-- Default quantity -->
                    <button type="submit" class="add-to-cart-btn">Add to Cart</button>
                </form>
            </div>
        </div>
    </div>

    <?php include "../includes/footer.php"; ?>
</body>
</html>