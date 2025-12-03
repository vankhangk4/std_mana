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
                  ORDER BY uploaded_at DESC";
        
        $stmt = $this->pdo->prepare($query);
        return $stmt->execute([':lesson_id' => $lesson_id]) ? $stmt->fetchAll() : [];
    }

    /**
     * Get material by ID
     */
    public function getMaterialById($id) {
        $query = "SELECT * FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    /**
     * Upload material
     */
    public function upload($data) {
        $query = "INSERT INTO " . $this->table . "
                  (lesson_id, filename, file_path, file_type)
                  VALUES (:lesson_id, :filename, :file_path, :file_type)";
        
        $stmt = $this->pdo->prepare($query);
        
        return $stmt->execute([
            ':lesson_id' => $data['lesson_id'],
            ':filename' => $data['filename'] ?? $data['title'] ?? 'Material',
            ':file_path' => $data['file_path'],
            ':file_type' => $data['file_type']
        ]);
    }

    /**
     * Delete material
     */
    public function delete($id) {
        $query = "DELETE FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->pdo->prepare($query);
        return $stmt->execute([':id' => $id]);
    }

    /**
     * Get total materials for a course
     */
    public function getTotalMaterialsByCourse($course_id) {
        $query = "SELECT COUNT(*) as total FROM " . $this->table . " m
                  JOIN lessons l ON m.lesson_id = l.id
                  WHERE l.course_id = :course_id";
        
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([':course_id' => $course_id]);
        $result = $stmt->fetch();
        return $result['total'] ?? 0;
    }
}
?>
