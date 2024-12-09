<?php
function getDBConnection() {
    $host = "localhost";
    $db = "ecommerce";
    $user = "root";
    $pass = "";

    try {
        $conn = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $conn;
    } catch (PDOException $e) {
        echo "Database connection failed: " . $e->getMessage();
        exit;
    }
}
?>