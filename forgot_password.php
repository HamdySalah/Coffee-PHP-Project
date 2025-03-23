<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'config.php';
require_once 'Database.php';

session_start();

// Generate CSRF token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$error = "";
$success = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);

    if (empty($email)) {
        $error = "Email is required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format.";
    } elseif ($_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $error = "Invalid CSRF token.";
    } else {
        try {
            $db = new Database();
            $user = $db->fetchUserByEmail($email);

            // Debugging: Log user data
            error_log("Fetched user: " . print_r($user, true));

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
            // Debugging: Log database error
            error_log("Database error: " . $e->getMessage());
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
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
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
