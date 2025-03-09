<?php
session_start();
require_once 'config.php';

// Check if user is logged in and has admin role
// if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 1) {
//     header("Location: ../public/index.php");
//     exit();
// }

if (!isset($_GET['id'])) {
    header("Location: product.php");
    exit();
}

$product_id = intval($_GET['id']);

$db = new Database();
$conn = $db->connect();

$delete_stmt = $conn->prepare("DELETE FROM Product WHERE product_id = :product_id");
$delete_stmt->execute([':product_id' => $product_id]);

header("Location: product.php");
exit();
?>
