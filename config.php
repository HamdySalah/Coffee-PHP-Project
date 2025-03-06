<?php
//host
try {
    define("HOST", "localhost");
    //database
    define("DBNAME", "Cafeteria");
    //user
    define("USER", "root");
    //passwrd
    define("PASS", "");
    //port
    define("PORT", "3307");

    $conn = new PDO("mysql:host=" . HOST . ";port=" . PORT . ";dbname=" . DBNAME, USER, PASS);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo $e->getMessage();
}
// if($conn == true){
//     echo "Connection is fine";
// }
// else echo "Error";