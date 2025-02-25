<?php
//host
try {
    define("HOST", "localhost");
    //database
    define("DBNAME", "cafeteria");
    //user
    define("USER", "root");
    //passwrd
    define("PASS", "");

    $conn = new PDO("mysql:host=" . HOST . ";dbname=" . DBNAME . "", USER, PASS);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo $e->getMessage();
}
// if($conn == true){
//     echo "Connection is fine";
// }
// else echo "Error";