<?php
// session_start();
require_once 'config.php';

// if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 1) {
//     header("Location: login.php");
//     exit();
// }

$db = new Database();
$conn = $db->connect();

$stmt = $conn->query("SELECT * FROM bill");
$checks = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Checks</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<?php require "includes/header.php"; ?>
    <div class="container mt-5">
        <h2>Checks</h2>
        <table class="table">
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>User</th>
                    <th>Product</th>
                    <th>Quantity</th>
                    <th>Price</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($checks as $check): ?>
                    <tr>
                        <td><?php echo $check['f_order_id']; ?></td>
                        <td><?php echo $check['user_name']; ?></td>
                        <td><?php echo $check['product_name']; ?></td>
                        <td><?php echo $check['quntity']; ?></td>
                        <td><?php echo $check['price']; ?></td>
                        <td><?php echo $check['total_amount']; ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php require "includes/footer.php"; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>