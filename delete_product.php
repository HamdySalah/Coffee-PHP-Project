<?php
session_start();
require_once 'config.php';
require_once 'Database.php';

// Check if user is logged in and has admin role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 1) {
    header("Location: index.php");
    exit();
}

if (!isset($_GET['product_id'])) {
    header("Location: product.php");
    exit();
}

$product_id = intval($_GET['product_id']);

$db = new Database();
$conn = $db->connect();

try {
    // Start a transaction
    $conn->beginTransaction();

    // Delete related rows in Order_product table
    $delete_order_product_stmt = $conn->prepare("DELETE FROM Order_product WHERE f_product_id = :product_id");
    $delete_order_product_stmt->execute([':product_id' => $product_id]);

    // Delete the product
    $delete_product_stmt = $conn->prepare("DELETE FROM Product WHERE product_id = :product_id");
    $delete_product_stmt->execute([':product_id' => $product_id]);

    // Commit the transaction
    $conn->commit();

    header("Location: product.php");
    exit();
} catch (PDOException $e) {
    // Rollback the transaction in case of error
    $conn->rollBack();
    echo "Error deleting product: " . $e->getMessage();
}
?>