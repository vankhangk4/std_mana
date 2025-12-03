<?php
/**
 * Database Configuration and Connection
 * Using PDO for secure database operations
 */

class Database {
    private $host = 'localhost';
    private $db_name = 'onlinecourse';
    private $user = 'root';
    private $password = 'Dk@17092004';
    private $pdo;

    /**
     * Connect to database using PDO
     */
    public function connect() {
        $this->pdo = null;

        try {
            $dsn = 'mysql:host=' . $this->host . ';dbname=' . $this->db_name . ';charset=utf8mb4';
            
            $this->pdo = new PDO(
                $dsn,
                $this->user,
                $this->password,
                array(
                    PDO::ATTR_PERSISTENT => false,
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
                )
            );
        } catch (PDOException $e) {
            die('Database Connection Error: ' . $e->getMessage());
        }

        return $this->pdo;
    }

    /**
     * Get PDO instance
     */
    public function getPDO() {
        if ($this->pdo === null) {
            $this->connect();
        }
        return $this->pdo;
    }
}
?>
