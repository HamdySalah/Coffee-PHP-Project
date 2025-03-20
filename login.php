<?php
session_start();
require_once 'config.php';
require_once 'Database.php';

$error = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['logpass'];

    if (empty($email) || empty($password)) {
        $error = "Email and password are required.";
    } else {
        try {
            $db = new Database();
            $user = $db->fetchUserByEmail($email);

            // Debugging: Log fetched user data
            // Remove these logs in production
            error_log("Fetched user: " . print_r($user, true));

            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['user_name'] = $user['user_name'];
                $_SESSION['profile_picture'] = $user['profile_picture'];
                header("Location: index.php");
                exit();
            } else {
                // Debugging: Log password verification result
                error_log("Password verification failed for email: $email");
                $error = "Incorrect email or password.";
            }
        } catch (PDOException $e) {
            $error = "Database error: " . $e->getMessage();
        }
    }
}
// var_dump($_SESSION);echo "<br>";
// var_dump($_POST);echo "<br>";
// var_dump($user);echo "<br>";
// var_dump($error);echo "<br>";
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <title>Coffee - Free Bootstrap 4 Template by Colorlib</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    
    <link href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Josefin+Sans:400,700" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Great+Vibes" rel="stylesheet">

    <link rel="stylesheet" href="assets/css/open-iconic-bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/animate.css">
    <link rel="stylesheet" href="assets/css/owl.carousel.min.css">
    <link rel="stylesheet" href="assets/css/owl.theme.default.min.css">
    <link rel="stylesheet" href="assets/css/magnific-popup.css">
    <link rel="stylesheet" href="assets/css/aos.css">
    <link rel="stylesheet" href="assets/css/ionicons.min.css">
    <link rel="stylesheet" href="assets/css/bootstrap-datepicker.css">
    <link rel="stylesheet" href="assets/css/jquery.timepicker.css">
    <link rel="stylesheet" href="assets/css/flaticon.css">
    <link rel="stylesheet" href="assets/css/icomoon.css">
    <link rel="stylesheet" href="assets/css/style.css">
  </head>
  
<section class="ftco-section">
      <div class="container">
        <div class="row">
          <div class="col-md-12 ftco-animate">
            <form action="login.php" class="billing-form ftco-bg-dark p-3 p-md-5" method="post">
                <h3 class="mb-4 billing-heading">Login</h3>
                <?php if (!empty($error)): ?>
                    <p class="error" style="color: red;" ><?php echo $error; ?></p>
                <?php endif; ?>
                <div class="row align-items-end">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="Email">Email</label>
                            <input type="text" class="form-control" name="email" placeholder="Email">
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="Password">Password</label>
                            <input type="password" class="form-control" name="logpass" placeholder="Password">
                        </div>
                        <div class="form-group">
                            <a href="forgot_password.php" class="text-light">Forgot Password?</a>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group mt-4">
                            <div class="radio">
                                <button class="btn btn-primary py-3 px-4">Login</button>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12 text-center mt-4">
                        <a href="index.php" class="btn btn-secondary py-3 px-4">Go to Home</a>
                    </div>
                </div>
            </form>
          </div> 
        </div>
      </div>
    </section>
<?php require "includes/footer.php"; ?>