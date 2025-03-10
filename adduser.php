<?php
session_start();
require_once 'config.php';
require_once 'Database.php';

define('UPLOAD_DIR', 'uploads/');
define('ALLOWED_FILE_TYPES', ['image/jpeg', 'image/png', 'image/gif']);
define('MAX_FILE_SIZE', 2 * 1024 * 1024);
define('SITE_NAME', 'Coffee-PHP-Project');
define('BASE_URL', 'http://localhost/Coffee-PHP-Project/');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 1) {
    header("Location: login.php");
    exit();
}

$db = new Database();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['pass'];
    $cpassword = $_POST['cpass'];
    $room = $_POST['room'];
    $ext = $_POST['ext'];

    if ($password !== $cpassword) {
        $error = "Passwords do not match!";
    } else {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $profile_picture = null;
        if (isset($_FILES['pic']) && $_FILES['pic']['error'] == UPLOAD_ERR_OK) {
            $upload_dir = 'uploads/users/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            $profile_picture = $upload_dir . uniqid() . '-' . basename($_FILES['pic']['name']);
            if (move_uploaded_file($_FILES['pic']['tmp_name'], $profile_picture)) {
                echo "File uploaded successfully to: " . $profile_picture; // Debug
            } else {
                $error = "Failed to move uploaded file.";
            }
        }
        try {
            $db->insertUser($name, $email, $hashed_password, $room, $ext, $profile_picture);
            header("Location: user.php");
            exit();
        } catch (PDOException $e) {
            $error = "Error adding user: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Add User - Coffee</title>
    <style>
        select.form-control {
            background-color: black;
            color: white;
        }
        select.form-control option {
            background-color: black;
            color: white;
        }
        .error {
            color: #f8d7da;
            text-align: center;
        }
    </style>
</head>
<body>
    <?php require "includes/header.php"; ?>

    <section class="ftco-section">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8 ftco-animate">
                    <form action="adduser.php" method="POST" class="billing-form ftco-bg-dark p-4 p-md-5" enctype="multipart/form-data">
                        <h3 class="mb-4 billing-heading text-center">Add User</h3>
                        <?php if (isset($error)): ?>
                            <p class="error"><?php echo $error; ?></p>
                        <?php endif; ?>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="username">Username</label>
                                    <input type="text" class="form-control" name="username" placeholder="Username" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="email">Email</label>
                                    <input type="email" class="form-control" name="email" placeholder="Email" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="pass">Password</label>
                                    <input type="password" class="form-control" name="pass" placeholder="Password" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="cpass">Confirm Password</label>
                                    <input type="password" class="form-control" name="cpass" placeholder="Confirm Password" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="room">Room No</label>
                                    <select id="room" name="room" class="form-control" required>
                                        <?php 
                                        $rooms = $db->fetchAllRooms();
                                        foreach ($rooms as $room) {
                                            echo "<option value='" . htmlspecialchars($room['room_name']) . "'>" . htmlspecialchars($room['room_name']) . "</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="ext">EXT</label>
                                    <input type="text" class="form-control" name="ext" placeholder="EXT" required>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="pic">Profile Picture</label>
                                    <input type="file" name="pic" class="form-control-file" accept="image/*">
                                </div>
                            </div>
                            <div class="col-md-12 text-center">
                                <div class="form-group mt-4">
                                    <button type="submit" name="submit" class="btn btn-primary py-3 px-4">Save</button>
                                    <button type="reset" name="reset" class="btn btn-secondary py-3 px-4">Reset</button>
                                </div>
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