<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 1) {
    header("Location: login.php");
    exit();
}

try {
    $db = new Config();
    $conn = $db->connect();

    $stmt = $conn->prepare("
        SELECT u.user_id, u.user_name, u.email, u.ext, u.profile_picture, 
               GROUP_CONCAT(ur.room_name SEPARATOR ', ') AS rooms
        FROM User u
        LEFT JOIN user_room ur ON u.user_id = ur.user_id
        GROUP BY u.user_id, u.user_name, u.email, u.ext, u.profile_picture
        ORDER BY u.user_id DESC
    ");
    $stmt->execute();
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Database Error: " . $e->getMessage());
} catch (Exception $e) {
    die("General Error: " . $e->getMessage());
}

// Base path for images (adjust if not in /php_project/)
$base_path = '/php_project/'; // Set to '' if at web root (e.g., /var/www/html/)
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Users - Coffee</title>
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
    <style>
        .user-card {
            background: #1a1a1a;
            color: #fff;
            border-radius: 10px;
            overflow: hidden;
            transition: transform 0.3s;
        }
        .user-card:hover {
            transform: translateY(-10px);
        }
        .user-card img {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }
        .user-card .card-body {
            padding: 20px;
        }
        .user-card .card-title {
            font-size: 1.5rem;
            margin-bottom: 10px;
        }
        .user-card .card-text {
            margin-bottom: 5px;
        }
        .debug {
            color: #f8d7da;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>
    <?php require "includes/header.php"; ?>

    <section class="ftco-section">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-12 ftco-animate">
                    <h3 class="mb-4 billing-heading text-center">All Users</h3>
                    <?php if (empty($users)): ?>
                        <p class="text-center text-white">No users found.</p>
                    <?php else: ?>
                        <div class="row">
                            <?php foreach ($users as $user): ?>
                                <div class="col-md-4 mb-4">
                                    <div class="user-card">
                                        <?php
                                        $profile_picture = $user['profile_picture'];
                                        $absolute_path = realpath(dirname(__FILE__)) . '/' . $profile_picture;
                                        if ($profile_picture && file_exists($absolute_path)): ?>
                                            <img src="<?php echo htmlspecialchars($base_path . $profile_picture); ?>" alt="<?php echo htmlspecialchars($user['user_name']); ?>">
                                        <?php else: ?>
                                            <img src="assets/images/default-user.jpg" alt="Default User">
                                            <p class="debug">Profile Picture: <?php echo htmlspecialchars($profile_picture ?: 'None'); ?> - Exists: <?php echo file_exists($absolute_path) ? 'Yes' : 'No'; ?></p>
                                        <?php endif; ?>
                                        <div class="card-body">
                                            <h5 class="card-title"><?php echo htmlspecialchars($user['user_name']); ?></h5>
                                            <p class="card-text"><strong>ID:</strong> <?php echo $user['user_id']; ?></p>
                                            <p class="card-text"><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
                                            <p class="card-text"><strong>Rooms:</strong> <?php echo htmlspecialchars($user['rooms'] ?: 'None'); ?></p>
                                            <p class="card-text"><strong>EXT:</strong> <?php echo htmlspecialchars($user['ext'] ?: 'N/A'); ?></p>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                    <div class="text-center mt-4">
                        <a href="index.php" class="btn btn-secondary py-3 px-4">Back to Home</a>
                        <a href="adduser.php" class="btn btn-primary py-3 px-4">Add New User</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <?php require "includes/footer.php"; ?>

    <script src="assets/js/jquery.min.js"></script>
    <script src="assets/js/jquery-migrate-3.0.1.min.js"></script>
    <script src="assets/js/popper.min.js"></script>
    <script src="assets/js/bootstrap.min.js"></script>
    <script src="assets/js/jquery.easing.1.3.js"></script>
    <script src="assets/js/jquery.waypoints.min.js"></script>
    <script src="assets/js/jquery.stellar.min.js"></script>
    <script src="assets/js/owl.carousel.min.js"></script>
    <script src="assets/js/jquery.magnific-popup.min.js"></script>
    <script src="assets/js/aos.js"></script>
    <script src="assets/js/jquery.animateNumber.min.js"></script>
    <script src="assets/js/bootstrap-datepicker.js"></script>
    <script src="assets/js/jquery.timepicker.min.js"></script>
    <script src="assets/js/scrollax.min.js"></script>
    <script src="assets/js/main.js"></script>
</body>
</html>