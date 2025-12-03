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
    private static $instance;

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

    /**
     * Get singleton instance
     */
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Execute query with parameters
     */
    public function execute($query, $params = []) {
        try {
            $stmt = $this->getPDO()->prepare($query);
            return $stmt->execute($params);
        } catch (Exception $e) {
            error_log('Database execute error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Query database and fetch all results
     */
    public function query($query, $params = []) {
        try {
            $stmt = $this->getPDO()->prepare($query);
            $stmt->execute($params);
            return $stmt->fetchAll();
        } catch (Exception $e) {
            error_log('Database query error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Query database and fetch single row
     */
    public function queryOne($query, $params = []) {
        try {
            $stmt = $this->getPDO()->prepare($query);
            $stmt->execute($params);
            return $stmt->fetch();
        } catch (Exception $e) {
            error_log('Database queryOne error: ' . $e->getMessage());
            return null;
        }
    }
}
?>
