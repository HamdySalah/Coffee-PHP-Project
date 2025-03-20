<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require_once 'config.php';
require_once 'Database.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 1) {
    header("Location: login.php");
    exit();
}
try {
    $db = new Database();//edit
    $conn = $db->connect();
    $users = $db->fetchAllUsersWithRooms();//edited
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
        h5{
            line-height: 4rem;
        }
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

            font-size: 1.9rem;
            margin-bottom: 8px;
            margin-top: 18px;
            color: gold;
            font-weight: bold;
        }
        .user-card .card-text {
            margin-bottom: 5px;
        }
        .debug {
            color: #f8d7da;
            font-size: 0.9rem;
        }
        strong{
            color :gray;
        }
        i{
            z-index: 11;
            top: 9px;
            right: 27px;
            font-size: 25px;
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
                    <a href="adduser.php" class="btn btn-primary py-3 px-4">Add New User</a>
                    <div class="position-relative">
                    <input type="text" id="searchInput" class="form-control mb-4 pe-5" placeholder="Search by name..." style="margin-bottom: 35px;">
                    <i class="bi bi-search position-absolute top-50 end-0 translate-middle-y me-3 text-white" style="color:white"></i>
                    </div>                    
                <?php if (empty($users)): ?>
                        <p class="text-center text-white">No users found.</p>
                    <?php else: ?>
                        <div class="row" id="userList">
                            <?php foreach ($users as $user): ?>
                                <div class="col-md-3 mb-3 user-card-container">
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
                    </div>
                </div>
            </div>
        </div>
    </section>

    <?php require "includes/footer.php"; ?>

    <script>
        document.getElementById('searchInput').addEventListener('keyup', function() {
            var searchValue = this.value.toLowerCase();
            var userCards = document.querySelectorAll('.user-card-container');

            userCards.forEach(function(card) {
                var userName = card.querySelector('.card-title').textContent.toLowerCase();
                if (userName.includes(searchValue)) {
                    card.style.display = '';
                } else {
                    card.style.display = 'none';
                }
            });
        });
    </script>
</body>
</html>