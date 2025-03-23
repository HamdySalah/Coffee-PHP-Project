<?php
session_start();
require_once 'config.php';
require_once 'Database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 1) {
    header("Location: login.php");
    exit();
}

$db = new Database();
$error = isset($_SESSION['error']) ? $_SESSION['error'] : '';
unset($_SESSION['error']); 

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $room = $_POST['room'];
    $ext = trim($_POST['ext']);
    $profile_picture = null;
    if (empty($username) || empty($email) || empty($password) || empty($confirm_password) || empty($room) || empty($ext)) {
        $error = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format.";
    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match.";
    } else {
        if (isset($_FILES['pic']) && $_FILES['pic']['error'] == 0) {
            $target_dir = "uploads/users/";
            if (!file_exists($target_dir)) {
                mkdir($target_dir, 0777, true);
            }
            $imageFileType = strtolower(pathinfo($_FILES['pic']['name'], PATHINFO_EXTENSION));
            $new_filename = $target_dir . $username . '_' . uniqid() . '.' . $imageFileType;
            $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
            if (in_array($imageFileType, $allowed_types) && getimagesize($_FILES['pic']['tmp_name'])) {
                if (move_uploaded_file($_FILES['pic']['tmp_name'], $new_filename)) {
                    $profile_picture = $new_filename;
                } else {
                    $error = "Failed to upload profile picture.";
                }
            } else {
                $error = "Invalid image format.";
            }
        }
        if (!isset($error)) {
            try {
                $hashed_password = password_hash($password, PASSWORD_BCRYPT);
                $db->insertUser($username, $email, $hashed_password, $room, $ext, $profile_picture);
                $_SESSION['success'] = "User added successfully.";
                header("Location: users.php");
                exit();
            } catch (PDOException $e) {
                $error = "Database error: " . $e->getMessage();
            }
        }
    }
    $to = $_POST['email'];
    $subject = "Welcome to Coffee!";
    $message = "Dear " . htmlspecialchars($_POST['username']) . ",\n\nWelcome to Coffee! Your account has been successfully created.\n\nBest regards,\nCoffee Team";
    $headers = "From: no-reply@coffee.com";

    if (mail($to, $subject, $message, $headers)) {
        echo "<p class='text-success text-center'>User added successfully, and an email has been sent.</p>";
    } else {
        echo "<p class='text-danger text-center'>User added successfully, but the email could not be sent.</p>";
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
                    <form action="validate.php" method="POST" class="billing-form ftco-bg-dark p-4 p-md-5" enctype="multipart/form-data">
                        <h3 class="mb-4 billing-heading text-center">Add User</h3>
                        <?php if (!empty($error)): ?>
                            <p class="error"><?php echo $error; ?></p>
                        <?php endif; ?>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="username">Username</label>
                                    <input type="text" class="form-control" name="username" placeholder="Username" value="<?php echo isset($_SESSION['form_data']['username']) ? htmlspecialchars($_SESSION['form_data']['username']) : ''; ?>" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="email">Email</label>
                                    <input type="email" class="form-control" name="email" placeholder="Email" value="<?php echo isset($_SESSION['form_data']['email']) ? htmlspecialchars($_SESSION['form_data']['email']) : ''; ?>" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="pass">Password</label>
                                    <input type="password" class="form-control" name="password" placeholder="Password" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="cpass">Confirm Password</label>
                                    <input type="password" class="form-control" name="confirm_password" placeholder="Confirm Password" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="room">Room No</label>
                                    <select id="room" name="room" class="form-control" required>
                                        <?php 
                                        $rooms = $db->fetchAllRooms();
                                        foreach ($rooms as $roomOption) {
                                            $selected = (isset($_SESSION['form_data']['room']) && $_SESSION['form_data']['room'] == $roomOption['room_name']) ? 'selected' : '';
                                            echo "<option value='" . htmlspecialchars($roomOption['room_name']) . "' $selected>" . htmlspecialchars($roomOption['room_name']) . "</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="ext">EXT</label>
                                    <input type="text" class="form-control" name="ext" placeholder="EXT" value="<?php echo isset($_SESSION['form_data']['ext']) ? htmlspecialchars($_SESSION['form_data']['ext']) : ''; ?>" required>
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
    <?php unset($_SESSION['form_data']); // Clear form data after rendering ?>
</body>
</html>