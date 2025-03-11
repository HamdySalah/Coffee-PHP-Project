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

// Build dynamic WHERE clause for filters
$where_clause = " WHERE o.status = 'Done'";
$params = [];
$filters_applied = false;

if (isset($_GET['filter_date']) && !empty($_GET['filter_date'])) {
    $where_clause .= " AND DATE(o.order_date) = :filter_date";
    $params[':filter_date'] = $_GET['filter_date'];
    $filters_applied = true;
}

if (isset($_GET['filter_user']) && !empty($_GET['filter_user'])) {
    $where_clause .= " AND (u.user_name LIKE :filter_user OR u.user_id = :filter_user_id)";
    $params[':filter_user'] = "%" . $_GET['filter_user'] . "%";
    $params[':filter_user_id'] = (int)$_GET['filter_user'];
    $filters_applied = true;
}

// Fetch orders with product prices and totals
$stmt = $conn->prepare("
    SELECT o.order_id, o.order_date, o.status, u.user_name,
           GROUP_CONCAT(CONCAT(p.product_name, ' (', op.quntity, ' x $', p.price, ')') SEPARATOR ', ') AS products,
           SUM(op.quntity) AS total_quantity,
           SUM(op.quntity * p.price) AS total_price
    FROM Orders o
    JOIN User u ON o.f_user_id = u.user_id
    LEFT JOIN Order_product op ON o.order_id = op.f_order_id
    LEFT JOIN Product p ON op.f_product_id = p.product_id
    $where_clause
    GROUP BY o.order_id, o.order_date, o.status, u.user_name
    ORDER BY o.order_date DESC
");
$stmt->execute($params);
$checks = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Calculate totals for summary
$total_orders = count($checks);
$total_price_all = array_sum(array_column($checks, 'total_price'));

// Fetch revenue breakdown by product (unchanged)
$revenue_stmt = $conn->prepare("
    SELECT p.product_name, SUM(op.quntity) AS total_sold, SUM(op.quntity * p.price) AS revenue
    FROM Orders o
    JOIN User u ON o.f_user_id = u.user_id
    LEFT JOIN Order_product op ON o.order_id = op.f_order_id
    LEFT JOIN Product p ON op.f_product_id = p.product_id
    $where_clause
    GROUP BY p.product_name
    HAVING total_sold > 0
    ORDER BY total_sold DESC
");
$revenue_stmt->execute($params);
$revenue_breakdown = $revenue_stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Checks - Coffee</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: left;
        }
    </style>
</head>
<body>
    <?php require "includes/header.php"; ?>

    <section class="ftco-section">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-12 ftco-animate">
                    <h3 class="mb-4 billing-heading text-center">Order Checks</h3>

                    <!-- Filter Form (removed status filter) -->
                    <form method="GET" class="mb-4">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="filter_date">Filter by Date</label>
                                    <input type="date" name="filter_date" id="filter_date" class="form-control" value="<?php echo isset($_GET['filter_date']) ? htmlspecialchars($_GET['filter_date']) : ''; ?>">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="filter_user">Filter by User (Name or ID)</label>
                                    <input type="text" name="filter_user" id="filter_user" class="form-control" value="<?php echo isset($_GET['filter_user']) ? htmlspecialchars($_GET['filter_user']) : ''; ?>" placeholder="e.g., Adham or 2">
                                </div>
                            </div>
                            <div class="col-md-12 d-flex justify-content-end mt-2">
                                <button type="submit" class="btn btn-primary py-2 px-4">Filter</button>
                                <a href="checks.php" class="btn btn-secondary py-2 px-4 ml-2">Clear</a>
                            </div>
                        </div>
                    </form>

                    <?php if (empty($checks)): ?>
                        <p class="text-center text-white">No checks available<?php echo $filters_applied ? ' for these filters' : ''; ?>.</p>
                    <?php else: ?>
                        <div class="table-responsive mb-4">
                            <table class="table table-dark table-striped">
                                <thead>
                                    <tr>
                                        <th>Order ID</th>
                                        <th>Date</th>
                                        <th>User</th>
                                        <th>Products (Quantity x Price)</th>
                                        <th>Total Quantity</th>
                                        <th>Total Price</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($checks as $check): ?>
                                        <tr>
                                            <td><?php echo $check['order_id']; ?></td>
                                            <td><?php echo date('Y-m-d H:i', strtotime($check['order_date'])); ?></td>
                                            <td><strong><?php echo $check['user_name']; ?></strong></td>
                                            <td><?php echo $check['products'] ?: 'No products'; ?></td>
                                            <td><?php echo $check['total_quantity'] ?: 0; ?></td>
                                            <td>$<?php echo number_format($check['total_price'] ?: 0, 2); ?></td>
                                            <td style="color: #14ff14;"><?php echo $check['status']; ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                                <tfoot>
                                    <tr class="table-light">
                                        <td colspan="3"><strong>Total Orders: <?php echo $total_orders; ?></strong></td>
                                        <td colspan="2"></td>
                                        <td><strong>Total Price: $<?php echo number_format($total_price_all, 2); ?></strong></td>
                                        <td></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                        <!-- Revenue Breakdown unchanged -->
                        <h4 class="mb-3 text-center">Revenue Breakdown by Product</h4>
                        <div class="table-responsive">
                            <table class="table table-dark table-striped">
                                <thead>
                                    <tr>
                                        <th>Product Name</th>
                                        <th>Total Sold</th>
                                        <th>Revenue</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($revenue_breakdown as $revenue): ?>
                                        <tr>
                                            <td><?php echo $revenue['product_name']; ?></td>
                                            <td><?php echo $revenue['total_sold']; ?></td>
                                            <td>$<?php echo number_format($revenue['revenue'], 2); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>

                    <div class="text-center mt-4">
                        <a href="index.php" class="btn btn-secondary py-3 px-4">Back to Home</a>
                        <?php if (!empty($checks)): ?>
                            <a href="export_checks.php?<?php echo http_build_query($_GET); ?>" class="btn btn-primary py-3 px-4">Export to CSV</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <?php require "includes/footer.php"; ?>
</body>
</html>