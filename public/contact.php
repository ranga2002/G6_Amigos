<!DOCTYPE html>
<html lang="en">
<head>
    <title>Contact Us</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <style>
        #map {
            width: 100%;
            height: 400px;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <?php include "../includes/header.php"; ?>
    <div class="container">
        <h1>Contact Us</h1>
        <p>If you have any questions or need support, feel free to reach out to us!</p>

        <!-- Contact Details -->
        <div class="contact-details">
            <p><strong>Address:</strong> 123 Main Street, Toronto, ON, Canada</p>
            <p><strong>Email:</strong> support@company.com</p>
            <p><strong>Phone:</strong> +1 (123) 456-7890</p>
        </div>

        <!-- OpenStreetMap Section -->
        <div id="map"></div>

        <!-- Contact Form -->
        <h2>Send Us a Message</h2>
        <form method="POST" action="send_message.php">
            <label for="name">Name:</label>
            <input type="text" id="name" name="name" required>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>

            <label for="message">Message:</label>
            <textarea id="message" name="message" rows="5" required></textarea>

            <button type="submit">Send Message</button>
        </form>
    </div>
    <?php include "../includes/footer.php"; ?>

    <script>
        // Initialize the map
        const map = L.map('map').setView([43.65107, -79.347015], 13); // Coordinates for Toronto, Canada

        // Add OpenStreetMap tiles
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);

        // Add a marker at the company's location
        const marker = L.marker([43.65107, -79.347015]).addTo(map);
        marker.bindPopup("<b>Our Location</b><br>123 Main Street, Toronto, ON, Canada").openPopup();
    </script>
</body>
</html>