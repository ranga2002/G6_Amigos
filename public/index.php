<?php
session_start();
require_once "../utils/db_connection.php";
require_once "../classes/Laptop.php";
require_once "../classes/Cart.php";

$conn = getDBConnection();
$laptop = new Laptop($conn);
$cart = new Cart($conn);

$userId = $_SESSION['user_id'] ?? 0; // Replace with actual user session ID if available

// Handle Add to Cart
// Add to Cart Logic in index.php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_to_cart') {
    $laptopId = $_POST['laptop_id'];
    $quantity = intval($_POST['quantity']);

    if ($userId) {
        // Logged-in user: store cart in the database
        $cart->addToCart($userId, $laptopId, $quantity);
        $_SESSION['success_message'] = "Laptop added to cart successfully!";
    } else {
        // Guest user: store cart in the session
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }
        if (isset($_SESSION['cart'][$laptopId])) {
            $_SESSION['cart'][$laptopId] += $quantity;
        } else {
            $_SESSION['cart'][$laptopId] = $quantity;
        }
        $_SESSION['success_message'] = "Laptop added to cart successfully!";
    }
}

// Initialize filters
$brandFilter = $_GET['brand'] ?? '';
$ramFilter = $_GET['ram'] ?? '';
$storageFilter = $_GET['storage'] ?? '';
$minPriceFilter = $_GET['min_price'] ?? '';
$maxPriceFilter = $_GET['max_price'] ?? '';

// Build the dynamic SQL query based on filters
$sql = "SELECT * FROM Laptops WHERE 1=1";

if ($brandFilter) {
    $sql .= " AND brand = :brand";
}
if ($ramFilter) {
    $sql .= " AND RAM = :ram";
}
if ($storageFilter) {
    $sql .= " AND storage = :storage";
}
if ($minPriceFilter) {
    $sql .= " AND price >= :min_price";
}
if ($maxPriceFilter) {
    $sql .= " AND price <= :max_price";
}

$stmt = $conn->prepare($sql);

// Bind filter parameters
if ($brandFilter) {
    $stmt->bindParam(':brand', $brandFilter);
}
if ($ramFilter) {
    $stmt->bindParam(':ram', $ramFilter, PDO::PARAM_INT);
}
if ($storageFilter) {
    $stmt->bindParam(':storage', $storageFilter);
}
if ($minPriceFilter) {
    $stmt->bindParam(':min_price', $minPriceFilter, PDO::PARAM_INT);
}
if ($maxPriceFilter) {
    $stmt->bindParam(':max_price', $maxPriceFilter, PDO::PARAM_INT);
}

// Execute the query
$stmt->execute();
$laptops = $stmt->fetchAll();

// Fetch filter options dynamically
$brands = $conn->query("SELECT DISTINCT brand FROM Laptops")->fetchAll();
$rams = $conn->query("SELECT DISTINCT RAM FROM Laptops ORDER BY RAM ASC")->fetchAll();
$storages = $conn->query("SELECT DISTINCT storage FROM Laptops ORDER BY storage ASC")->fetchAll();

// Fetch the deal of the day (cheapest laptop)
$dealOfTheDay = $conn->query("SELECT * FROM Laptops ORDER BY price ASC LIMIT 1")->fetch();

// Fetch featured laptops for the sidebar
$featuredLaptops = $conn->query("SELECT * FROM Laptops ORDER BY price DESC LIMIT 3")->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Laptop Store</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <?php include "../includes/header.php"; ?>

    <!-- Hero Section -->
    <div class="hero">
        <h1>Welcome to Wizard's Store</h1>
        <p>Your one-stop shop for the best laptop deals!</p>
    </div>

    <div class="container">
        <?php if (isset($_SESSION['success_message'])) { ?>
            <div class="alert success">
                <?= $_SESSION['success_message'] ?>
                <?php unset($_SESSION['success_message']); ?>
            </div>
        <?php } ?>
        <?php if (isset($_SESSION['error_message'])) { ?>
            <div class="alert error">
                <?= $_SESSION['error_message'] ?>
                <?php unset($_SESSION['error_message']); ?>
            </div>
        <?php } ?>

        <div class="main-content">
            <!-- Left Sidebar -->
            <aside class="sidebar">
                <h2>Top Deals</h2>
                <?php foreach ($featuredLaptops as $laptop) { ?>
                    <div class="sidebar-item">
                        <h3><?= $laptop['name'] ?></h3>
                        <p><strong>Price:</strong> $<?= $laptop['price'] ?></p>
                        <a href="laptop-details.php?id=<?= $laptop['laptop_id'] ?>">View Deal</a>
                    </div>
                <?php } ?>
                <h2>Laptop Tips</h2>
                <ul>
                    <li><a href="#">How to choose the best gaming laptop</a></li>
                    <li><a href="#">Why SSD is better than HDD</a></li>
                    <li><a href="#">Laptop care tips to increase longevity</a></li>
                </ul>
            </aside>

            <!-- Main Section -->
            <section class="main-section">
                <!-- Featured Laptop (Deal of the Day) -->
                <div class="featured-laptop">
                    <h2>Deal of the Day</h2>
                    <div class="deal-of-the-day" style="background-image: url('../<?= $dealOfTheDay['image_path'] ?>');">
                        <div class="deal-overlay">
                            <h2><?= $dealOfTheDay['name'] ?></h2>
                            <p class="deal-price">$<?= $dealOfTheDay['price'] ?></p>
                            <a href="laptop-details.php?id=<?= $dealOfTheDay['laptop_id'] ?>" class="shop-now-btn">Shop Now</a>
                        </div>
                    </div>
                </div>

                <!-- Search Filters -->
                <form class="filter-form" method="GET" action="index.php">
                    <div class="filter-group">
                        <label for="brand">Brand</label>
                        <select name="brand" id="brand">
                            <option value="">All Brands</option>
                            <?php foreach ($brands as $brand) { ?>
                                <option value="<?= $brand['brand'] ?>" <?= $brand['brand'] === $brandFilter ? 'selected' : '' ?>><?= $brand['brand'] ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label for="ram">RAM</label>
                        <select name="ram" id="ram">
                            <option value="">All RAM Sizes</option>
                            <?php foreach ($rams as $ram) { ?>
                                <option value="<?= $ram['RAM'] ?>" <?= $ram['RAM'] == $ramFilter ? 'selected' : '' ?>><?= $ram['RAM'] ?> GB</option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label for="storage">Storage</label>
                        <select name="storage" id="storage">
                            <option value="">All Storage Options</option>
                            <?php foreach ($storages as $storage) { ?>
                                <option value="<?= $storage['storage'] ?>" <?= $storage['storage'] === $storageFilter ? 'selected' : '' ?>><?= $storage['storage'] ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="filter-group" id="price-range">
                        
                        
                        <input type="number" name="min_price" id="min_price" placeholder="Min Price" value="<?= $minPriceFilter ?>">
                        <input type="number" name="max_price" id="max_price" placeholder="Max Price" value="<?= $maxPriceFilter ?>">
                    </div>
                    <button type="submit">Search</button>
                </form>

                <!-- Product Grid -->
                <h2>All Laptops</h2>
                <div class="product-grid">
                    <?php foreach ($laptops as $laptop) { ?>
                        <div class="product-card">
                            <img src="../<?= $laptop['image_path'] ?>" alt="<?= $laptop['name'] ?>" class="product-image">
                            <div class="product-details">
                                <h3 class="product-name"><?= $laptop['name'] ?></h3>
                                <p class="product-brand">Brand: <?= $laptop['brand'] ?></p>
                                <p class="product-price">$<?= $laptop['price'] ?></p>
                                <div class="product-footer">
                                    <div class="quantity-controls">
                                        <button class="quantity-btn" onclick="updateQuantity(<?= $laptop['laptop_id'] ?>, -1)">-</button>
                                        <span id="quantity-<?= $laptop['laptop_id'] ?>">0</span>
                                        <button class="quantity-btn" onclick="updateQuantity(<?= $laptop['laptop_id'] ?>, 1)">+</button>
                                    </div>
                                    <button class="add-to-cart-btn" id="cart-btn-<?= $laptop['laptop_id'] ?>" onclick="addToCart(<?= $laptop['laptop_id'] ?>)" disabled>Add to Cart</button>

                                </div>
                            </div>
                        </div>
                    <?php } ?>
                </div>

                <!-- Newsletter Section -->
                <div class="newsletter">
                    <h2>Subscribe to Our Newsletter</h2>
                    <p>Get updates on the latest deals and offers!</p>
                    <form method="POST" action="newsletter.php">
                        <input type="email" name="email" placeholder="Enter your email" required>
                        <button type="submit">Subscribe</button>
                    </form>
                </div>

                <!-- Blog/News Section -->
                <div class="blog-section">
                    <h2>Latest News</h2>
                    <div class="blog-grid">
                        <div class="blog-post">
                            <h3>Top 5 Gaming Laptops in 2024</h3>
                            <p>Explore our expert recommendations for gaming laptops...</p>
                            <a href="#">Read More</a>
                        </div>
                        <div class="blog-post">
                            <h3>Why SSDs Matter in Laptops</h3>
                            <p>Learn about the benefits of SSDs over traditional HDDs...</p>
                            <a href="#">Read More</a>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>

    <?php include "../includes/footer.php"; ?>
</body>

<script>
// Update quantity dynamically
function updateQuantity(laptopId, delta) {
    const quantityElement = document.getElementById(`quantity-${laptopId}`);
    const cartButton = document.getElementById(`cart-btn-${laptopId}`);
    const currentQuantity = parseInt(quantityElement.textContent);

    // Prevent quantity from going below 0
    if (currentQuantity === 0 && delta === -1) return;

    const newQuantity = currentQuantity + delta;
    quantityElement.textContent = newQuantity;

    // Enable/disable the "Add to Cart" button based on quantity
    cartButton.disabled = newQuantity === 0;
}


// Add to cart with the specified quantity
function addToCart(laptopId) {
    const quantityElement = document.getElementById(`quantity-${laptopId}`);
    const quantity = parseInt(quantityElement.textContent);

    if (quantity === 0) {
        alert("Please select a quantity before adding to cart.");
        return;
    }

    // Make an AJAX request to add the item to the cart
    const xhr = new XMLHttpRequest();
    xhr.open("POST", "./update_cart.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

    xhr.onload = function () {
        if (xhr.status === 200) {
            const response = JSON.parse(xhr.responseText);
            if (response.status === "success") {
                alert(response.message);
                quantityElement.textContent = "0";
            } else {
                alert("Error: " + response.message);
            }
        } else if (xhr.status === 401) {
            alert("Please log in to add items to the cart.");
            window.location.href = "signup.php";
        } else {
            alert("Error: Unable to connect to the server.");
        }
    };

    xhr.send(`laptop_id=${laptopId}&quantity=${quantity}`);
}
</script>

</html>
