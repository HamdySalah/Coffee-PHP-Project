<?php
// session_start();
require_once 'config.php';

// if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 1) {
//     header("Location: ../public/index.php");
//     exit();
// }

$db = new Database();
$conn = $db->connect();

$categories = $conn->query("SELECT * FROM Category")->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $price = $_POST['price'];
    $category_id = $_POST['category_id'];
    $status = $_POST['status'];

    $stmt = $conn->prepare("INSERT INTO Product (product_name, price, f_category_id, status) VALUES (:name, :price, :category_id, :status)");
    $stmt->execute(['name' => $name, 'price' => $price, 'category_id' => $category_id, 'status' => $status]);
    header("Location: product.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Product</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<?php require "includes/header.php"; ?>
    <div class="container mt-5">
        <h2>Add Product</h2>
        <form method="POST" class="mt-3">
            <div class="mb-3">
                <label>Name</label>
                <input type="text" name="name" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>Price</label>
                <input type="number" step="0.01" name="price" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>Category</label>
                <select name="category_id" class="form-control" required>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?php echo $category['category_id']; ?>"><?php echo $category['category_name']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-3">
                <label>Status</label>
                <select name="status" class="form-control" required>
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