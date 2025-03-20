<?php
session_start();
require_once 'config.php';
require_once 'Database.php';

$db = new Database();
$conn = $db->connect();

// Get categories for filter dropdown
$categories = $db->fetchAllCategories();

// Prepare product query with search and filter
$products = $db->fetchProductsWithFilters($_GET['search'] ?? null, $_GET['category'] ?? null);

?>

<!DOCTYPE html>
<html>
<head>
    <title>Products</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .search-filter-container {
            background-color: #000;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .search-input {
            max-width: 300px;
            border: 2px solid gray !important;
            padding: 5px !important;
            border-radius: 5px !important;
            color: white;
        }
        .search-input:focus {
            border: 1px solid gold !important;
        }
        .filter-select {
            max-width: 200px;
        }
        h2 { margin-top: 7rem !important; }
        .card-img-top { object-fit: cover; height: 200px; }
        .btn.btn-primary{
            margin-top: 5px;
            /* background: #e9e054 !important; */
        }

        .pro-card {
            background: #1a1a1a !important;
            color: #fff;
            border-radius: 10px;
            overflow: hidden;
            transition: transform 0.3s;
            
        }
        .pro-card .card-title {

        font-size: 1.9rem;
        margin-bottom: 8px;
        margin-top: 18px;
        color: gold;
        font-weight: bold;
}


        .pro-card:hover {
            transform: translateY(-10px);
        }



    </style>
</head>
<body>
<?php require "includes/header.php"; ?>
<div class="container mt-5">
    <h2>Products</h2>
    <?php if($_SESSION['role'] == 1): ?>
    <a href="add_product.php" class="btn btn-success mb-3">Add New Product</a>
    <?php endif; ?>
    <div class="search-filter-container">
        <form method="GET" class="row g-3 align-items-end">
            <div class="col-auto">
                <label for="search" class="form-label text-white">Search by Name</label>
                <input type="text" class="form-control search-input" id="search" name="search"
                       value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>"
                       placeholder="Enter product name">
            </div>
            <div class="col-auto">
                <label for="category" class="form-label text-white">Filter by Category</label>
                <select class="form-select filter-select form-control" id="category" name="category">
                    <option value="all" <?php echo (!isset($_GET['category']) || $_GET['category'] === 'all') ? 'selected' : ''; ?>>All Categories</option>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?php echo $category['category_id']; ?>" <?php echo (isset($_GET['category']) && $_GET['category'] == $category['category_id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($category['category_name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-primary">Apply</button>
            </div>
        </form>
    </div>

    <div class="row">
        <?php if (empty($products)): ?>
            <p>No products found.</p>
        <?php else: ?>
            <?php foreach ($products as $product): ?>
                <div class="col-md-3 mb-3">
                    <div class="card pro-card">
                        <?php if (!empty($product['product_image']) && file_exists($product['product_image'])): ?>
                            <img src="<?php echo htmlspecialchars($product['product_image']); ?>" class="card-img-top" alt="Product Image">
                        <?php else: ?>
                            <img src="uploads/non.png" class="card-img-top" alt="Default Image">
                        <?php endif; ?>
                        <div class="card-body">
                            <h5 class="card-title">
                                <strong><?php echo htmlspecialchars($product['product_name']); ?></strong>
                            </h5>
                            <p class="card-text"><small style="color: #cec3c3;">Category:
                                <?php echo htmlspecialchars($product['category_name']); ?></small></p>
                            <h6 class="card-text">Price: <strong><?php echo number_format($product['price'], 2); ?> $</strong></h6>
                            <p class="card-text" style="color: <?php echo ($product['status'] === 'available' ? '#40e540' : 'red'); ?>">
                                Status: <?php echo htmlspecialchars($product['status']); ?>
                            </p>
                            <?php if($_SESSION['role'] == 1): ?>
                            <a href="edit_product.php?id=<?php echo $product['product_id']; ?>" class="btn btn-warning">Edit</a>
                            <a href="delete_product.php?product_id=<?php echo $product['product_id']; ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this product?');">Delete</a>
                            <?php endif; ?>
                            <?php if($product['status'] === 'available'): ?>
                            <br>  
                            <a href="addorder.php?id=<?php echo $product['product_id']; ?>" class="btn btn-primary">Order Now</a>
                            <?php else: ?>
                            <br>
                            <button class="btn btn-secondary" style="margin-top: 6px;" disabled>Unavailable</button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>
<?php require "includes/footer.php"; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>