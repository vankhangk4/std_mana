<?php
/**
 * Auth Controller
 * Handles authentication (login, register, logout)
 */

class AuthController {

    /**
     * Show login form
     */
    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handleLogin();
        } else {
            $this->render('auth/login', [
                'page_title' => 'Đăng Nhập'
            ]);
        }
    }

    /**
     * Handle login form submission
     */
    private function handleLogin() {
        $email = isset($_POST['email']) ? trim($_POST['email']) : '';
        $password = isset($_POST['password']) ? $_POST['password'] : '';

        if (empty($email) || empty($password)) {
            $_SESSION['error'] = 'Email và mật khẩu không được để trống';
            header('Location: /auth/login');
            exit;
        }

        $user = new User();
        $userRecord = $user->getUserByEmail($email);

        if (!$userRecord || !$user->verifyPassword($password, $userRecord['password'])) {
            $_SESSION['error'] = 'Email hoặc mật khẩu không chính xác';
            header('Location: /auth/login');
            exit;
        }

        if ($userRecord['status'] != 1) {
            $_SESSION['error'] = 'Tài khoản của bạn đã bị vô hiệu hóa';
            header('Location: /auth/login');
            exit;
        }

        // Set session
        $_SESSION['user_id'] = $userRecord['id'];
        $_SESSION['user_name'] = $userRecord['name'];
        $_SESSION['user_email'] = $userRecord['email'];
        $_SESSION['user_role'] = $userRecord['role'];

        // Redirect based on role
        if ($userRecord['role'] === 'admin') {
            header('Location: /admin/dashboard');
        } elseif ($userRecord['role'] === 'instructor') {
            header('Location: /instructor/dashboard');
        } else {
            header('Location: /student/dashboard');
        }
        exit;
    }

    /**
     * Show register form
     */
    public function register() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handleRegister();
        } else {
            $this->render('auth/register', [
                'page_title' => 'Đăng Ký'
            ]);
        }
    }

    /**
     * Handle register form submission
     */
    private function handleRegister() {
        $name = isset($_POST['name']) ? trim($_POST['name']) : '';
        $email = isset($_POST['email']) ? trim($_POST['email']) : '';
        $password = isset($_POST['password']) ? $_POST['password'] : '';
        $password_confirm = isset($_POST['password_confirm']) ? $_POST['password_confirm'] : '';
        $role = isset($_POST['role']) ? $_POST['role'] : 'student';

        // Validation
        if (empty($name) || empty($email) || empty($password) || empty($password_confirm)) {
            $_SESSION['error'] = 'Vui lòng điền tất cả các trường';
            header('Location: /auth/register');
            exit;
        }

        if ($password !== $password_confirm) {
            $_SESSION['error'] = 'Mật khẩu không khớp';
            header('Location: /auth/register');
            exit;
        }

        if (strlen($password) < 6) {
            $_SESSION['error'] = 'Mật khẩu phải có ít nhất 6 ký tự';
            header('Location: /auth/register');
            exit;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['error'] = 'Email không hợp lệ';
            header('Location: /auth/register');
            exit;
        }

        $user = new User();
        if ($user->getUserByEmail($email)) {
            $_SESSION['error'] = 'Email này đã được đăng ký';
            header('Location: /auth/register');
            exit;
        }

        // Register user
        if ($user->register([
            'name' => $name,
            'email' => $email,
            'password' => $password,
            'role' => in_array($role, ['student', 'instructor']) ? $role : 'student'
        ])) {
            $_SESSION['success'] = 'Đăng ký thành công. Vui lòng đăng nhập';
            header('Location: /auth/login');
        } else {
            $_SESSION['error'] = 'Đã xảy ra lỗi khi đăng ký';
            header('Location: /auth/register');
        }
        exit;
    }

    /**
     * Logout
     */
    public function logout() {
        session_destroy();
        header('Location: /');
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
