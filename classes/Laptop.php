<?php
class Laptop {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getAllLaptops() {
        $query = "SELECT * FROM Laptops";
        $stmt = $this->conn->query($query);
        return $stmt->fetchAll();
    }

    public function getLaptopById($id) {
        $query = "SELECT * FROM Laptops WHERE laptop_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function addLaptop($data) {
        $query = "INSERT INTO Laptops (name, description, brand, processor, RAM, storage, price, stock, category_id)
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute(array_values($data));
    }
}
?>
