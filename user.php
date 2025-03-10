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

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Users - Coffee</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    
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
<?php require "includes/header.php"; ?>
<body>

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
                                            <img src="<?php echo htmlspecialchars(BASE_URL . $profile_picture); ?>" alt="<?php echo htmlspecialchars($user['user_name']); ?>">
                                        <?php else: ?>
                                            <img src="<?php echo BASE_URL; ?>uploads/img_avatar.png" alt="Default User">
                                        <?php endif; ?>
                                        <div class="card-body">
                                            <h5 class="card-title"><?php echo htmlspecialchars($user['user_name']); ?></h5>
                                            <p class="card-text"><strong>ID:</strong> <?php echo $user['user_id']; ?></p>
                                            <p class="card-text"><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
                                            <p class="card-text"><strong>Rooms:</strong> <?php echo htmlspecialchars($user['rooms'] ?: 'None'); ?></p>
                                            <p class="card-text"><strong>EXT:</strong> <?php echo htmlspecialchars($user['ext'] ?: 'N/A'); ?></p>
                                            <a href="edit_user.php?id=<?php echo $user['user_id']; ?>" class="btn btn-warning">Update</a>
                                            <a href="delete_user.php?id=<?php echo $user['user_id']; ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this user?');">Delete</a>
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

</body>
</html>