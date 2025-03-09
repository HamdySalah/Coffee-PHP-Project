<?php 
if (session_status() == PHP_SESSION_NONE) {
  session_start();
}

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
    <style>
      .navbar-nav {
        margin: 0 auto;
      }
      .user-profile {        
        display: flex;
        align-items: center;
      }
      .user-profile img {
        border-radius: 50%;
        width: 40px;
        height: 40px;
        margin-right: 10px;
      }
      .dropdown-menu {
        right: 0;
        left: auto;
      }
    </style>
  </head>
  <body>
  <?php
        $current_page = basename($_SERVER['PHP_SELF']);
      ?>
      <nav class="navbar navbar-expand-lg navbar-dark ftco_navbar bg-dark ftco-navbar-light" id="ftco-navbar">
        <div class="container">
          <a class="navbar-brand" href="index.php">Coffee<small>Blend</small></a>
          <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#ftco-nav" aria-controls="ftco-nav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="oi oi-menu">Menu</span> 
          </button>
          <div class="collapse navbar-collapse" id="ftco-nav">
            <ul class="navbar-nav mx-auto">
              <?php if($_SESSION['role'] == 1): ?>
              <li class="nav-item <?php echo $current_page == 'index.php' ? 'active' : ''; ?>"><a href="index.php" class="nav-link">Home</a></li>
              <li class="nav-item <?php echo $current_page == 'product.php' ? 'active' : ''; ?>"><a href="product.php" class="nav-link">Products</a></li>
              <li class="nav-item <?php echo $current_page == 'user.php' ? 'active' : ''; ?>"><a href="user.php" class="nav-link">Users</a></li>
              <li class="nav-item <?php echo $current_page == 'admin_orders.php' ? 'active' : ''; ?>"><a href="admin_orders.php" class="nav-link">Manuel Order</a></li>
              <li class="nav-item <?php echo $current_page == 'checks.php' ? 'active' : ''; ?>"><a href="checks.php" class="nav-link">Checks</a></li>
              <?php else: ?>
                <li class="nav-item <?php echo $current_page == 'index.php' ? 'active' : ''; ?>"><a href="index.php" class="nav-link">Home</a></li>
                <li class="nav-item <?php echo $current_page == 'user_orders.php' ? 'active' : ''; ?>"><a href="user_orders.php" class="nav-link">My Orders</a></li>
                <li class="nav-item <?php echo $current_page == 'about.php' ? 'active' : ''; ?>"><a href="about.php" class="nav-link">About</a></li>
              <?php endif; ?>
            </ul>
            <div class="user-profile">
              <img src="uploads/pic3.jpg" alt="User Profile Picture">
              <div class="dropdown">
                <a style="font-family: 'Great Vibes', cursive;" class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                  <?php echo $_SESSION['user_name']; ?>
                </a>
                <div class="dropdown-menu" aria-labelledby="userDropdown">
                  <a class="dropdown-item" href="#"><?php echo $_SESSION['user_name']; ?></a>
                  <a class="dropdown-item" href="logout.php">Logout</a>
                </div>
              </div>
            </div>
          </div>
        </div>
      </nav>