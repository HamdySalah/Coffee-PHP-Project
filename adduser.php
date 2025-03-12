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