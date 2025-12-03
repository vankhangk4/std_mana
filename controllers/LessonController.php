<?php
/**
 * Lesson Controller
 * Handles lesson viewing
 */

class LessonController {

    /**
     * View lesson
     */
    public function view($id = null) {
        if (!$id) {
            header('Location: /course');
            exit;
        }

        if (!isset($_SESSION['user_id'])) {
            $_SESSION['error'] = 'Vui lòng đăng nhập để xem bài học';
            header('Location: /auth/login');
            exit;
        }

        $lesson = new Lesson();
        $lessonData = $lesson->getLessonById($id);
        
        if (!$lessonData) {
            header('HTTP/1.0 404 Not Found');
            echo "Bài học không tìm thấy";
            exit;
        }

        // Check if student is enrolled in course
        if ($_SESSION['user_role'] === 'student') {
            $enrollment = new Enrollment();
            if (!$enrollment->isEnrolled($_SESSION['user_id'], $lessonData['course_id'])) {
                $_SESSION['error'] = 'Bạn chưa đăng ký khóa học này';
                header('Location: /course/detail/' . $lessonData['course_id']);
                exit;
            }
        }

        $material = new Material();
        $materials = $material->getMaterialsByLesson($id);
        
        $course = new Course();
        $courseData = $course->getCourseById($lessonData['course_id']);
        
        $this->render('student/lesson', [
            'lesson' => $lessonData,
            'materials' => $materials,
            'course' => $courseData,
            'page_title' => $lessonData['title']
        ]);
    }

    /**
     * Render view with data
     */
    protected function render($view, $data = []) {
        extract($data);
        require VIEWS_PATH . '/' . $view . '.php';
    }
}
?>
