<?php
require_once 'config.php';

class Database extends Config {

    public function fetchAllUsers() {
        $stmt = $this->connect()->query("SELECT * FROM User");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function fetchAllProducts() {
        $stmt = $this->connect()->query("SELECT * FROM Product");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function fetchAllOrders() {
        $stmt = $this->connect()->query("SELECT * FROM Orders");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function fetchUserById($user_id) {
        $stmt = $this->connect()->prepare("SELECT * FROM User WHERE user_id = :user_id");
        $stmt->execute(['user_id' => $user_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function fetchUserByEmail($email) {
        $stmt = $this->connect()->prepare("SELECT * FROM User WHERE email = :email");
        $stmt->execute(['email' => $email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function fetchProductById($product_id) {
        $stmt = $this->connect()->prepare("SELECT * FROM Product WHERE product_id = :product_id");
        $stmt->execute(['product_id' => $product_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function fetchOrderById($order_id) {
        $stmt = $this->connect()->prepare("SELECT * FROM Orders WHERE order_id = :order_id");
        $stmt->execute(['order_id' => $order_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public function fetchAllRooms() {
        $stmt = $this->connect()->query("SELECT * FROM user_room");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function insertUser($name, $email, $password, $room, $ext, $profile_picture, $role = 0) {
        $conn = $this->connect();
        try {
            $conn->beginTransaction();
            $stmt = $conn->prepare("
                INSERT INTO User (user_name, email, password, ext, profile_picture, role)
                VALUES (:name, :email, :password, :ext, :profile_picture, :role)
            ");
            $stmt->execute([
                ':name' => $name,
                ':email' => $email,
                ':password' => $password,
                ':ext' => $ext,
                ':profile_picture' => $profile_picture,
                ':role' => $role
            ]);
            $user_id = $conn->lastInsertId();
            $stmt = $conn->prepare("
                INSERT INTO user_room (user_id, room_name)
                VALUES (:user_id, :room_name)
            ");
            $stmt->execute([
                ':user_id' => $user_id,
                ':room_name' => $room
            ]);
            $conn->commit();
        } catch (PDOException $e) {
            $conn->rollBack();
            throw $e;
        }
    }
    public function fetchEmailByToken($token) {
        $stmt = $this->connect()->prepare("SELECT email FROM password_resets WHERE token = :token");
        $stmt->execute(['token' => $token]);
        return $stmt->fetchColumn();
    }

    public function updatePassword($email, $hashed_password) {
        $stmt = $this->connect()->prepare("UPDATE User SET password = :password WHERE email = :email");
        $stmt->execute(['password' => $hashed_password, 'email' => $email]);
    }

    public function deleteResetToken($token) {
        $stmt = $this->connect()->prepare("DELETE FROM password_resets WHERE token = :token");
        $stmt->execute(['token' => $token]);
    }

    public function storeResetToken($email, $token) {
        $stmt = $this->connect()->prepare("INSERT INTO password_resets (email, token) VALUES (:email, :token)");
        $stmt->execute(['email' => $email, 'token' => $token]);
    }


    // new functions
    
    public function fetchAllUsersWithRooms() {
        $stmt = $this->connect()->prepare("
        SELECT u.user_id, u.user_name, u.email, u.ext, u.profile_picture, 
               GROUP_CONCAT(ur.room_name SEPARATOR ', ') AS rooms
        FROM User u
        LEFT JOIN user_room ur ON u.user_id = ur.user_id
        WHERE u.role = 0
        GROUP BY u.user_id, u.user_name, u.email, u.ext, u.profile_picture
        ORDER BY u.user_id DESC
    ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    public function fetchAllCategories() {
        $stmt = $this->connect()->query("SELECT * FROM Category");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    
    public function fetchProductsWithFilters($search = null, $category = null) {
    $query = "SELECT p.*, c.category_name FROM Product p JOIN Category c ON p.f_category_id = c.category_id WHERE 1=1";
    $params = [];
    if ($search) {
        $query .= " AND p.product_name LIKE :search";
        $params[':search'] = '%' . $search . '%';
    }
    if ($category && $category !== 'all') {
        $query .= " AND p.f_category_id = :category";
        $params[':category'] = $category;
    }
    $query .= " ORDER BY p.product_id DESC";
    $stmt = $this->connect()->prepare($query);
    $stmt->execute($params);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function fetchOrdersWithFilters($user_id, $filter_date = null) {
        $where_clause = "WHERE o.f_user_id = :user_id";
        $params = [':user_id' => $user_id];
        if ($filter_date) {
            $where_clause .= " AND DATE(o.order_date) = :filter_date";
            $params[':filter_date'] = $filter_date;
        }
        $query = "
            SELECT o.order_id, o.order_date, o.status,
                   GROUP_CONCAT(CONCAT(p.product_name, ' (', op.quntity, ' x $', p.price, ')') SEPARATOR ', ') AS products,
                   SUM(op.quntity) AS total_quantity,
                   SUM(op.quntity * p.price) AS total_price
            FROM Orders o
            LEFT JOIN Order_product op ON o.order_id = op.f_order_id
            LEFT JOIN Product p ON op.f_product_id = p.product_id
            $where_clause
            GROUP BY o.order_id, o.order_date, o.status
            ORDER BY o.order_id DESC
        ";
        $stmt = $this->connect()->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    public function updateProduct($product_id, $name, $price, $category_id, $status, $product_image) {
        $stmt = $this->connect()->prepare("
            UPDATE Product 
            SET product_name = :name, 
                price = :price, 
                f_category_id = :category_id, 
                status = :status, 
                product_image = :product_image 
            WHERE product_id = :product_id
        ");
        $stmt->execute([
            ':name' => $name,
            ':price' => $price,
            ':category_id' => $category_id,
            ':status' => $status,
            ':product_image' => $product_image,
            ':product_id' => $product_id
        ]);
    }

    public function insertProduct($name, $price, $category_id, $status, $product_image) {
        $stmt = $this->connect()->prepare("
            INSERT INTO Product (product_name, price, f_category_id, status, product_image) 
            VALUES (:name, :price, :category_id, :status, :product_image)
        ");
        $stmt->execute([
            ':name' => $name,
            ':price' => $price,
            ':category_id' => $category_id,
            ':status' => $status,
            ':product_image' => $product_image
        ]);
    }
    public function insertOrder($order_date, $status, $f_user_id, $order_notes) {
        $stmt = $this->connect()->prepare("
            INSERT INTO Orders (order_date, status, f_user_id, order_notes) 
            VALUES (:order_date, :status, :f_user_id, :order_notes)
        ");
        $stmt->execute([
            ':order_date' => $order_date,
            ':status' => $status,
            ':f_user_id' => $f_user_id,
            ':order_notes' => $order_notes
        ]);
        return $this->connect()->lastInsertId();
    }

    public function insertOrderProducts($order_id, $product_quantities) {
        $stmt = $this->connect()->prepare("
            INSERT INTO Order_product (f_order_id, f_product_id, quntity) 
            VALUES (:order_id, :product_id, :quantity)
        ");
        $inserted_products = 0;
        foreach ($product_quantities as $product_id => $quantity) {
            $stmt->execute([
                ':order_id' => $order_id,
                ':product_id' => $product_id,
                ':quantity' => $quantity
            ]);
            $inserted_products++;
        }
        return $inserted_products;
    }

    public function fetchFilteredOrders($filters = []) {
        $where_clause = "";
        $params = [];
    
        if (!empty($filters['filter_date'])) {
            $where_clause .= " WHERE DATE(o.order_date) = :filter_date";
            $params[':filter_date'] = $filters['filter_date'];
        }
    
        if (!empty($filters['filter_user'])) {
            $where_clause .= $where_clause ? " AND" : " WHERE";
            $where_clause .= " (u.user_name LIKE :filter_user OR u.user_id = :filter_user_id)";
            $params[':filter_user'] = "%" . $filters['filter_user'] . "%";
            $params[':filter_user_id'] = (int)$filters['filter_user'];
        }
    
        if (!empty($filters['filter_status'])) {
            $where_clause .= $where_clause ? " AND" : " WHERE";
            $where_clause .= " o.status = :filter_status";
            $params[':filter_status'] = $filters['filter_status'];
        }
    
        $query = "
            SELECT o.order_id, o.order_date, u.user_name, o.status,
                   GROUP_CONCAT(CONCAT(p.product_name, ' (', op.quntity, ' x $', p.price, ')') SEPARATOR ', ') AS products,
                   SUM(op.quntity) AS total_quantity,
                   SUM(op.quntity * p.price) AS total_price
            FROM Orders o
            JOIN User u ON o.f_user_id = u.user_id
            LEFT JOIN Order_product op ON o.order_id = op.f_order_id
            LEFT JOIN Product p ON op.f_product_id = p.product_id
            $where_clause
            GROUP BY o.order_id, o.order_date, o.status, u.user_name
        ";
    
        $stmt = $this->connect()->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function fetchAdminOrdersWithFilters($filters = []) {
        $where_clause = "";
        $params = [];

        if (!empty($filters['filter_date'])) {
            $where_clause .= " WHERE DATE(o.order_date) = :filter_date";
            $params[':filter_date'] = $filters['filter_date'];
        }

        if (!empty($filters['filter_user'])) {
            $where_clause .= $where_clause ? " AND" : " WHERE";
            $where_clause .= " (u.user_name LIKE :filter_user OR u.user_id = :filter_user_id)";
            $params[':filter_user'] = "%" . $filters['filter_user'] . "%";
            $params[':filter_user_id'] = (int)$filters['filter_user'];
        }

        if (!empty($filters['filter_status'])) {
            $where_clause .= $where_clause ? " AND" : " WHERE";
            $where_clause .= " o.status = :filter_status";
            $params[':filter_status'] = $filters['filter_status'];
        }

        $query = "
            SELECT o.order_id, o.order_date, o.status, u.user_name, o.order_notes,
                   GROUP_CONCAT(CONCAT(p.product_name, ' (', op.quntity, ' x $', p.price, ')') SEPARATOR ', ') AS products,
                   SUM(op.quntity) AS total_quantity,
                   SUM(op.quntity * p.price) AS total_price
            FROM Orders o
            JOIN User u ON o.f_user_id = u.user_id
            LEFT JOIN Order_product op ON o.order_id = op.f_order_id
            LEFT JOIN Product p ON op.f_product_id = p.product_id
            $where_clause
            GROUP BY o.order_id, o.order_date, o.status, u.user_name, o.order_notes
            ORDER BY o.order_date DESC
        ";

        $stmt = $this->connect()->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function deleteProductWithDependencies($product_id) {
        $conn = $this->connect();
        try {
            $conn->beginTransaction();

            $delete_order_product_stmt = $conn->prepare("DELETE FROM Order_product WHERE f_product_id = :product_id");
            $delete_order_product_stmt->execute([':product_id' => $product_id]);
            $delete_product_stmt = $conn->prepare("DELETE FROM Product WHERE product_id = :product_id");
            $delete_product_stmt->execute([':product_id' => $product_id]);

            $conn->commit();
        } catch (PDOException $e) {
            $conn->rollBack();
            throw $e;
        }
    }

    public function fetchChecksWithFilters($filters = []) {
        $where_clause = "";
        $params = [];

        if (!empty($filters['filter_date'])) {
            $where_clause .= " WHERE DATE(o.order_date) = :filter_date";
            $params[':filter_date'] = $filters['filter_date'];
        }

        if (!empty($filters['filter_user'])) {
            $where_clause .= $where_clause ? " AND" : " WHERE";
            $where_clause .= " (u.user_name LIKE :filter_user OR u.user_id = :filter_user_id)";
            $params[':filter_user'] = "%" . $filters['filter_user'] . "%";
            $params[':filter_user_id'] = (int)$filters['filter_user'];
        }

        $query = "
            SELECT o.order_id, o.order_date, u.user_name, o.status, o.order_notes,
                   GROUP_CONCAT(CONCAT(p.product_name, ' (', op.quntity, ' x $', p.price, ')') SEPARATOR ', ') AS products,
                   SUM(op.quntity) AS total_quantity,
                   SUM(op.quntity * p.price) AS total_price
            FROM Orders o
            JOIN User u ON o.f_user_id = u.user_id
            LEFT JOIN Order_product op ON o.order_id = op.f_order_id
            LEFT JOIN Product p ON op.f_product_id = p.product_id
            $where_clause
            GROUP BY o.order_id, o.order_date, u.user_name, o.status, o.order_notes
            ORDER BY o.order_date DESC
        ";

        $stmt = $this->connect()->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function fetchRevenueBreakdown($filters = []) {
        $where_clause = "";
        $params = [];

        if (!empty($filters['filter_date'])) {
            $where_clause .= " WHERE DATE(o.order_date) = :filter_date";
            $params[':filter_date'] = $filters['filter_date'];
        }

        if (!empty($filters['filter_user'])) {
            $where_clause .= $where_clause ? " AND" : " WHERE";
            $where_clause .= " (u.user_name LIKE :filter_user OR u.user_id = :filter_user_id)";
            $params[':filter_user'] = "%" . $filters['filter_user'] . "%";
            $params[':filter_user_id'] = (int)$filters['filter_user'];
        }

        $query = "
            SELECT p.product_name, 
                   SUM(op.quntity) AS total_sold, 
                   SUM(op.quntity * p.price) AS revenue
            FROM Orders o
            JOIN Order_product op ON o.order_id = op.f_order_id
            JOIN Product p ON op.f_product_id = p.product_id
            JOIN User u ON o.f_user_id = u.user_id
            $where_clause
            GROUP BY p.product_name
            ORDER BY revenue DESC
        ";

        $stmt = $this->connect()->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function deleteUserById($user_id) {
        $conn = $this->connect();
        $stmt = $conn->prepare("DELETE FROM User WHERE user_id = :user_id");
        $stmt->execute([':user_id' => $user_id]);
    }

    public function fetchUserDetailsById($user_id) {
        $stmt = $this->connect()->prepare("SELECT * FROM User WHERE user_id = :user_id");
        $stmt->execute([':user_id' => $user_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function fetchUserRoomsById($user_id) {
        $stmt = $this->connect()->prepare("SELECT room_name FROM user_room WHERE user_id = :user_id");
        $stmt->execute([':user_id' => $user_id]);
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    public function updateUser($user_id, $user_name, $email, $ext, $profile_picture) {
        $stmt = $this->connect()->prepare("
            UPDATE User 
            SET user_name = :user_name, email = :email, ext = :ext, profile_picture = :profile_picture 
            WHERE user_id = :user_id
        ");
        $stmt->execute([
            ':user_name' => $user_name,
            ':email' => $email,
            ':ext' => $ext,
            ':profile_picture' => $profile_picture,
            ':user_id' => $user_id
        ]);
    }

    public function updateUserRooms($user_id, $rooms) {
        $conn = $this->connect();
        $stmt = $conn->prepare("DELETE FROM user_room WHERE user_id = :user_id");
        $stmt->execute([':user_id' => $user_id]);

        if (!empty($rooms)) {
            $stmt = $conn->prepare("INSERT INTO user_room (user_id, room_name) VALUES (:user_id, :room_name)");
            foreach ($rooms as $room) {
                $stmt->execute([':user_id' => $user_id, ':room_name' => $room]);
            }
        }
    }
}
?>