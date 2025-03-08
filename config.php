<?php
class Database {
    #Mostafa Config
    // private $host = "localhost";
    // private $dbname = "Cafeteria";
    // private $username = "root";
    // private $password = "123456Mh*"; 
    // private $conn;      
    
    # Hamdy Config
    private $host = "localhost";
    private $dbname = "cafeteria"; #cafeteria
    private $username = "root";
    private $password = ""; 
    private $port = "3307"; 
    private $conn;
       

    // omar confg
    /*private $host = "localhost";
    private $dbname = "cafeteria_db";
    private $username = "admin";
    private $password = "123"; 
    private $conn;*/


    //yasmeen confg
    // private $host = "localhost";
    // private $dbname = "cafeteria";
    // private $username = "root";
    // private $password = ""; 
    // private $conn;

    public function connect() {
        $this->conn = null;
        try {
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";port=" . $this->port . ";dbname=" . $this->dbname,
                $this->username,
                $this->password
            );
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            echo "Connection Error: " . $e->getMessage();
        }
        return $this->conn;
    }
}
?>