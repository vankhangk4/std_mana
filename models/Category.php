<?php
/**
 * Category Model
 * Handles category data operations
 */

class Category {
    private $pdo;
    private $table = 'categories';

    public function __construct() {
        $db = new Database();
        $this->pdo = $db->getPDO();
    }

    /**
     * Get all categories
     */
    public function getAllCategories() {
        $query = "SELECT * FROM " . $this->table . " ORDER BY name ASC";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Get category by ID
     */
    public function getCategoryById($id) {
        $query = "SELECT * FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch();
    }

    /**
     * Create category
     */
    public function create($data) {
        $query = "INSERT INTO " . $this->table . "
                  (name, description, created_at)
                  VALUES (:name, :description, NOW())";
        
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':name', $data['name']);
        $stmt->bindParam(':description', $data['description'] ?? null);
        
        return $stmt->execute();
    }

    /**
     * Update category
     */
    public function update($id, $data) {
        $query = "UPDATE " . $this->table . " SET name = :name, description = :description
                  WHERE id = :id";
        
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':name', $data['name']);
        $stmt->bindParam(':description', $data['description'] ?? null);
        
        return $stmt->execute();
    }

    /**
     * Delete category
     */
    public function delete($id) {
        $query = "DELETE FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }
}
?>
