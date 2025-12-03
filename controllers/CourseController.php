<?php
/**
 * Course Controller
 * Handles course-related views and actions
 */

class CourseController {

    /**
     * List all courses with search and filter
     */
    public function index() {
        $course = new Course();
        $category = new Category();
        
        $search = isset($_GET['search']) ? trim($_GET['search']) : '';
        $category_id = isset($_GET['category']) ? intval($_GET['category']) : null;
        
        if ($search) {
            $courses = $course->search($search, $category_id);
        } else {
            $courses = $course->getAllCourses('published');
            if ($category_id) {
                $courses = array_filter($courses, function($c) use ($category_id) {
                    return $c['category_id'] == $category_id;
                });
            }
        }
        
        $categories = $category->getAllCategories();
        
        $this->render('courses/index', [
            'courses' => $courses,
            'categories' => $categories,
            'search' => $search,
            'category_id' => $category_id,
            'page_title' => 'Danh Sách Khóa Học'
        ]);
    }

    /**
     * View course detail
     */
    public function detail($id = null) {
        if (!$id) {
            header('Location: /course');
            exit;
        }

        $course = new Course();
        $courseData = $course->getCourseById($id);
        
        if (!$courseData) {
            header('HTTP/1.0 404 Not Found');
            echo "Khóa học không tìm thấy";
            exit;
        }

        $lesson = new Lesson();
        $lessons = $lesson->getLessonsByCourse($id);
        
        $enrollment = new Enrollment();
        $isEnrolled = false;
        
        if (isset($_SESSION['user_id']) && $_SESSION['user_role'] === 'student') {
            $isEnrolled = $enrollment->isEnrolled($_SESSION['user_id'], $id);
        }
        
        $this->render('courses/detail', [
            'course' => $courseData,
            'lessons' => $lessons,
            'isEnrolled' => $isEnrolled,
            'page_title' => $courseData['title']
        ]);
    }

    /**
     * Search courses (AJAX)
     */
    public function search() {
        header('Content-Type: application/json');
        
        $search = isset($_GET['q']) ? trim($_GET['q']) : '';
        $category_id = isset($_GET['category']) ? intval($_GET['category']) : null;
        
        if (strlen($search) < 2) {
            echo json_encode(['courses' => []]);
            exit;
        }

        $course = new Course();
        $courses = $course->search($search, $category_id);
        
        echo json_encode(['courses' => $courses]);
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
