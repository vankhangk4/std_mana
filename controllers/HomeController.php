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
        
        $courses = $course->getAllCourses('published');
        $categories = $category->getAllCategories();
        
        $data = [
            'courses' => $courses,
            'categories' => $categories,
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
