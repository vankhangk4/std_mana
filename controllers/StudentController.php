<?php
/**
 * Student Controller
 * Handles student dashboard, enrollments, and course viewing
 */

class StudentController {
    
    /**
     * Check if user is logged in and is a student
     */
    private function requireAuth() {
        if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 0) {
            header('Location: /auth/login');
            exit;
        }
    }

    /**
     * Show student dashboard
     */
    public function dashboard() {
        $this->requireAuth();
        
        $enrollment = new Enrollment();
        $user_id = $_SESSION['user_id'];
        
        // Get enrolled courses
        $enrolled_courses = $enrollment->getStudentCoursesWithDetails($user_id);
        
        // Count ongoing and completed courses
        $ongoing_courses = [];
        $completed_courses = [];
        
        foreach ($enrolled_courses as $course) {
            if ($course['progress'] < 100) {
                $ongoing_courses[] = $course;
            } else {
                $completed_courses[] = $course;
            }
        }
        
        $this->render('student/dashboard', [
            'page_title' => 'Dashboard Học Viên',
            'enrolled_courses' => $enrolled_courses,
            'ongoing_count' => count($ongoing_courses),
            'completed_count' => count($completed_courses),
            'ongoing_courses' => $ongoing_courses
        ]);
    }

    /**
     * Show my courses page
     */
    public function my_courses() {
        $this->requireAuth();
        
        $enrollment = new Enrollment();
        $user_id = $_SESSION['user_id'];
        
        // Get all enrolled courses with details
        $courses = $enrollment->getStudentCoursesWithDetails($user_id);
        
        $this->render('student/my_courses', [
            'page_title' => 'Khóa Học Của Tôi',
            'enrollments' => $courses
        ]);
    }

    /**
     * Show lesson for a course
     */
    public function lesson($course_id, $lesson_id = null) {
        $this->requireAuth();
        
        $enrollment = new Enrollment();
        $user_id = $_SESSION['user_id'];
        
        // Check if student is enrolled in this course
        $is_enrolled = $enrollment->isStudentEnrolled($user_id, $course_id);
        
        if (!$is_enrolled) {
            $_SESSION['error'] = 'Bạn chưa đăng ký khóa học này';
            header('Location: /student/my-courses');
            exit;
        }
        
        $lesson = new Lesson();
        $course = new Course();
        $material = new Material();
        $lesson_progress = new LessonProgress();
        
        // Get all lessons for the course
        $lessons = $lesson->getLessonsByCourse($course_id);
        
        // Get current lesson
        if ($lesson_id) {
            $current_lesson = $lesson->getLessonById($lesson_id);
        } else {
            $current_lesson = $lessons[0] ?? null;
        }
        
        if (!$current_lesson) {
            $_SESSION['error'] = 'Bài học không tồn tại';
            header('Location: /student/my-courses');
            exit;
        }
        
        // Get course info
        $course_info = $course->getCourseById($course_id);
        
        // Get materials for this lesson
        $lesson_materials = $material->getMaterialsByLesson($current_lesson['id']);
        
        // Check if lesson is completed
        $is_lesson_completed = $lesson_progress->isLessonCompleted($user_id, $current_lesson['id']);
        
        // Get progress percentage for course
        $course_progress = $lesson_progress->getCourseProgress($user_id, $course_id);
        
        $this->render('student/lesson', [
            'page_title' => $current_lesson['title'],
            'course' => $course_info,
            'lesson' => $current_lesson,
            'lessons' => $lessons,
            'materials' => $lesson_materials,
            'is_lesson_completed' => $is_lesson_completed,
            'course_progress' => $course_progress
        ]);
    }

    /**
     * Mark lesson as completed
     */
    public function mark_lesson_complete($course_id, $lesson_id) {
        $this->requireAuth();
        
        $enrollment = new Enrollment();
        $user_id = $_SESSION['user_id'];
        
        // Check if student is enrolled
        if (!$enrollment->isStudentEnrolled($user_id, $course_id)) {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }
        
        $lesson_progress = new LessonProgress();
        $success = $lesson_progress->markAsWatched($user_id, $lesson_id, $course_id);
        
        if ($success) {
            // Update overall course progress
            $progress = $lesson_progress->getCourseProgress($user_id, $course_id);
            
            // Update enrollment progress
            $enrollment->updateProgress($user_id, $course_id, $progress);
            
            http_response_code(200);
            echo json_encode(['success' => true, 'progress' => $progress]);
        } else {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Error updating progress']);
        }
        exit;
    }

    /**
     * Enroll in a course
     */
    public function enroll($course_id) {
        $this->requireAuth();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $_SESSION['error'] = 'Invalid request method';
            header('Location: /course');
            exit;
        }
        
        $enrollment = new Enrollment();
        $user_id = $_SESSION['user_id'];
        
        // Check if already enrolled
        if ($enrollment->isStudentEnrolled($user_id, $course_id)) {
            $_SESSION['error'] = 'Bạn đã đăng ký khóa học này';
            header('Location: /course/detail/' . $course_id);
            exit;
        }
        
        // Enroll student
        if ($enrollment->enroll($course_id, $user_id)) {
            $_SESSION['success'] = 'Đăng ký khóa học thành công';
            header('Location: /student/my-courses');
        } else {
            $_SESSION['error'] = 'Đã xảy ra lỗi khi đăng ký khóa học';
            header('Location: /course/detail/' . $course_id);
        }
        exit;
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
