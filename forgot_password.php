<?php
require_once 'config.php';
require_once 'Database.php';

$error = "";
$success = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];

    if (empty($email)) {
        $error = "Email is required.";
    } else {
        try {
            $db = new Database();
            $user = $db->fetchUserByEmail($email);
            if ($user) {
                // Generate a unique token
                $token = bin2hex(random_bytes(50));
                $db->storeResetToken($email, $token);

                // Send reset link to user's email
                $resetLink = "http://localhost/Coffee-PHP-Project/reset_password.php?token=" . $token;
                $subject = "Password Reset Request";
                $message = "Hello,\n\nClick the link below to reset your password:\n\n" . $resetLink . "\n\nIf you did not request this, please ignore this email.";
                $headers = "From: no-reply@localhost";

                if (mail($email, $subject, $message, $headers)) {
                    $success = "A password reset link has been sent to your email.";
                } else {
                    $error = "Failed to send email. Please try again.";
                }
            } else {
                $error = "No user found with that email address.";
            }
        } catch (PDOException $e) {
            $error = "Database error: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <title>Forgot Password</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="assets/css/style.css">
  </head>
  <body>
    <section class="ftco-section">
      <div class="container">
        <div class="row">
          <div class="col-md-12 ftco-animate">
            <form action="forgot_password.php" class="billing-form ftco-bg-dark p-3 p-md-5" method="post">
                <h3 class="mb-4 billing-heading">Forgot Password</h3>
                <?php if (!empty($error)): ?>
                    <p class="error"><?php echo $error; ?></p>
                <?php endif; ?>
                <?php if (!empty($success)): ?>
                    <p class="success"><?php echo $success; ?></p>
                <?php endif; ?>
                <div class="row align-items-end">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="Email">Email</label>
                            <input type="text" class="form-control" name="email" placeholder="Email">
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group mt-4">
                            <div class="radio">
                                <button class="btn btn-primary py-3 px-4">Send Reset Link</button>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12 text-center mt-4">
                        <a href="login.php" class="btn btn-secondary py-3 px-4">Back to Login</a>
                    </div>
                </div>
            </form>
          </div> 
        </div>
      </div>
    </section>
  </body>
</html>
