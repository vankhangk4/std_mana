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
        $stmt->execute([
            ':student_id' => $student_id,
            ':course_id' => $course_id
        ]);
        
        return $stmt->fetch() ? true : false;
    }

    /**
     * Alias for isEnrolled (used by StudentController)
     */
    public function isStudentEnrolled($student_id, $course_id) {
        return $this->isEnrolled($student_id, $course_id);
    }

    /**
     * Enroll student in course
     */
    public function enroll($course_id, $student_id) {
        if ($this->isEnrolled($student_id, $course_id)) {
            return false;
        }

        $query = "INSERT INTO " . $this->table . "
                  (course_id, student_id, enrolled_date, status, progress)
                  VALUES (:course_id, :student_id, NOW(), 'active', 0)";
        
        $stmt = $this->pdo->prepare($query);
        
        return $stmt->execute([
            ':course_id' => $course_id,
            ':student_id' => $student_id
        ]);
    }

    /**
     * Get student enrollments
     */
    public function getStudentEnrollments($student_id) {
        $query = "SELECT e.*, c.title, c.description, u.fullname as instructor_name
                  FROM " . $this->table . " e
                  JOIN courses c ON e.course_id = c.id
                  LEFT JOIN users u ON c.instructor_id = u.id
                  WHERE e.student_id = :student_id
                  ORDER BY e.enrolled_date DESC";
        
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([':student_id' => $student_id]);
        return $stmt->fetchAll();
    }

    /**
     * Get student courses with details
     */
    public function getStudentCoursesWithDetails($student_id) {
        $query = "SELECT e.*, c.id as course_id, c.title, c.description, c.level, c.duration_weeks,
                         u.fullname as instructor_name
                  FROM " . $this->table . " e
                  JOIN courses c ON e.course_id = c.id
                  LEFT JOIN users u ON c.instructor_id = u.id
                  WHERE e.student_id = :student_id
                  ORDER BY e.enrolled_date DESC";
        
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([':student_id' => $student_id]);
        return $stmt->fetchAll();
    }

    /**
     * Get course enrollments
     */
    public function getCourseEnrollments($course_id) {
        $query = "SELECT e.*, u.fullname, u.email
                  FROM " . $this->table . " e
                  JOIN users u ON e.student_id = u.id
                  WHERE e.course_id = :course_id
                  ORDER BY e.enrolled_date DESC";
        
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([':course_id' => $course_id]);
        return $stmt->fetchAll();
    }

    /**
     * Update progress
     */
    public function updateProgress($student_id, $course_id, $progress) {
        $query = "UPDATE " . $this->table . "
                  SET progress = :progress
                  WHERE student_id = :student_id AND course_id = :course_id";
        
        $stmt = $this->pdo->prepare($query);
        
        return $stmt->execute([
            ':progress' => $progress,
            ':student_id' => $student_id,
            ':course_id' => $course_id
        ]);
    }

    /**
     * Unenroll student
     */
    public function unenroll($student_id, $course_id) {
        $query = "DELETE FROM " . $this->table . "
                  WHERE student_id = :student_id AND course_id = :course_id";
        
        $stmt = $this->pdo->prepare($query);
        
        return $stmt->execute([
            ':student_id' => $student_id,
            ':course_id' => $course_id
        ]);
    }
}
?>
