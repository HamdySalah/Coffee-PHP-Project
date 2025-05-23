<?php
require_once 'config.php';
require_once 'Database.php';

$error = "";
$success = "";

// Generate CSRF token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $token = filter_var(trim($_POST['token']), FILTER_SANITIZE_STRING);
    $new_password = trim($_POST['new_password']);
    $confirm_password = trim($_POST['confirm_password']);

    if (empty($token) || empty($new_password) || empty($confirm_password)) {
        $error = "All fields are required.";
    } elseif (!preg_match('/^[a-f0-9]{100}$/', $token)) {
        $error = "Invalid token format.";
    } elseif ($_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $error = "Invalid CSRF token.";
    } elseif ($new_password !== $confirm_password) {
        $error = "Passwords do not match.";
    } else {
        try {
            $db = new Database();
            $email = $db->fetchEmailByToken($token);
            if ($email) {
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                $db->updatePassword($email, $hashed_password);
                $db->deleteResetToken($token);
                $success = "Your password has been reset successfully.";
            } else {
                $error = "The reset token is invalid or has expired. Please request a new password reset.";
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
    <title>Reset Password</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="assets/css/style.css">
  </head>
  <body>
    <section class="ftco-section">
      <div class="container">
        <div class="row">
          <div class="col-md-12 ftco-animate">
            <form action="reset_password.php" class="billing-form ftco-bg-dark p-3 p-md-5" method="post">
                <h3 class="mb-4 billing-heading">Reset Password</h3>
                <?php if (!empty($error)): ?>
                    <p class="error"><?php echo $error; ?></p>
                <?php endif; ?>
                <?php if (!empty($success)): ?>
                    <p class="success"><?php echo $success; ?></p>
                <?php endif; ?>
                <div class="row align-items-end">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="New Password">New Password</label>
                            <input type="password" class="form-control" name="new_password" placeholder="New Password">
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="Confirm Password">Confirm Password</label>
                            <input type="password" class="form-control" name="confirm_password" placeholder="Confirm Password">
                        </div>
                    </div>
                    <input type="hidden" name="token" value="<?php echo $_GET['token']; ?>">
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                    <div class="col-md-12">
                        <div class="form-group mt-4">
                            <div class="radio">
                                <button class="btn btn-primary py-3 px-4">Reset Password</button>
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
