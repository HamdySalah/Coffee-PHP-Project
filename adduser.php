<?php require "includes/header.php"; ?>
<head>
    <style>
        select.form-control {
            background-color: black;
            color: white;
        }
        select.form-control option {
            background-color: black;
            color: white;
        }
    </style>
</head>
<?php
require_once 'config.php';

if(!isset($_SESSION['user_id'])) {
    header("Location:login.php");
    exit();
}
elseif( $_SESSION['role'] != 1){
    header("Location:index.php");
    exit();
}
// if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 1) {
//     header("Location: ../public/index.php");
//     exit();
// }
$db = new Database();
$conn = $db->connect();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    echo "POST";
    $name = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['pass'];
    $Cpassword = $_POST['cpass'];
    $room = $_POST['room'];
    $ext = $_POST['ext'];


    $stmt = $conn->prepare("INSERT INTO User (user_name, email, password, ) VALUES (:name, :email, :password, :role)");
    $stmt->execute(['name' => $name, 'email' => $email, 'password' => $password, 'role' => $role]);
    header("Location: user.php");
    exit();
}
?>
<section class="ftco-section">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8 ftco-animate">
                <form action="#" method="POST" class="billing-form ftco-bg-dark p-4 p-md-5" enctype="multipart/form-data">
                    <h3 class="mb-4 billing-heading text-center">Add User</h3>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="Username">Username</label>
                                <input type="text" class="form-control" name="username" placeholder="Username" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="Email">Email</label>
                                <input type="email" class="form-control" name="email" placeholder="Email" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="Password">Password</label>
                                <input type="password" class="form-control" name="pass" placeholder="Password" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="CPassword">Confirm Password</label>
                                <input type="password" class="form-control" name="cpass" placeholder="Confirm Password" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="room">Room No</label>
                                <select id="room" name="room" class="form-control" required>
                                    <?php 
                                    $stmt = $conn->prepare('SELECT * FROM user_room');
                                    $stmt->execute();
                                    $rooms = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                    var_dump($rooms);
                                    foreach ($rooms as $room) {
                                        echo "<option value='".$room['room_name']."'>".$room['room_name']."</option>";
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
                                <input type="file" name="pic" class="form-control-file">
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