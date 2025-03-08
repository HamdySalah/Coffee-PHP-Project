<?php
require_once 'config.php';
require_once 'Database.php';

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize inputs
    $name = htmlspecialchars($_POST["name"]);
    $email = htmlspecialchars($_POST["email"]);
    $password = $_POST["password"];
    $confirmPassword = $_POST["confirm_password"];
    $room = htmlspecialchars($_POST["room"]);
    $ext = htmlspecialchars($_POST["ext"]);

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

    if (empty($error)) {
        // Insert user if no errors
        $db->insertUser($name, $email, $password, $room, $ext);
        header("Location: user.php");
        exit();
    }
}
?>