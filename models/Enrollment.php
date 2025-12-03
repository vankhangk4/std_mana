<?php
/**
 * Enrollment Model
 * Handles student enrollment data operations
 */

class Enrollment {
    private $pdo;
    private $table = 'enrollments';

    public function __construct() {
        $db = new Database();
        $this->pdo = $db->getPDO();
    }

    /**
     * Check if student is enrolled in course
     */
    public function isEnrolled($student_id, $course_id) {
        $query = "SELECT * FROM " . $this->table . "
                  WHERE student_id = :student_id AND course_id = :course_id";
        
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':student_id', $student_id);
        $stmt->bindParam(':course_id', $course_id);
        $stmt->execute();
        
        return $stmt->fetch() ? true : false;
    }

    /**
     * Enroll student in course
     */
    public function enroll($student_id, $course_id) {
        if ($this->isEnrolled($student_id, $course_id)) {
            return false;
        }

        $query = "INSERT INTO " . $this->table . "
                  (student_id, course_id, enrolled_at, progress)
                  VALUES (:student_id, :course_id, NOW(), 0)";
        
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':student_id', $student_id);
        $stmt->bindParam(':course_id', $course_id);
        
        return $stmt->execute();
    }

    /**
     * Get student enrollments
     */
    public function getStudentEnrollments($student_id) {
        $query = "SELECT e.*, c.title, c.description, c.thumbnail, u.name as instructor_name
                  FROM " . $this->table . " e
                  JOIN courses c ON e.course_id = c.id
                  LEFT JOIN users u ON c.instructor_id = u.id
                  WHERE e.student_id = :student_id
                  ORDER BY e.enrolled_at DESC";
        
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':student_id', $student_id);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Get course enrollments
     */
    public function getCourseEnrollments($course_id) {
        $query = "SELECT e.*, u.name, u.email
                  FROM " . $this->table . " e
                  JOIN users u ON e.student_id = u.id
                  WHERE e.course_id = :course_id
                  ORDER BY e.enrolled_at DESC";
        
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':course_id', $course_id);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Update progress
     */
    public function updateProgress($student_id, $course_id, $progress) {
        $query = "UPDATE " . $this->table . "
                  SET progress = :progress, updated_at = NOW()
                  WHERE student_id = :student_id AND course_id = :course_id";
        
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':progress', $progress);
        $stmt->bindParam(':student_id', $student_id);
        $stmt->bindParam(':course_id', $course_id);
        
        return $stmt->execute();
    }

    /**
     * Unenroll student
     */
    public function unenroll($student_id, $course_id) {
        $query = "DELETE FROM " . $this->table . "
                  WHERE student_id = :student_id AND course_id = :course_id";
        
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':student_id', $student_id);
        $stmt->bindParam(':course_id', $course_id);
        
        return $stmt->execute();
    }
}
?>
