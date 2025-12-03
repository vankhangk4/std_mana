<?php
/**
 * Lesson Model
 * Handles lesson data operations
 */

class Lesson {
    private $pdo;
    private $table = 'lessons';

    public function __construct() {
        $db = new Database();
        $this->pdo = $db->getPDO();
    }

    /**
     * Get all lessons for a course
     */
    public function getLessonsByCourse($course_id) {
        $query = "SELECT * FROM " . $this->table . "
                  WHERE course_id = :course_id
                  ORDER BY order_num ASC";
        
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':course_id', $course_id);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Get lesson by ID
     */
    public function getLessonById($id) {
        $query = "SELECT * FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch();
    }

    /**
     * Create lesson
     */
    public function create($data) {
        $query = "INSERT INTO " . $this->table . "
                  (course_id, title, description, video_url, content, order_num, created_at)
                  VALUES (:course_id, :title, :description, :video_url, :content, :order_num, NOW())";
        
        $stmt = $this->pdo->prepare($query);
        
        $stmt->bindParam(':course_id', $data['course_id']);
        $stmt->bindParam(':title', $data['title']);
        $stmt->bindParam(':description', $data['description'] ?? null);
        $stmt->bindParam(':video_url', $data['video_url'] ?? null);
        $stmt->bindParam(':content', $data['content'] ?? null);
        $stmt->bindParam(':order_num', $data['order_num'] ?? 1);
        
        return $stmt->execute();
    }

    /**
     * Update lesson
     */
    public function update($id, $data) {
        $query = "UPDATE " . $this->table . " SET ";
        $fields = [];
        
        foreach ($data as $key => $value) {
            $fields[] = $key . " = :" . $key;
        }
        
        $query .= implode(", ", $fields) . ", updated_at = NOW() WHERE id = :id";
        
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':id', $id);
        
        foreach ($data as $key => $value) {
            $stmt->bindParam(':' . $key, $value);
        }
        
        return $stmt->execute();
    }

    /**
     * Delete lesson
     */
    public function delete($id) {
        $query = "DELETE FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }
}
?>
