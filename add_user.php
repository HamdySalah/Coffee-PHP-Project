<?php
session_start();
require_once 'config.php';

// if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 1) {
//     header("Location: ../public/index.php");
//     exit();
// }

$db = new Database();
$conn = $db->connect();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $role = $_POST['role'];

    $stmt = $conn->prepare("INSERT INTO User (user_name, email, password, role) VALUES (:name, :email, :password, :role)");
    $stmt->execute(['name' => $name, 'email' => $email, 'password' => $password, 'role' => $role]);
    header("Location: user.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add User</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<?php require "includes/header.php"; ?>
    <div class="container mt-5">
        <h2>Add User</h2>
        <form method="POST" class="mt-3">
            <div class="mb-3">
                <label>Name</label>
                <input type="text" name="name" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>Email</label>
                <input type="email" name="email" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>Role</label>
                <select name="role" class="form-control" required>
                    <option value="0">User</option>
                    <option value="1">Admin</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Add User</button>
        </form>
    </div>
    <?php require "includes/footer.php"; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>