<?php
require_once "../utils/db_connection.php";
require_once "../classes/Laptop.php";

$conn = getDBConnection();
$laptop = new Laptop($conn);

// Handle filters
$filters = [];
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $brand = $_GET['brand'] ?? null;
    $ram = $_GET['ram'] ?? null;
    $storage = $_GET['storage'] ?? null;
    $minPrice = $_GET['min_price'] ?? null;
    $maxPrice = $_GET['max_price'] ?? null;

    if ($brand) $filters['brand'] = $brand;
    if ($ram) $filters['ram'] = $ram;
    if ($storage) $filters['storage'] = $storage;
    if ($minPrice) $filters['min_price'] = $minPrice;
    if ($maxPrice) $filters['max_price'] = $maxPrice;
}

// Fetch laptops with applied filters
$laptops = $laptop->getAllLaptops($filters);

// Fetch dynamic filter options
$brands = $conn->query("SELECT DISTINCT brand FROM Laptops")->fetchAll();
$rams = $conn->query("SELECT DISTINCT RAM FROM Laptops ORDER BY RAM ASC")->fetchAll();
$storages = $conn->query("SELECT DISTINCT storage FROM Laptops ORDER BY storage ASC")->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>All Laptops</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <?php include "../includes/header.php"; ?>

    <div class="container">
        <h1>All Laptops</h1>

        <!-- Display Success or Error Message -->
        <?php
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (isset($_SESSION['success_message'])) {
            echo '<div class="alert success">' . htmlspecialchars($_SESSION['success_message']) . '</div>';
            unset($_SESSION['success_message']);
        }

        if (isset($_SESSION['error_message'])) {
            echo '<div class="alert error">' . htmlspecialchars($_SESSION['error_message']) . '</div>';
            unset($_SESSION['error_message']);
        }
        ?>

        <!-- Filters Section -->
        <form method="GET" class="filter-form">
            <div class="filter-row">
                <select name="brand">
                    <option value="">Brand</option>
                    <?php foreach ($brands as $brand) { ?>
                        <option value="<?= $brand['brand'] ?>" <?= isset($filters['brand']) && $filters['brand'] === $brand['brand'] ? 'selected' : '' ?>>
                            <?= $brand['brand'] ?>
                        </option>
                    <?php } ?>
                </select>
                <select name="ram">
                    <option value="">RAM</option>
                    <?php foreach ($rams as $ram) { ?>
                        <option value="<?= $ram['RAM'] ?>" <?= isset($filters['ram']) && $filters['ram'] === $ram['RAM'] ? 'selected' : '' ?>>
                            <?= $ram['RAM'] ?> GB
                        </option>
                    <?php } ?>
                </select>
                <select name="storage">
                    <option value="">Storage</option>
                    <?php foreach ($storages as $storage) { ?>
                        <option value="<?= $storage['storage'] ?>" <?= isset($filters['storage']) && $filters['storage'] === $storage['storage'] ? 'selected' : '' ?>>
                            <?= $storage['storage'] ?>
                        </option>
                    <?php } ?>
                </select>
                <input type="number" name="min_price" placeholder="Min Price" value="<?= $filters['min_price'] ?? '' ?>">
                <input type="number" name="max_price" placeholder="Max Price" value="<?= $filters['max_price'] ?? '' ?>">
                <button type="submit">Apply Filters</button>
            </div>
        </form>

        <!-- Products Grid -->
        <div class="product-grid">
            <?php if (!empty($laptops)) { ?>
                <?php foreach ($laptops as $laptop) { ?>
                    <div class="product-card">
                        <img src="../<?= $laptop['image_path'] ?>" alt="<?= htmlspecialchars($laptop['name']) ?>" class="product-image">
                        <div class="product-details">
                            <h3 class="product-name"><?= htmlspecialchars($laptop['name']) ?></h3>
                            <p class="product-brand">Brand: <?= htmlspecialchars($laptop['brand']) ?></p>
                            <div class="product-footer">
                                <p class="product-price">$<?= htmlspecialchars($laptop['price']) ?></p>
                                <a href="laptop-details.php?id=<?= htmlspecialchars($laptop['laptop_id']) ?>" class="details-button">More Details</a>
                            </div>
                            <form method="POST" action="update_cart.php">
                                <input type="hidden" name="laptop_id" value="<?= $laptop['laptop_id'] ?>">
                                <input type="hidden" name="quantity" value="1"> <!-- Default quantity to 1 -->
                                <button type="submit" class="add-to-cart-btn">Add to Cart</button>
                            </form>
                        </div>
                    </div>
                <?php } ?>
            <?php } else { ?>
                <p>No laptops found matching the filters.</p>
            <?php } ?>
        </div>
    </div>

    <?php include "../includes/footer.php"; ?>
</body>
</html>