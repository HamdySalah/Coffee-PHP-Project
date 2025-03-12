<?php
require_once 'config.php';

class Database extends Config {

    public function fetchAllUsers() {
        $stmt = $this->connect()->query("SELECT * FROM User");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function fetchAllProducts() {
        $stmt = $this->connect()->query("SELECT * FROM Product");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function fetchAllOrders() {
        $stmt = $this->connect()->query("SELECT * FROM Orders");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function fetchUserById($user_id) {
        $stmt = $this->connect()->prepare("SELECT * FROM User WHERE user_id = :user_id");
        $stmt->execute(['user_id' => $user_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function fetchUserByEmail($email) {
        $stmt = $this->connect()->prepare("SELECT * FROM User WHERE email = :email");
        $stmt->execute(['email' => $email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function fetchProductById($product_id) {
        $stmt = $this->connect()->prepare("SELECT * FROM Product WHERE product_id = :product_id");
        $stmt->execute(['product_id' => $product_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

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

    public function insertUser($name, $email, $password, $room, $ext, $profile_picture, $role = 0) {
        $conn = $this->connect();
        try {
            $conn->beginTransaction();
            $stmt = $conn->prepare("
                INSERT INTO User (user_name, email, password, ext, profile_picture, role)
                VALUES (:name, :email, :password, :ext, :profile_picture, :role)
            ");
            $stmt->execute([
                ':name' => $name,
                ':email' => $email,
                ':password' => $password,
                ':ext' => $ext,
                ':profile_picture' => $profile_picture,
                ':role' => $role
            ]);
            $user_id = $conn->lastInsertId();
            $stmt = $conn->prepare("
                INSERT INTO user_room (user_id, room_name)
                VALUES (:user_id, :room_name)
            ");
            $stmt->execute([
                ':user_id' => $user_id,
                ':room_name' => $room
            ]);
            $conn->commit();
        } catch (PDOException $e) {
            $conn->rollBack();
            throw $e;
        }
    }

    public function fetchEmailByToken($token) {
        $stmt = $this->connect()->prepare("SELECT email FROM password_resets WHERE token = :token");
        $stmt->execute(['token' => $token]);
        return $stmt->fetchColumn();
    }

    public function updatePassword($email, $hashed_password) {
        $stmt = $this->connect()->prepare("UPDATE User SET password = :password WHERE email = :email");
        $stmt->execute(['password' => $hashed_password, 'email' => $email]);
    }

    public function deleteResetToken($token) {
        $stmt = $this->connect()->prepare("DELETE FROM password_resets WHERE token = :token");
        $stmt->execute(['token' => $token]);
    }

    public function storeResetToken($email, $token) {
        $stmt = $this->connect()->prepare("INSERT INTO password_resets (email, token) VALUES (:email, :token)");
        $stmt->execute(['email' => $email, 'token' => $token]);
    }
}
?>