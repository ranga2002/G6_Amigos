<?php
class Order {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function createOrder($userId, $cartItems) {
        try {
            $this->conn->beginTransaction();

            // Calculate total price
            $totalPrice = 0;
            foreach ($cartItems as $item) {
                $totalPrice += $item['price'] * $item['quantity'];
            }

            // Insert into Orders
            $query = "INSERT INTO Orders (user_id, total_price) VALUES (?, ?)";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$userId, $totalPrice]);
            $orderId = $this->conn->lastInsertId();

            // Insert into OrderDetails
            foreach ($cartItems as $item) {
                $query = "INSERT INTO OrderDetails (order_id, laptop_id, quantity, price_per_unit) 
                          VALUES (?, ?, ?, ?)";
                $stmt = $this->conn->prepare($query);
                $stmt->execute([$orderId, $item['laptop_id'], $item['quantity'], $item['price']]);
            }

            // Clear user's cart
            $query = "DELETE FROM Cart WHERE user_id = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$userId]);

            $this->conn->commit();
            return $orderId;
        } catch (Exception $e) {
            $this->conn->rollBack();
            return false;
        }
    }

    public function getOrderDetails($orderId) {
        $query = "SELECT od.order_detail_id, l.name, od.quantity, od.price_per_unit 
                  FROM OrderDetails od 
                  JOIN Laptops l ON od.laptop_id = l.laptop_id 
                  WHERE od.order_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$orderId]);
        return $stmt->fetchAll();
    }
}
?>
