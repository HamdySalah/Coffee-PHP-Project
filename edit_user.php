<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 1) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        $db = new Config();
        $conn = $db->connect();

        $stmt = $conn->prepare("UPDATE User SET user_name = :user_name, email = :email, ext = :ext WHERE user_id = :user_id");
        $stmt->bindParam(':user_name', $_POST['user_name']);
        $stmt->bindParam(':email', $_POST['email']);
        $stmt->bindParam(':ext', $_POST['ext']);
        $stmt->bindParam(':user_id', $_POST['user_id']);
        $stmt->execute();

        header("Location: user.php");
        exit();
    } catch (PDOException $e) {
        die("Database Error: " . $e->getMessage());
    } catch (Exception $e) {
        die("General Error: " . $e->getMessage());
    }
} else {
    try {
        $db = new Config();
        $conn = $db->connect();

        $stmt = $conn->prepare("SELECT * FROM User WHERE user_id = :user_id");
        $stmt->bindParam(':user_id', $_GET['id']);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        die("Database Error: " . $e->getMessage());
    } catch (Exception $e) {
        die("General Error: " . $e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Edit User - Coffee</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
</head>
<body>
    <?php require "includes/header.php"; ?>
    <div class="container">
        <h3 class="mb-4">Edit User</h3>
        <form method="POST" action="edit_user.php">
            <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($user['user_id']); ?>">
            <div class="form-group">
                <label for="user_name">User Name</label>
                <input type="text" class="form-control" id="user_name" name="user_name" value="<?php echo htmlspecialchars($user['user_name']); ?>" required>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
            </div>
            <div class="form-group">
                <label for="ext">EXT</label>
                <input type="text" class="form-control" id="ext" name="ext" value="<?php echo htmlspecialchars($user['ext']); ?>">
            </div>
            <button type="submit" class="btn btn-primary">Update</button>
        </form>
    </div>
    <?php require "includes/footer.php"; ?>
</body>
</html>
