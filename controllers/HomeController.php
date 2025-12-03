<?php
/**
 * Home Controller
 * Handles home page and general views
 */

class HomeController {

    /**
     * Display home page
     */
    public function index() {
        $course = new Course();
        $category = new Category();
        
        // Get all courses (published and draft)
        $courses = $course->getAllCourses();
        $categories = $category->getAllCategories();
        
        // Check enrollment status for logged-in students
        $enrolled_courses = [];
        if (isset($_SESSION['user_id']) && $_SESSION['user_role'] == 0) {
            $enrollment = new Enrollment();
            $enrollments = $enrollment->getStudentEnrollments($_SESSION['user_id']);
            foreach ($enrollments as $e) {
                $enrolled_courses[] = $e['course_id'] ?? null;
            }
        }
        
        $data = [
            'courses' => $courses,
            'categories' => $categories,
            'enrolled_courses' => $enrolled_courses,
            'page_title' => 'Trang Chủ - Nền Tảng Học Trực Tuyến'
        ];
        
        $this->render('home/index', $data);
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
