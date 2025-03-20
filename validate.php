<?php
session_start();
require_once 'config.php';
require_once 'Database.php';

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize inputs
    $name = htmlspecialchars($_POST["username"]);
    $email = htmlspecialchars($_POST["email"]);
    $password = $_POST["password"];
    $confirmPassword = $_POST["confirm_password"];
    $room = htmlspecialchars($_POST["room"]);
    $ext = htmlspecialchars($_POST["ext"]);

    // Store form data in session to repopulate the form if validation fails
    $_SESSION['form_data'] = [
        'username' => $name,
        'email' => $email,
        'room' => $room,
        'ext' => $ext
    ];

    $db = new Database();
    if (empty($name)) {
        $error .= "Name is required.<br>";
    }

    $existingUser = $db->fetchUserByEmail($email);
    if ($existingUser) {
        $error .= "Email already exists.<br>";
    }

    if (empty($email)) {
        $error .= "Email is required.<br>";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error .= "Invalid email format.<br>";
    }

    if (empty($password)) {
        $error .= "Password is required.<br>";
    } elseif (strlen($password) < 8) {
        $error .= "Password must be at least 8 characters long.<br>";
    }

    if ($password !== $confirmPassword) {
        $error .= "Passwords do not match.<br>";
    }

    if (empty($room)) {
        $error .= "Room is required.<br>";
    }

    if (empty($ext)) {
        $error .= "EXT is required.<br>";
    }

    // Handle file upload if no errors so far
    $profile_picture = null;
    if (empty($error) && isset($_FILES['pic']) && $_FILES['pic']['error'] == UPLOAD_ERR_OK) {
        $upload_dir = 'uploads/users/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        $profile_picture = $upload_dir . uniqid() . '-' . basename($_FILES['pic']['name']);
        if (!move_uploaded_file($_FILES['pic']['tmp_name'], $profile_picture)) {
            $error .= "Failed to move uploaded file.<br>";
        }
    }

    if (empty($error)) {
        // Hash the password before storing it
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        // Insert user if no errors
        try {
            $db->insertUser($name, $email, $hashedPassword, $room, $ext, $profile_picture);
            $_SESSION['profile_picture'] = $profile_picture; // Store image in session
            unset($_SESSION['form_data']); // Clear form data on success
            header("Location: user.php");
            exit();
        } catch (PDOException $e) {
            $error .= "Error adding user: " . $e->getMessage() . "<br>";
        }
    }

    // If there are errors, store them in the session and redirect back to adduser.php
    if (!empty($error)) {
        $_SESSION['error'] = $error;
        header("Location: adduser.php");

        exit();
    }
    // if($_POST['submit']){
    //     mail($_POST['email'],"My subject",$msg);
    // }
}
?>