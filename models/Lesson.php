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
                  ORDER BY `order` ASC";
        
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([':course_id' => $course_id]);
        return $stmt->fetchAll();
    }

    /**
     * Get lesson by ID
     */
    public function getLessonById($id) {
        $query = "SELECT * FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    /**
     * Create lesson
     */
    public function create($data) {
        $query = "INSERT INTO " . $this->table . "
                  (course_id, title, content, video_url, `order`, created_at)
                  VALUES (:course_id, :title, :content, :video_url, :order, NOW())";
        
        $stmt = $this->pdo->prepare($query);
        
        $stmt->execute([
            ':course_id' => $data['course_id'],
            ':title' => $data['title'],
            ':content' => $data['content'] ?? null,
            ':video_url' => $data['video_url'] ?? null,
            ':order' => $data['order'] ?? 1
        ]);
        
        return true;
    }

    /**
     * Update lesson
     */
    public function update($id, $data) {
        $query = "UPDATE " . $this->table . " SET ";
        $fields = [];
        
        foreach ($data as $key => $value) {
            $fields[] = "`" . $key . "` = :" . $key;
        }
        
        $query .= implode(", ", $fields) . " WHERE id = :id";
        
        $params = [':id' => $id];
        foreach ($data as $key => $value) {
            $params[':' . $key] = $value;
        }
        
        $stmt = $this->pdo->prepare($query);
        return $stmt->execute($params);
    }

    /**
     * Delete lesson
     */
    public function delete($id) {
        $query = "DELETE FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->pdo->prepare($query);
        return $stmt->execute([':id' => $id]);
    }
}
?>
