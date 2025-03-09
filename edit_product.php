<?php
session_start();
require_once 'config.php';
require_once 'Database.php';

// Check if user is logged in and has admin role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 1) {
    header("Location: ../public/index.php");
    exit();
}

$db = new Database();
$conn = $db->connect();

$categories = $conn->query("SELECT * FROM Category")->fetchAll(PDO::FETCH_ASSOC);

if (!isset($_GET['id'])) {
    header("Location: product.php");
    exit();
}

$product_id = intval($_GET['id']);
$product_stmt = $conn->prepare("SELECT * FROM Product WHERE product_id = :product_id");
$product_stmt->execute([':product_id' => $product_id]);
$product = $product_stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    header("Location: product.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']); // Sanitize input
    $price = floatval($_POST['price']); // Ensure price is a float
    $category_id = intval($_POST['category_id']); // Ensure integer
    $status = $_POST['status'];

    // Validate inputs
    if (empty($name) || $price <= 0 || !$category_id) {
        $error = "Invalid input data";
    } else {
        // Handle file upload
        $product_image = $product['product_image'];
        if (isset($_FILES['product_picture']) && $_FILES['product_picture']['error'] == 0) {
            $target_dir = "uploads/product/";
            // Ensure directory exists
            if (!file_exists($target_dir)) {
                mkdir($target_dir, 0777, true);
            }
            
            $imageFileType = strtolower(pathinfo($_FILES["product_picture"]["name"], PATHINFO_EXTENSION));
            // Generate unique filename to avoid overwrites
            $new_filename = $target_dir . $name . '_' . uniqid() . '.' . $imageFileType;
            
            // Validate image
            $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
            if (in_array($imageFileType, $allowed_types) && getimagesize($_FILES["product_picture"]["tmp_name"])) {
                if (move_uploaded_file($_FILES["product_picture"]["tmp_name"], $new_filename)) {
                    $product_image = $new_filename;
                } else {
                    $error = "Failed to upload image";
                }
            } else {
                $error = "Invalid image format";
            }
        }

        if (!isset($error)) {
            try {
                $stmt = $conn->prepare("UPDATE Product SET product_name = :name, price = :price, f_category_id = :category_id, status = :status, product_image = :product_image WHERE product_id = :product_id");
                $stmt->execute([
                    'name' => $name,
                    'price' => $price,
                    'category_id' => $category_id,
                    'status' => $status,
                    'product_image' => $product_image,
                    'product_id' => $product_id
                ]);
                header("Location: product.php");
                exit();
            } catch (PDOException $e) {
                $error = "Database error: " . $e->getMessage();
            }
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Product</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        h2 { margin-top: 7.5rem !important; }
        input, select {
            border: 1px solid gray !important;
            padding: 10px !important;
        }
        input:focus, select:focus {
            border: 1px solid gold !important;
        }
    </style>
</head>
<body>
<?php require "includes/header.php"; ?>
<div class="container mt-5">
    <h2>Edit Product</h2>
    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>
    <form method="POST" class="mt-3" enctype="multipart/form-data">
        <div class="mb-3">
            <label for="name">Name</label>
            <input type="text" name="name" id="name" class="form-control" placeholder="Name" value="<?php echo htmlspecialchars($product['product_name']); ?>" required>
        </div>
        <div class="mb-3">
            <label for="price">Price</label>
            <input type="number" step="0.01" name="price" id="price" class="form-control" placeholder="Price" value="<?php echo htmlspecialchars($product['price']); ?>" required min="0">
        </div>
        <div class="mb-3">
            <label for="category_id">Category</label>
            <select name="category_id" id="category_id" class="form-control" required>
                <option value="">Select Category</option>
                <?php foreach ($categories as $category): ?>
                    <option value="<?php echo $category['category_id']; ?>" <?php echo ($product['f_category_id'] == $category['category_id']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($category['category_name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="mb-3">
            <label for="product_picture">Product Picture</label>
            <input type="file" id="product_picture" class="form-control" name="product_picture" accept="image/*">
            <?php if (!empty($product['product_image']) && file_exists($product['product_image'])): ?>
                <img src="<?php echo htmlspecialchars($product['product_image']); ?>" alt="Product Image" style="max-width: 100px; margin-top: 10px;">
            <?php endif; ?>
        </div>
        <div class="mb-3">
            <label for="status">Status</label>
            <select name="status" id="status" class="form-control" required>
                <option value="available" <?php echo ($product['status'] == 'available') ? 'selected' : ''; ?>>Available</option>
                <option value="unavailable" <?php echo ($product['status'] == 'unavailable') ? 'selected' : ''; ?>>Unavailable</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Edit Product</button>
    </form>
</div>
<?php require "includes/footer.php"; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
