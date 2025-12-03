<?php
/**
 * User Model
 * Handles user data operations
 */

class User {
    private $pdo;
    private $table = 'users';

    public function __construct() {
        $db = new Database();
        $this->pdo = $db->getPDO();
    }

    /**
     * Get user by ID
     */
    public function getUserById($id) {
        $query = "SELECT * FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch();
    }

    /**
     * Get user by email
     */
    public function getUserByEmail($email) {
        $query = "SELECT * FROM " . $this->table . " WHERE email = :email";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        return $stmt->fetch();
    }

    /**
     * Register new user
     */
    public function register($data) {
        $query = "INSERT INTO " . $this->table . " 
                  (name, email, password, phone, role, status, created_at)
                  VALUES (:name, :email, :password, :phone, :role, :status, NOW())";
        
        $stmt = $this->pdo->prepare($query);
        
        // Hash password
        $password = password_hash($data['password'], PASSWORD_ARGON2ID);
        
        $stmt->bindParam(':name', $data['name']);
        $stmt->bindParam(':email', $data['email']);
        $stmt->bindParam(':password', $password);
        $stmt->bindParam(':phone', $data['phone'] ?? null);
        $stmt->bindParam(':role', $data['role'] ?? 'student');
        $stmt->bindParam(':status', $data['status'] ?? 1);
        
        return $stmt->execute();
    }

    /**
     * Update user
     */
    public function update($id, $data) {
        $query = "UPDATE " . $this->table . " SET ";
        $fields = [];
        
        foreach ($data as $key => $value) {
            $fields[] = $key . " = :" . $key;
        }
        
        $query .= implode(", ", $fields) . " WHERE id = :id";
        
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':id', $id);
        
        foreach ($data as $key => $value) {
            $stmt->bindParam(':' . $key, $value);
        }
        
        return $stmt->execute();
    }

    /**
     * Get all users
     */
    public function getAllUsers($role = null) {
        $query = "SELECT * FROM " . $this->table;
        
        if ($role) {
            $query .= " WHERE role = :role";
        }
        
        $query .= " ORDER BY created_at DESC";
        
        $stmt = $this->pdo->prepare($query);
        
        if ($role) {
            $stmt->bindParam(':role', $role);
        }
        
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Delete user
     */
    public function delete($id) {
        $query = "DELETE FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    /**
     * Verify password
     */
    public function verifyPassword($password, $hash) {
        return password_verify($password, $hash);
    }
}
?>
