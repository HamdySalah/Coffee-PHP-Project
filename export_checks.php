<?php
session_start();
require_once 'config.php';
require_once 'Database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 1) {
    header("Location: login.php");
    exit();
}

$db = new Database();
$conn = $db->connect();

$filters = [
    'filter_date' => $_GET['filter_date'] ?? null,
    'filter_user' => $_GET['filter_user'] ?? null,
    'filter_status' => $_GET['filter_status'] ?? null
];

$checks = $db->fetchFilteredOrders($filters);

header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="order_checks_' . date('Y-m-d_His') . '.csv"');

$output = fopen('php://output', 'w');

fputcsv($output, ['Order ID', 'Date', 'User', 'Products (Quantity x Price)', 'Total Quantity', 'Total Price', 'Status']);

foreach ($checks as $check) {
    fputcsv($output, [
        $check['order_id'],
        date('Y-m-d H:i', strtotime($check['order_date'])),
        $check['user_name'],
        $check['products'] ?: 'No products',
        $check['total_quantity'] ?: 0,
        '$' . number_format($check['total_price'] ?: 0, 2),
        $check['status']
    ]);
}

fclose($output);
exit();