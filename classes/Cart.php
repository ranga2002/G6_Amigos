<?php
class Cart {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function addToCart($userId, $laptopId, $quantityDelta) {
        $query = "INSERT INTO Cart (user_id, laptop_id, quantity) 
                  VALUES (?, ?, ?) 
                  ON DUPLICATE KEY UPDATE quantity = quantity + VALUES(quantity)";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$userId, $laptopId, $quantityDelta]);
    }    

    public function removeFromCart($userId, $laptopId) {
        $query = "DELETE FROM Cart WHERE user_id = ? AND laptop_id = ?";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$userId, $laptopId]);
    }

    public function getCartItems($userId) {
        $query = "SELECT c.laptop_id, l.name, l.price, l.stock, c.quantity 
                  FROM Cart c 
                  JOIN Laptops l ON c.laptop_id = l.laptop_id 
                  WHERE c.user_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    public function clearCart($userId) {
        $query = "DELETE FROM Cart WHERE user_id = ?";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$userId]);
    }

    public function updateCartQuantity($userId, $laptopId, $quantityDelta) {
        // Check if the item already exists in the cart
        $query = "SELECT quantity FROM Cart WHERE user_id = ? AND laptop_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$userId, $laptopId]);
        $currentQuantity = $stmt->fetchColumn();
    
        if ($currentQuantity !== false) {
            // Update quantity if the item already exists
            $newQuantity = $currentQuantity + $quantityDelta;
    
            if ($newQuantity > 0) {
                $query = "UPDATE Cart SET quantity = ? WHERE user_id = ? AND laptop_id = ?";
                $stmt = $this->conn->prepare($query);
                $stmt->execute([$newQuantity, $userId, $laptopId]);
            } else {
                // Remove the item if the quantity drops to 0 or below
                $this->removeFromCart($userId, $laptopId);
            }
        } else if ($quantityDelta > 0) {
            // Insert new item into the cart
            $this->addToCart($userId, $laptopId, $quantityDelta);
        }
    }
    
}
?>
