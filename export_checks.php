<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 1) {
    header("Location: login.php");
    exit();
}

$db = new Database();
$conn = $db->connect();

$where_clause = "";
$params = [];
$filters_applied = false;

if (isset($_GET['filter_date']) && !empty($_GET['filter_date'])) {
    $where_clause .= " WHERE DATE(o.order_date) = :filter_date";
    $params[':filter_date'] = $_GET['filter_date'];
    $filters_applied = true;
}

if (isset($_GET['filter_user']) && !empty($_GET['filter_user'])) {
    $where_clause .= $filters_applied ? " AND" : " WHERE";
    $where_clause .= " (u.user_name LIKE :filter_user OR u.user_id = :filter_user_id)";
    $params[':filter_user'] = "%" . $_GET['filter_user'] . "%";
    $params[':filter_user_id'] = (int)$_GET['filter_user'];
    $filters_applied = true;
}

if (isset($_GET['filter_status']) && !empty($_GET['filter_status'])) {
    $where_clause .= $filters_applied ? " AND" : " WHERE";
    $where_clause .= " o.status = :filter_status";
    $params[':filter_status'] = $_GET['filter_status'];
}

$stmt = $conn->prepare("
    SELECT o.order_id, o.order_date, u.user_name, o.status,
           GROUP_CONCAT(CONCAT(p.product_name, ' (', op.quntity, ' x $', p.price, ')') SEPARATOR ', ') AS products,
           SUM(op.quntity) AS total_quantity,
           SUM(op.quntity * p.price) AS total_price
    FROM Orders o
    JOIN User u ON o.f_user_id = u.user_id
    LEFT JOIN Order_product op ON o.order_id = op.f_order_id
    LEFT JOIN Product p ON op.f_product_id = p.product_id
    $where_clause
    GROUP BY o.order_id, o.order_date, o.status, u.user_name
");
$stmt->execute($params);
$checks = $stmt->fetchAll(PDO::FETCH_ASSOC);

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