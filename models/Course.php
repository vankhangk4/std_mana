<?php
/**
 * Course Model
 * Handles course data operations
 */

class Course {
    private $pdo;
    private $table = 'courses';

    public function __construct() {
        $db = new Database();
        $this->pdo = $db->getPDO();
    }

    /**
     * Get all courses
     */
    public function getAllCourses($status = null) {
        $query = "SELECT c.*, u.fullname as instructor_name, cat.name as category_name, 
                  COUNT(DISTINCT e.id) as total_enrolled
                  FROM " . $this->table . " c
                  LEFT JOIN users u ON c.instructor_id = u.id
                  LEFT JOIN categories cat ON c.category_id = cat.id
                  LEFT JOIN enrollments e ON c.id = e.course_id
                  WHERE 1=1";
        
        if ($status) {
            $query .= " AND c.status = :status";
        }
        
        $query .= " GROUP BY c.id ORDER BY c.created_at DESC";
        
        $stmt = $this->pdo->prepare($query);
        
        if ($status) {
            $stmt->bindParam(':status', $status);
        }
        
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Get course by ID
     */
    public function getCourseById($id) {
        $query = "SELECT c.*, u.fullname as instructor_name, cat.name as category_name,
                  COUNT(DISTINCT e.id) as total_enrolled
                  FROM " . $this->table . " c
                  LEFT JOIN users u ON c.instructor_id = u.id
                  LEFT JOIN categories cat ON c.category_id = cat.id
                  LEFT JOIN enrollments e ON c.id = e.course_id
                  WHERE c.id = :id
                  GROUP BY c.id";
        
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch();
    }

    /**
     * Get courses by instructor
     */
    public function getCoursesByInstructor($instructor_id) {
        $query = "SELECT * FROM " . $this->table . " 
                  WHERE instructor_id = :instructor_id
                  ORDER BY created_at DESC";
        
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':instructor_id', $instructor_id);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Create course
     */
    public function create($data) {
        $query = "INSERT INTO " . $this->table . "
                  (title, description, category_id, instructor_id, price, duration, 
                   thumbnail, status, created_at)
                  VALUES (:title, :description, :category_id, :instructor_id, :price, 
                          :duration, :thumbnail, :status, NOW())";
        
        $stmt = $this->pdo->prepare($query);
        
        $stmt->bindParam(':title', $data['title']);
        $stmt->bindParam(':description', $data['description']);
        $stmt->bindParam(':category_id', $data['category_id']);
        $stmt->bindParam(':instructor_id', $data['instructor_id']);
        $stmt->bindParam(':price', $data['price'] ?? 0);
        $stmt->bindParam(':duration', $data['duration'] ?? null);
        $stmt->bindParam(':thumbnail', $data['thumbnail'] ?? null);
        $stmt->bindParam(':status', $data['status'] ?? 'draft');
        
        return $stmt->execute();
    }

    /**
     * Update course
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
     * Delete course
     */
    public function delete($id) {
        $query = "DELETE FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    /**
     * Search courses
     */
    public function search($keyword, $category_id = null) {
        $query = "SELECT c.*, u.fullname as instructor_name, cat.name as category_name
                  FROM " . $this->table . " c
                  LEFT JOIN users u ON c.instructor_id = u.id
                  LEFT JOIN categories cat ON c.category_id = cat.id
                  WHERE (c.title LIKE :keyword OR c.description LIKE :keyword)
                  AND c.status = 'published'";
        
        if ($category_id) {
            $query .= " AND c.category_id = :category_id";
        }
        
        $query .= " ORDER BY c.created_at DESC";
        
        $stmt = $this->pdo->prepare($query);
        $keyword_param = "%{$keyword}%";
        $stmt->bindParam(':keyword', $keyword_param);
        
        if ($category_id) {
            $stmt->bindParam(':category_id', $category_id);
        }
        
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
?>
