<?php
/**
 * LessonProgress Model
 * Handles lesson viewing progress tracking
 */

class LessonProgress {
    private $pdo;
    private $table = 'lesson_progress';

    public function __construct() {
        $db = new Database();
        $this->pdo = $db->getPDO();
    }

    /**
     * Mark lesson as watched/completed
     */
    public function markAsWatched($student_id, $lesson_id, $course_id) {
        $query = "INSERT INTO " . $this->table . "
                  (student_id, lesson_id, course_id, is_completed)
                  VALUES (:student_id, :lesson_id, :course_id, 1)
                  ON DUPLICATE KEY UPDATE
                  is_completed = 1, watched_at = NOW()";
        
        $stmt = $this->pdo->prepare($query);
        return $stmt->execute([
            ':student_id' => $student_id,
            ':lesson_id' => $lesson_id,
            ':course_id' => $course_id
        ]);
    }

    /**
     * Check if lesson is completed
     */
    public function isLessonCompleted($student_id, $lesson_id) {
        $query = "SELECT is_completed FROM " . $this->table . "
                  WHERE student_id = :student_id AND lesson_id = :lesson_id";
        
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([
            ':student_id' => $student_id,
            ':lesson_id' => $lesson_id
        ]);
        
        $result = $stmt->fetch();
        return $result ? ($result['is_completed'] == 1) : false;
    }

    /**
     * Get completed lessons for a course
     */
    public function getCompletedLessonsForCourse($student_id, $course_id) {
        $query = "SELECT COUNT(*) as completed FROM " . $this->table . "
                  WHERE student_id = :student_id AND course_id = :course_id AND is_completed = 1";
        
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([
            ':student_id' => $student_id,
            ':course_id' => $course_id
        ]);
        
        $result = $stmt->fetch();
        return $result['completed'] ?? 0;
    }

    /**
     * Get progress percentage for a course
     */
    public function getCourseProgress($student_id, $course_id) {
        $lesson = new Lesson();
        $total_lessons = count($lesson->getLessonsByCourse($course_id));
        
        if ($total_lessons == 0) {
            return 0;
        }
        
        $completed = $this->getCompletedLessonsForCourse($student_id, $course_id);
        return round(($completed / $total_lessons) * 100);
    }

    /**
     * Get all completed lessons for a course with details
     */
    public function getCompletedLessonsWithDetails($student_id, $course_id) {
        $query = "SELECT lp.*, l.title, l.video_url FROM " . $this->table . " lp
                  JOIN lessons l ON lp.lesson_id = l.id
                  WHERE lp.student_id = :student_id AND lp.course_id = :course_id AND lp.is_completed = 1
                  ORDER BY lp.watched_at DESC";
        
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([
            ':student_id' => $student_id,
            ':course_id' => $course_id
        ]);
        
        return $stmt->fetchAll();
    }
}
?>
