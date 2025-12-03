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
        
        $params = [];
        if ($status) {
            $params[':status'] = $status;
        }
        
        $stmt->execute($params);
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
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    /**
     * Get courses by instructor
     */
    public function getCoursesByInstructor($instructor_id) {
        $query = "SELECT c.*, 
                  ca.status as approval_status,
                  ca.reviewed_by,
                  ca.notes
                  FROM " . $this->table . " c
                  LEFT JOIN course_approvals ca ON c.id = ca.course_id
                  WHERE c.instructor_id = :instructor_id
                  ORDER BY c.created_at DESC";
        
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([':instructor_id' => $instructor_id]);
        return $stmt->fetchAll();
    }

    /**
     * Create course
     */
    public function create($data) {
        $query = "INSERT INTO " . $this->table . "
                  (title, description, category_id, instructor_id, price, duration_weeks, 
                   level, image, status, created_at)
                  VALUES (:title, :description, :category_id, :instructor_id, :price, 
                          :duration_weeks, :level, :image, :status, NOW())";
        
        $stmt = $this->pdo->prepare($query);
        
        $stmt->execute([
            ':title' => $data['title'],
            ':description' => $data['description'],
            ':category_id' => $data['category_id'] ?? 1,
            ':instructor_id' => $data['instructor_id'],
            ':price' => $data['price'] ?? 0,
            ':duration_weeks' => $data['duration_weeks'] ?? 1,
            ':level' => $data['level'] ?? 'Beginner',
            ':image' => $data['image'] ?? null,
            ':status' => $data['status'] ?? 'draft'
        ]);
        
        return true;
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
        
        $params = [':id' => $id];
        foreach ($data as $key => $value) {
            $params[':' . $key] = $value;
        }
        
        $stmt = $this->pdo->prepare($query);
        return $stmt->execute($params);
    }

    /**
     * Delete course
     */
    public function delete($id) {
        $query = "DELETE FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->pdo->prepare($query);
        return $stmt->execute([':id' => $id]);
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
        
        $params = [':keyword' => $keyword_param];
        if ($category_id) {
            $params[':category_id'] = $category_id;
        }
        
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
}
?>
