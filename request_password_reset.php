<?php
require_once 'config.php';
require_once 'Database.php';

$error = "";
$success = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = filter_var(trim($_POST['email']), FILTER_VALIDATE_EMAIL);

    if (!$email) {
        $error = "Please enter a valid email address.";
    } else {
        try {
            $db = new Database();
            $user = $db->fetchUserByEmail($email);

            if ($user) {
                $token = bin2hex(random_bytes(50));
                $db->storeResetToken($email, $token);

                $reset_link = "http://localhost/Coffee-PHP-Project/reset_password.php?token=" . $token;
                mail($email, "Password Reset Request", "Click this link to reset your password: $reset_link");

                $success = "A password reset link has been sent to your email.";
            } else {
                $error = "No account found with that email address.";
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
    <title>Request Password Reset</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="assets/css/style.css">
  </head>
  <body>
    <section class="ftco-section">
      <div class="container">
        <div class="row">
          <div class="col-md-12 ftco-animate">
            <form action="request_password_reset.php" class="billing-form ftco-bg-dark p-3 p-md-5" method="post">
                <h3 class="mb-4 billing-heading">Request Password Reset</h3>
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
                            <input type="email" class="form-control" name="email" placeholder="Enter your email">
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group mt-4">
                            <div class="radio">
                                <button class="btn btn-primary py-3 px-4">Send Reset Link</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
          </div> 
        </div>
      </div>
    </section>
  </body>
</html>
