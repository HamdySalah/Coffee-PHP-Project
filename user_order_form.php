<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$db = new Database();
$conn = $db->connect();

$products = $conn->query("SELECT * FROM Product WHERE status = 'available'")->fetchAll(PDO::FETCH_ASSOC);
$users = $_SESSION['role'] == 1 ? $conn->query("SELECT * FROM User WHERE role = 0")->fetchAll(PDO::FETCH_ASSOC) : [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_SESSION['role'] == 1 ? $_POST['user_id'] : $_SESSION['user_id'];
    $products_selected = $_POST['products'] ?? [];
    $quantities = $_POST['quantities'] ?? [];

    if (!empty($products_selected)) {
        try {
            $stmt = $conn->prepare("INSERT INTO Orders (order_date, status, f_user_id) VALUES (NOW(), 'Processing', :user_id)");
            $stmt->execute(['user_id' => $user_id]);
            $order_id = $conn->lastInsertId();
            $product_quantities = [];
            foreach ($products as $index => $product) {
                if (in_array($product['product_id'], $products_selected)) {
                    $quantity = $quantities[$index] ?? 0;
                    if ($quantity > 0) {
                        $product_quantities[$product['product_id']] = $quantity;
                    }
                }
            }
            $inserted_products = 0;
            foreach ($product_quantities as $product_id => $quantity) {
                $stmt = $conn->prepare("INSERT INTO Order_product (f_order_id, f_product_id, quntity) VALUES (:order_id, :product_id, :quantity)");
                $stmt->execute(['order_id' => $order_id, 'product_id' => $product_id, 'quantity' => $quantity]);
                $inserted_products++;
            }

            if ($inserted_products > 0) {
                header("Location: " . ($_SESSION['role'] == 1 ? "admin_orders.php" : "user_orders.php"));
                exit();
            } else {
                $error = "No valid products with quantities were selected.";
            }
        } catch (PDOException $e) {
            $error = "Error creating order: " . $e->getMessage();
        }
    } else {
        $error = "Please select at least one product.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Place Order - Coffee</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    
    <link href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Josefin+Sans:400,700" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Great+Vibes" rel="stylesheet">

    <link rel="stylesheet" href="assets/css/open-iconic-bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/animate.css">
    <link rel="stylesheet" href="assets/css/owl.carousel.min.css">
    <link rel="stylesheet" href="assets/css/owl.theme.default.min.css">
    <link rel="stylesheet" href="assets/css/magnific-popup.css">
    <link rel="stylesheet" href="assets/css/aos.css">
    <link rel="stylesheet" href="assets/css/ionicons.min.css">
    <link rel="stylesheet" href="assets/css/bootstrap-datepicker.css">
    <link rel="stylesheet" href="assets/css/jquery.timepicker.css">
    <link rel="stylesheet" href="assets/css/flaticon.css">
    <link rel="stylesheet" href="assets/css/icomoon.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php require "includes/header.php"; ?>

    <section class="ftco-section">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8 ftco-animate">
                    <form action="#" class="billing-form ftco-bg-dark p-3 p-md-5" method="post">
                        <h3 class="mb-4 billing-heading">Place an Order</h3>
                        <?php if (isset($error)): ?>
                            <p class="text-danger text-center"><?php echo $error; ?></p>
                        <?php endif; ?>
                        <div class="row align-items-end">
                            <?php if ($_SESSION['role'] == 1): ?>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="user_id">Select User</label>
                                        <select name="user_id" id="user_id" class="form-control" required>
                                            <option value="">Choose a user...</option>
                                            <?php foreach ($users as $user): ?>
                                                <option value="<?php echo $user['user_id']; ?>"><?php echo $user['user_name']; ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                            <?php endif; ?>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Select Products</label>
                                    <?php foreach ($products as $index => $product): ?>
                                        <div class="d-flex align-items-center mb-3">
                                            <input type="checkbox" name="products[]" value="<?php echo $product['product_id']; ?>" class="form-check-input mr-2" id="product_<?php echo $product['product_id']; ?>">
                                            <label class="form-check-label text-white mr-3" for="product_<?php echo $product['product_id']; ?>">
                                                <?php echo $product['product_name'] . " - $" . $product['price']; ?>
                                            </label>
                                            <input type="number" name="quantities[]" min="0" value="0" class="form-control w-25" placeholder="Qty">
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group mt-4">
                                    <button type="submit" class="btn btn-primary py-3 px-4">Place Order</button>
                                </div>
                            </div>
                            <div class="col-md-12 text-center mt-4">
                                <a href="index.php" class="btn btn-secondary py-3 px-4">Back to Home</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <?php require "includes/footer.php"; ?>

    <script src="assets/js/jquery.min.js"></script>
    <script src="assets/js/jquery-migrate-3.0.1.min.js"></script>
    <script src="assets/js/popper.min.js"></script>
    <script src="assets/js/bootstrap.min.js"></script>
    <script src="assets/js/jquery.easing.1.3.js"></script>
    <script src="assets/js/jquery.waypoints.min.js"></script>
    <script src="assets/js/jquery.stellar.min.js"></script>
    <script src="assets/js/owl.carousel.min.js"></script>
    <script src="assets/js/jquery.magnific-popup.min.js"></script>
    <script src="assets/js/aos.js"></script>
    <script src="assets/js/jquery.animateNumber.min.js"></script>
    <script src="assets/js/bootstrap-datepicker.js"></script>
    <script src="assets/js/jquery.timepicker.min.js"></script>
    <script src="assets/js/scrollax.min.js"></script>
    <script src="assets/js/main.js"></script>
</body>
</html>