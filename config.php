<?php
    define('UPLOAD_DIR', 'uploads/');
    define('ALLOWED_FILE_TYPES', ['image/jpeg', 'image/png', 'image/gif']);
    define('MAX_FILE_SIZE', 2 * 1024 * 1024);
    define('SITE_NAME', 'Coffee-PHP-Project');
    define('BASE_URL', 'http://localhost/Coffee-PHP-Project/');
    class Config {
    #General Config To Remote DB for All Developers
    // private $host = "nozomi.proxy.rlwy.net";
    // private $dbname = "railway";
    // private $username = "root";
    // private $password = "hZhDzuMNFzuSYMkzkNkYTRjetBvRElRd"; 
    // private $port = "51811";
    // private $conn;

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
    // private $host = "localhost";
    // private $dbname = "cafeteria_db";
    // private $username = "admin";
    // private $password = "123"; 
    // private $conn;


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
                "mysql:host=" . $this->host .";port=".$this->port. ";dbname=" . $this->dbname,
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