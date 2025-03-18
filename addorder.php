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

// Fetch products ordered by product_id in descending order
$products = $conn->query("SELECT * FROM Product WHERE status = 'available' ORDER BY product_id DESC")->fetchAll(PDO::FETCH_ASSOC);
$users = $_SESSION['role'] == 1 ? $conn->query("SELECT * FROM User WHERE role = 0")->fetchAll(PDO::FETCH_ASSOC) : [];

$selected_product_id = isset($_GET['id']) ? intval($_GET['id']) : null;

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
                header("Location: " . ($_SESSION['role'] == 1 ? "admin_orders.php" : "orders.php"));
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
    <style>
        .product-image {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            object-fit: cover;
        }
    </style>
</head>
<body>
    <?php require "includes/header.php"; ?>

    <section class="ftco-section" >
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8 ftco-animate">
                    <form action="#" class="billing-form ftco-bg-dark p-3 p-md-5" style="width: 900px" method="post">
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
                                    <table class="table table-dark table-striped">
                                        <thead>
                                            <tr>
                                                <th scope="col">Cheek</th>
                                                <th scope="col">Product Name</th>
                                                <th scope="col">Price</th>
                                                <th scope="col">Quantity</th>
                                                <th scope="col">Image</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($products as $index => $product): ?>
                                                <tr>
                                                    <td>
                                                        <input type="checkbox" name="products[]" value="<?php echo $product['product_id']; ?>" class="form-check-input" id="product_<?php echo $product['product_id']; ?>" <?php echo ($product['product_id'] == $selected_product_id) ? 'checked' : ''; ?>>
                                                    </td>
                                                    <td>
                                                        <label class="form-check-label text-white" for="product_<?php echo $product['product_id']; ?>">
                                                            <?php echo htmlspecialchars($product['product_name']); ?>
                                                        </label>
                                                    </td>
                                                    <td>
                                                        <?php echo number_format($product['price'], 2); ?> $
                                                    </td>
                                                    <td>
                                                        <input type="number" name="quantities[]" min="0" value="<?php echo ($product['product_id'] == $selected_product_id) ? '1' : '0'; ?>" class="form-control" placeholder="Qty" style="width: 150px;">
                                                    </td>
                                                    <td>
                                                        <?php if (!empty($product['product_image']) && file_exists($product['product_image'])): ?>
                                                            <img src="<?php echo htmlspecialchars($product['product_image']); ?>" class="product-image" alt="Product Image">
                                                        <?php else: ?>
                                                            <img src="assets/images/drink-5.jpg" class="product-image" alt="Default Image">
                                                        <?php endif; ?>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
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
</body>
</html>