<?php
/**
 * Admin Controller
 * Handles admin dashboard and management
 */

class AdminController {

    /**
     * Check if user is admin
     */
    private function requireAdmin() {
        if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
            $_SESSION['error'] = 'Bạn không có quyền truy cập';
            header('Location: /');
            exit;
        }
    }

    /**
     * Admin dashboard
     */
    public function dashboard() {
        $this->requireAdmin();
        
        $user = new User();
        $course = new Course();
        $enrollment = new Enrollment();
        
        $totalUsers = count($user->getAllUsers());
        $totalCourses = count($course->getAllCourses());
        $totalInstructors = count($user->getAllUsers('instructor'));
        $totalStudents = count($user->getAllUsers('student'));
        
        $this->render('admin/dashboard', [
            'totalUsers' => $totalUsers,
            'totalCourses' => $totalCourses,
            'totalInstructors' => $totalInstructors,
            'totalStudents' => $totalStudents,
            'page_title' => 'Bảng Điều Khiển Quản Trị'
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
