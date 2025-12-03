<?php
/**
 * Material Model
 * Handles learning material data operations
 */

class Material {
    private $pdo;
    private $table = 'materials';

    public function __construct() {
        $db = new Database();
        $this->pdo = $db->getPDO();
    }

    /**
     * Get materials for a lesson
     */
    public function getMaterialsByLesson($lesson_id) {
        $query = "SELECT * FROM " . $this->table . "
                  WHERE lesson_id = :lesson_id
                  ORDER BY created_at DESC";
        
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':lesson_id', $lesson_id);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Get material by ID
     */
    public function getMaterialById($id) {
        $query = "SELECT * FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch();
    }

    /**
     * Upload material
     */
    public function upload($data) {
        $query = "INSERT INTO " . $this->table . "
                  (lesson_id, title, file_path, file_type, file_size, created_at)
                  VALUES (:lesson_id, :title, :file_path, :file_type, :file_size, NOW())";
        
        $stmt = $this->pdo->prepare($query);
        
        $stmt->bindParam(':lesson_id', $data['lesson_id']);
        $stmt->bindParam(':title', $data['title']);
        $stmt->bindParam(':file_path', $data['file_path']);
        $stmt->bindParam(':file_type', $data['file_type']);
        $stmt->bindParam(':file_size', $data['file_size']);
        
        return $stmt->execute();
    }

    /**
     * Delete material
     */
    public function delete($id) {
        $query = "DELETE FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    /**
     * Get total materials for a course
     */
    public function getTotalMaterialsByCourse($course_id) {
        $query = "SELECT COUNT(*) as total FROM " . $this->table . " m
                  JOIN lessons l ON m.lesson_id = l.id
                  WHERE l.course_id = :course_id";
        
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':course_id', $course_id);
        $stmt->execute();
        $result = $stmt->fetch();
        return $result['total'] ?? 0;
    }
}
?>
