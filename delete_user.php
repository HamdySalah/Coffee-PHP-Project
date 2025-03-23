<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 1) {
    header("Location: login.php");
    exit();
}

if (isset($_GET['id'])) {
    try {
        $db = new Config();
        $conn = $db->connect();

        // Delete related rows in Order_product
        $stmt = $conn->prepare("DELETE FROM Order_product WHERE f_order_id IN (SELECT order_id FROM Orders WHERE f_user_id = :user_id)");
        $stmt->bindParam(':user_id', $_GET['id']);
        $stmt->execute();

        // Delete related orders
        $stmt = $conn->prepare("DELETE FROM Orders WHERE f_user_id = :user_id");
        $stmt->bindParam(':user_id', $_GET['id']);
        $stmt->execute();

        // Delete the user
        $stmt = $conn->prepare("DELETE FROM User WHERE user_id = :user_id");
        $stmt->bindParam(':user_id', $_GET['id']);
        $stmt->execute();

        header("Location: user.php");
        exit();
    } catch (PDOException $e) {
        die("Database Error: " . $e->getMessage());
    } catch (Exception $e) {
        die("General Error: " . $e->getMessage());
    }
} else {
    header("Location: user.php");
    exit();
}
?>
