<?php
class User {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Register a new user
    public function register($username, $email, $password) {
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
        $query = "INSERT INTO Users (username, email, password_hash) VALUES (?, ?, ?)";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$username, $email, $hashedPassword]);
    }

    // Log in an existing user
    public function login($email, $password) {
        $query = "SELECT * FROM Users WHERE email = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        if ($user && password_verify($password, $user['password_hash'])) {
            return $user;
        }
        return false;
    }

    // Fetch user details by ID
    public function getUserById($userId) {
        $query = "SELECT username, email FROM Users WHERE user_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$userId]);
        return $stmt->fetch();
    }

    // Update user details
    public function updateUser($userId, $username, $email, $password = null) {
        if ($password) {
            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
            $query = "UPDATE Users SET username = ?, email = ?, password_hash = ? WHERE id = ?";
            $stmt = $this->conn->prepare($query);
            return $stmt->execute([$username, $email, $hashedPassword, $userId]);
        } else {
            $query = "UPDATE Users SET username = ?, email = ? WHERE id = ?";
            $stmt = $this->conn->prepare($query);
            return $stmt->execute([$username, $email, $userId]);
        }
    }
}
?>