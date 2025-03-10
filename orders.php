<?php
session_start();
require_once 'config.php';
require_once 'Database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$db = new Database();
$conn = $db->connect();

$where_clause = "WHERE o.f_user_id = :user_id";
$params = [':user_id' => $_SESSION['user_id']];
if (isset($_GET['filter_date']) && !empty($_GET['filter_date'])) {
    $filter_date = $_GET['filter_date'];
    $where_clause .= " AND DATE(o.order_date) = :filter_date";
    $params[':filter_date'] = $filter_date;
}

$stmt = $conn->prepare("
    SELECT o.order_id, o.order_date, o.status,
           GROUP_CONCAT(CONCAT(p.product_name, ' (', op.quntity, ' x $', p.price, ')') SEPARATOR ', ') AS products,
           SUM(op.quntity) AS total_quantity,
           SUM(op.quntity * p.price) AS total_price
    FROM Orders o
    LEFT JOIN Order_product op ON o.order_id = op.f_order_id
    LEFT JOIN Product p ON op.f_product_id = p.product_id
    $where_clause
    GROUP BY o.order_id, o.order_date, o.status
");
$stmt->execute($params);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

$total_orders = count($orders);
$total_price_all = array_sum(array_column($orders, 'total_price'));
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>My Orders - Coffee</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
</head>
<body>
    <?php require "includes/header.php"; ?>

    <section class="ftco-section">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-10 ftco-animate">
                    <h3 class="mb-4 billing-heading text-center">My Orders</h3>
                    <form method="GET" class="mb-4">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="filter_date">Filter by Date</label>
                                    <input type="date" name="filter_date" id="filter_date" class="form-control" value="<?php echo isset($_GET['filter_date']) ? htmlspecialchars($_GET['filter_date']) : ''; ?>">
                                </div>
                            </div>
                            <div class="col-md-6 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary py-2 px-4">Filter</button>
                                <a href="user_orders.php" class="btn btn-secondary py-2 px-4 ml-2">Clear</a>
                            </div>
                        </div>
                    </form>

                    <?php if (empty($orders)): ?>
                        <p class="text-center text-white">You have no orders yet<?php echo isset($_GET['filter_date']) ? ' for this date' : ''; ?>.</p>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-dark table-striped">
                                <thead>
                                    <tr>
                                        <th>Order ID</th>
                                        <th>Date</th>
                                        <th>Products (Quantity x Price)</th>
                                        <th>Total Quantity</th>
                                        <th>Total Price</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($orders as $order): ?>
                                        <tr>
                                            <td><?php echo $order['order_id']; ?></td>
                                            <td><?php echo date('Y-m-d H:i', strtotime($order['order_date'])); ?></td>
                                            <td><?php echo $order['products'] ?: 'No products associated'; ?></td>
                                            <td><?php echo $order['total_quantity'] ?: 0; ?></td>
                                            <td>$<?php echo number_format($order['total_price'] ?: 0, 2); ?></td>
                                            <td><?php echo $order['status']; ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                                <tfoot>
                                    <tr class="table-light">
                                        <td colspan="2"><strong>Total Orders: <?php echo $total_orders; ?></strong></td>
                                        <td colspan="2"></td>
                                        <td><strong>Total Price: $<?php echo number_format($total_price_all, 2); ?></strong></td>
                                        <td></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    <?php endif; ?>
                    <div class="text-center mt-4">
                        <a href="index.php" class="btn btn-secondary py-3 px-4">Back to Home</a>
                        <a href="user_order_form.php" class="btn btn-primary py-3 px-4">Place New Order</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <?php require "includes/footer.php"; ?>
</body>
</html>