<?php
// session_start();
require_once 'config.php';

// if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 1) {
//     header("Location: ../public/index.php");
//     exit();
// }

$db = new Database();
$conn = $db->connect();

$stmt = $conn->prepare("SELECT p.*, c.category_name FROM Product p JOIN Category c ON p.f_category_id = c.category_id");
$stmt->execute();
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Products</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<?php require "includes/header.php"; ?>
    <div class="container mt-5">
        <h2>Products</h2>
        <a href="add_product.php" class="btn btn-success mb-3">Add Product</a>
        <div class="row">
            <?php foreach ($products as $product): ?>
                <div class="col-md-3 mb-3">
                    <div class="card">
                        <img src="assets/images/drink-5.jpg" class="card-img-top" alt="Product Image" style="height: 50%;">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo $product['product_name']; ?></h5>
                            <p class="card-text">Category: <?php echo $product['category_name']; ?></p>
                            <p class="card-text">Price: <?php echo $product['price']; ?> $</p>
                            <p style="color=green" class="card-text">Status: <?php echo $product['status']; ?></p>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php require "includes/footer.php"; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>