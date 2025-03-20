<?php
require_once 'database.php';

$db = new Database();

$admins = $db->fetchAllUsers("SELECT user_id, password FROM user WHERE role = '1'");

foreach ($admins as $admin) {
    $id = $admin['user_id'];
    $plainPassword = $admin['password'];
    if (!password_get_info($plainPassword)['algo']) { // check if it's already hashed or not
        $hashedPassword = password_hash($plainPassword, PASSWORD_DEFAULT);
        try {
            $db->execute("UPDATE user SET password = ? WHERE user_id = ?", [$hashedPassword, $id]);
            echo "Updated password for admin ID: $id <br>";
        } catch (Exception $e) {
            echo "Error updating password for admin ID: $id - " . $e->getMessage() . "<br>";
        }
    } else {
        echo "Password for admin ID: $id is already hashed.<br>";
    }
}

echo "Password hashing complete!";
?>
