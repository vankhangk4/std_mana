<?php
/**
 * Admin Controller
 * Handles admin dashboard and management
 * Role 17: Course Reviewer/Moderator
 * Role 2: System Admin
 */

class AdminController {

    /**
     * Check if user is admin (role 17 or 2)
     */
    private function requireAdmin() {
        if (!isset($_SESSION['user_id']) || ($_SESSION['user_role'] != 2 && $_SESSION['user_role'] != 17)) {
            $_SESSION['error'] = 'Bạn không có quyền truy cập';
            header('Location: /');
            exit;
        }
    }

    /**
     * Check if user is course reviewer (role 17 or 2)
     */
    private function requireReviewer() {
        if (!isset($_SESSION['user_id']) || ($_SESSION['user_role'] != 2 && $_SESSION['user_role'] != 17)) {
            $_SESSION['error'] = 'Bạn không có quyền truy cập';
            header('Location: /');
            exit;
        }
    }

    /**
     * Admin/Reviewer dashboard
     */
    public function dashboard() {
        $this->requireAdmin();
        
        $user = new User();
        $course = new Course();
        
        $totalUsers = count($user->getAllUsers());
        $totalCourses = count($course->getAllCourses());
        $totalInstructors = count($user->getAllUsers(1)); // role 1 = instructor
        $totalStudents = count($user->getAllUsers(0)); // role 0 = student
        
        // Get pending approvals count
        $pending_approvals = $this->getPendingApprovals();
        $pending_count = count($pending_approvals);
        
        $this->render('admin/dashboard', [
            'totalUsers' => $totalUsers,
            'totalCourses' => $totalCourses,
            'totalInstructors' => $totalInstructors,
            'totalStudents' => $totalStudents,
            'pending_approvals' => $pending_approvals,
            'pending_count' => $pending_count,
            'page_title' => 'Bảng Điều Khiển Quản Trị'
        ]);
    }

    /**
     * Course Approvals - List pending approvals
     */
    public function approvals() {
        $this->requireReviewer();
        
        $pending_approvals = $this->getPendingApprovals();
        $approved_approvals = $this->getApprovedApprovals();
        
        $this->render('admin/approvals', [
            'pending_approvals' => $pending_approvals,
            'approved_approvals' => $approved_approvals,
            'page_title' => 'Phê Duyệt Khóa Học'
        ]);
    }

    /**
     * Approve a course
     */
    public function approve_course($course_id) {
        $this->requireReviewer();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Invalid request']);
            exit;
        }

        $notes = $_POST['notes'] ?? '';

        $db = Database::getInstance();
        
        // Update course_approvals table
        $query = "UPDATE course_approvals 
                  SET status = 'approved', 
                      reviewed_at = NOW(), 
                      reviewed_by = :admin_id,
                      notes = :notes
                  WHERE course_id = :course_id AND status = 'pending'";

        $result = $db->execute($query, [
            ':course_id' => $course_id,
            ':admin_id' => $_SESSION['user_id'],
            ':notes' => $notes
        ]);

        if ($result) {
            // Update courses table approval_status
            $query2 = "UPDATE courses SET approval_status = 'approved' WHERE id = :course_id";
            $db->execute($query2, [':course_id' => $course_id]);
            
            echo json_encode([
                'success' => true,
                'message' => 'Khóa học đã được phê duyệt'
            ]);
        } else {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Không thể phê duyệt khóa học']);
        }
    }

    /**
     * Reject a course
     */
    public function reject_course($course_id) {
        $this->requireReviewer();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Invalid request']);
            exit;
        }

        $reason = $_POST['reason'] ?? '';

        if (empty($reason)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Lý do từ chối là bắt buộc']);
            exit;
        }

        $db = Database::getInstance();
        
        // Update course_approvals table
        $query = "UPDATE course_approvals 
                  SET status = 'rejected', 
                      reviewed_at = NOW(), 
                      reviewed_by = :admin_id,
                      notes = :reason
                  WHERE course_id = :course_id AND status = 'pending'";

        $result = $db->execute($query, [
            ':course_id' => $course_id,
            ':admin_id' => $_SESSION['user_id'],
            ':reason' => $reason
        ]);

        if ($result) {
            // Update courses table approval_status
            $query2 = "UPDATE courses SET approval_status = 'rejected' WHERE id = :course_id";
            $db->execute($query2, [':course_id' => $course_id]);
            
            echo json_encode([
                'success' => true,
                'message' => 'Khóa học đã bị từ chối'
            ]);
        } else {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Không thể từ chối khóa học']);
        }
    }

    /**
     * Get pending approvals
     */
    private function getPendingApprovals() {
        $db = Database::getInstance();
        $query = "SELECT ca.*, c.title, c.description, u.fullname as instructor_name 
                  FROM course_approvals ca
                  JOIN courses c ON ca.course_id = c.id
                  JOIN users u ON ca.instructor_id = u.id
                  WHERE ca.status = 'pending'
                  ORDER BY ca.submitted_at DESC";
        return $db->query($query) ?? [];
    }

    /**
     * Get approved/rejected approvals
     */
    private function getApprovedApprovals() {
        $db = Database::getInstance();
        $query = "SELECT ca.*, c.title, u.fullname as instructor_name,
                         admin.fullname as reviewer_name
                  FROM course_approvals ca
                  JOIN courses c ON ca.course_id = c.id
                  JOIN users u ON ca.instructor_id = u.id
                  LEFT JOIN users admin ON ca.reviewed_by = admin.id
                  WHERE ca.status IN ('approved', 'rejected')
                  ORDER BY ca.reviewed_at DESC
                  LIMIT 50";
        return $db->query($query) ?? [];
    }

    /**
     * View all users by role
     */
    public function users($role = null) {
        $this->requireAdmin();
        
        $user = new User();
        
        if ($role !== null) {
            $users = $user->getAllUsers(intval($role));
            $role_name = $this->getRoleName(intval($role));
        } else {
            $users = $user->getAllUsers();
            $role_name = 'Tất Cả Người Dùng';
        }
        
        $this->render('admin/users', [
            'users' => $users,
            'role' => $role,
            'role_name' => $role_name,
            'page_title' => $role_name
        ]);
    }

    /**
     * Edit user (change role)
     */
    public function edit_user($user_id) {
        $this->requireAdmin();
        
        $user = new User();
        $user_data = $user->getUserById($user_id);
        
        if (!$user_data) {
            $_SESSION['error'] = 'Người dùng không tồn tại';
            header('Location: /admin/users');
            exit;
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handleEditUser($user_id);
        } else {
            $this->render('admin/edit_user', [
                'user' => $user_data,
                'page_title' => 'Sửa Thông Tin Người Dùng'
            ]);
        }
    }

    /**
     * Handle edit user form
     */
    private function handleEditUser($user_id) {
        $fullname = $_POST['fullname'] ?? '';
        $role = intval($_POST['role'] ?? 0);
        
        if (empty($fullname)) {
            $_SESSION['error'] = 'Tên người dùng không được để trống';
            header('Location: /admin/edit_user/' . $user_id);
            exit;
        }
        
        $user = new User();
        if ($user->update($user_id, [
            'fullname' => $fullname,
            'role' => $role
        ])) {
            $_SESSION['success'] = 'Cập nhật người dùng thành công';
            header('Location: /admin/users');
        } else {
            $_SESSION['error'] = 'Không thể cập nhật người dùng';
            header('Location: /admin/edit_user/' . $user_id);
        }
        exit;
    }

    /**
     * Delete user
     */
    public function delete_user($user_id) {
        $this->requireAdmin();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Invalid request']);
            exit;
        }
        
        // Prevent deleting self
        if ($user_id == $_SESSION['user_id']) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Không thể xóa chính mình']);
            exit;
        }
        
        $user = new User();
        if ($user->delete($user_id)) {
            echo json_encode(['success' => true, 'message' => 'Người dùng đã được xóa']);
        } else {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Không thể xóa người dùng']);
        }
    }

    /**
     * Get role name
     */
    private function getRoleName($role) {
        $roles = [
            0 => 'Học Viên',
            1 => 'Giảng Viên',
            2 => 'Quản Trị Viên',
            17 => 'Quản Lý Phê Duyệt'
        ];
        return $roles[$role] ?? 'Không xác định';
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
