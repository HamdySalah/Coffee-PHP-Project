<?php
session_start();
require_once 'config.php';
require_once 'Database.php'; 

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 1) {
    header("Location: index.php");
    exit();
}

$db = new Database();
$conn = $db->connect();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['order_id']) && isset($_POST['status'])) {
    $stmt = $conn->prepare("UPDATE Orders SET status = :status WHERE order_id = :order_id");
    $stmt->execute([':status' => $_POST['status'], ':order_id' => $_POST['order_id']]);
    header("Location: admin_orders.php?" . http_build_query($_GET));
    exit();
}

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
    $filters_applied = true;
}

$stmt = $conn->prepare("
    SELECT o.order_id, o.order_date, o.status, u.user_name, o.order_notes,
           GROUP_CONCAT(CONCAT(p.product_name, ' (', op.quntity, ' x $', p.price, ')') SEPARATOR ', ') AS products,
           SUM(op.quntity) AS total_quantity,
           SUM(op.quntity * p.price) AS total_price
    FROM Orders o
    JOIN User u ON o.f_user_id = u.user_id
    LEFT JOIN Order_product op ON o.order_id = op.f_order_id
    LEFT JOIN Product p ON op.f_product_id = p.product_id
    $where_clause
    GROUP BY o.order_id, o.order_date, o.status, u.user_name, o.order_notes
    ORDER BY o.order_date DESC
");
$stmt->execute($params);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

$total_orders = count($orders);
$total_revenue = array_sum(array_column($orders, 'total_price'));
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>All Orders - Coffee</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    
    <style>
        .status-overdue { color:rgb(250, 102, 117);}
        .status-done {  color:rgb(40, 167, 69);}
    </style>
</head>
<body>
    <?php require "includes/header.php"; ?>

    <section class="ftco-section">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-12 ftco-animate">
                    <h3 class="mb-4 billing-heading text-center">All Orders</h3>

                    <form method="GET" class="mb-4">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="filter_date">Filter by Date</label>
                                    <input type="date" name="filter_date" id="filter_date" class="form-control" value="<?php echo isset($_GET['filter_date']) ? htmlspecialchars($_GET['filter_date']) : ''; ?>">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="filter_user">Filter by User (Name or ID)</label>
                                    <input type="text" name="filter_user" id="filter_user" class="form-control" value="<?php echo isset($_GET['filter_user']) ? htmlspecialchars($_GET['filter_user']) : ''; ?>" placeholder="e.g., Adham or 2">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="filter_status">Filter by Status</label>
                                    <select name="filter_status" id="filter_status" class="form-control">
                                        <option value="">All Statuses</option>
                                        <option value="Processing" <?php echo (isset($_GET['filter_status']) && $_GET['filter_status'] == 'Processing') ? 'selected' : ''; ?>>Processing</option>
                                        <option value="Out for delivery" <?php echo (isset($_GET['filter_status']) && $_GET['filter_status'] == 'Out for delivery') ? 'selected' : ''; ?>>Out for Delivery</option>
                                        <option value="Done" <?php echo (isset($_GET['filter_status']) && $_GET['filter_status'] == 'Done') ? 'selected' : ''; ?>>Done</option>
                                        <option value="Canceled" <?php echo (isset($_GET['filter_status']) && $_GET['filter_status'] == 'Canceled') ? 'selected' : ''; ?>>Canceled</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-12 d-flex justify-content-end mt-2">
                                <button type="submit" class="btn btn-primary py-2 px-4">Filter</button>
                                <a href="admin_orders.php" class="btn btn-secondary py-2 px-4 ml-2">Clear</a>
                            </div>
                        </div>
                    </form>

                    <?php if (empty($orders)): ?>
                        <p class="text-center text-white">No orders found<?php echo $filters_applied ? ' for these filters' : ''; ?>.</p>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-dark table-striped">
                                <thead>
                                    <tr>
                                        <th>Order ID</th>
                                        <th>Date</th>
                                        <th>User</th>
                                        <th>Products (Quantity x Price)</th>
                                        <th>Order Notes</th>
                                        <th>Total Quantity</th>
                                        <th>Total Price</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($orders as $order): ?>
                                        <?php
                                        $order_age = (strtotime('now') - strtotime($order['order_date'])) / (60 * 60); // Hours
                                        $display_status = ($order['status'] == 'Processing' && $order_age > 24) ? 'Canceled' : $order['status'];
                                        ?>
                                        <tr <?php echo $display_status == 'Canceled' ? 'class="status-overdue"' : ''; echo $display_status == 'Done' ? 'class="status-done"' : ''; ?>>
                                            <td><?php echo $order['order_id']; ?></td>
                                            <td><?php echo date('Y-m-d H:i', strtotime($order['order_date'])); ?></td>
                                            <td><?php echo $order['user_name']; ?></td>
                                            <td><?php echo $order['products'] ?: 'No products'; ?></td>
                                            <td><?php echo htmlspecialchars($order['order_notes'] ?? 'No notes'); ?></td>
                                            <td><?php echo $order['total_quantity'] ?: 0; ?></td>
                                            <td>$<?php echo number_format($order['total_price'] ?: 0, 2); ?></td>
                                            <td>
                                                <form method="POST" class="d-inline">
                                                    <input type="hidden" name="order_id" value="<?php echo $order['order_id']; ?>">
                                                    <select name="status" class="form-control form-control-sm d-inline-block w-auto" onchange="this.form.submit()">
                                                        <option value="Processing" <?php echo $display_status == 'Processing' ? 'selected' : ''; ?>>Processing</option>
                                                        <option value="Out for delivery" <?php echo $display_status == 'Out for delivery' ? 'selected' : ''; ?>>Out for Delivery</option>
                                                        <option value="Done" <?php echo $display_status == 'Done' ? 'selected' : ''; ?>>Done</option>
                                                        <option value="Canceled" <?php echo $display_status == 'Canceled' ? 'selected' : ''; ?>>Canceled</option>
                                                    </select>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                                <tfoot>
                                    <tr class="table-light">
                                        <td colspan="3"><strong>Total Orders: <?php echo $total_orders; ?></strong></td>
                                        <td colspan="2"></td>
                                        <td><strong>Total Revenue: $<?php echo number_format($total_revenue, 2); ?></strong></td>
                                        <td></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    <?php endif; ?>

                    <div class="text-center mt-4">
                        <a href="index.php" class="btn btn-secondary py-3 px-4">Back to Home</a>
                        <a href="addorder.php" class="btn btn-primary py-3 px-4">Add Order for User</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <?php require "includes/footer.php"; ?>

</body>
</html>