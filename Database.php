<?php
require_once 'config.php';

class Database extends Config {
    // Method to fetch all users
    public function fetchAllUsers() {
        $stmt = $this->connect()->query("SELECT * FROM User");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Method to fetch all products
    public function fetchAllProducts() {
        $stmt = $this->connect()->query("SELECT * FROM Product");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Method to fetch all orders
    public function fetchAllOrders() {
        $stmt = $this->connect()->query("SELECT * FROM Orders");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Method to fetch user by ID
    public function fetchUserById($user_id) {
        $stmt = $this->connect()->prepare("SELECT * FROM User WHERE user_id = :user_id");
        $stmt->execute(['user_id' => $user_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Method to fetch user by email
    public function fetchUserByEmail($email) {
        $stmt = $this->connect()->prepare("SELECT * FROM User WHERE email = :email");
        $stmt->execute(['email' => $email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Method to fetch product by ID
    public function fetchProductById($product_id) {
        $stmt = $this->connect()->prepare("SELECT * FROM Product WHERE product_id = :product_id");
        $stmt->execute(['product_id' => $product_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Method to fetch order by ID
    public function fetchOrderById($order_id) {
        $stmt = $this->connect()->prepare("SELECT * FROM Orders WHERE order_id = :order_id");
        $stmt->execute(['order_id' => $order_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Method to fetch all rooms
    public function fetchAllRooms() {
        $stmt = $this->connect()->query("SELECT * FROM user_room");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Method to insert a new user
    public function insertUser($name, $email, $password, $room, $ext, $role = 0) {
        $stmt = $this->connect()->prepare("INSERT INTO User (user_name, email, password, role) VALUES (:name, :email, :password, :role)");
        $stmt->execute(['name' => $name, 'email' => $email, 'password' => $password, 'role' => $role]);

        // Get the last inserted user ID
        $user_id = $this->connect()->lastInsertId();

        // Insert into user_room table
        $stmt = $this->connect()->prepare("INSERT INTO user_room (user_id, room_name, ext) VALUES (:user_id, :room, :ext)");
        $stmt->execute(['user_id' => $user_id, 'room' => $room, 'ext' => $ext]);

        return $user_id;
    }
}
?>