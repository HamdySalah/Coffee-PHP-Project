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

$categories = $db->fetchAllCategories();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']); 
    $price = floatval($_POST['price']); 
    $category_id = intval($_POST['category_id']); 
    $status = $_POST['status'];


    if (empty($name) || $price <= 0 || !$category_id) {
        $error = "Invalid input data";
    } else {

        $product_image = null;
        if (isset($_FILES['product_picture']) && $_FILES['product_picture']['error'] == 0) {
            $target_dir = "uploads/product/";

            if (!file_exists($target_dir)) {
                mkdir($target_dir, 0777, true);
            }
            if ($_FILES["product_picture"]["size"] > 2 * 1024 * 1024) { // Corrected key here
                die("File is too large. Max size is 2MB.");
            }
            $imageFileType = strtolower(pathinfo($_FILES["product_picture"]["name"], PATHINFO_EXTENSION)); 
            $new_filename = $target_dir . $name . '_' . uniqid() . '.' . $imageFileType;
            $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
            if (in_array($imageFileType, $allowed_types) && getimagesize($_FILES["product_picture"]["tmp_name"])) { // Corrected key here
                if (move_uploaded_file($_FILES["product_picture"]["tmp_name"], $new_filename)) { // Corrected key here
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
                $db->insertProduct($name, $price, $category_id, $status, $product_image);
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
    <title>Add Product</title>
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
    <h2>Add Product</h2>
    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>
    <form method="POST" class="mt-3" enctype="multipart/form-data">
        <div class="mb-3">
            <label for="name">Name</label>
            <input type="text" name="name" id="name" class="form-control" placeholder="Name" required>
        </div>
        <div class="mb-3">
            <label for="price">Price</label>
            <input type="number" step="0.01" name="price" id="price" class="form-control" placeholder="Price" required min="0">
        </div>
        <div class="mb-3">
            <label for="category_id">Category</label>
            <select name="category_id" id="category_id" class="form-control" required>
                <option value="">Select Category</option>
                <?php foreach ($categories as $category): ?>
                    <option value="<?php echo $category['category_id']; ?>">
                        <?php echo htmlspecialchars($category['category_name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="mb-3">
            <label for="product_picture">Product Picture</label>
            <input type="file" id="product_picture" class="form-control" name="product_picture" accept="image/*">
        </div>
        <div class="mb-3">
            <label for="status">Status</label>
            <select name="status" id="status" class="form-control" required>
                <option value="available">Available</option>
                <option value="unavailable">Unavailable</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Add Product</button>
    </form>
</div>
<?php require "includes/footer.php"; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>