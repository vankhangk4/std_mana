<?php
session_start();

require 'config/Database.php';
require 'models/User.php';

// Simulate login as student
$user = new User();
$userData = $user->getUserByEmail('xuanhuong@gmail.com');

if ($userData && $user->verifyPassword('123456', $userData['password'])) {
    $_SESSION['user_id'] = $userData['id'];
    $_SESSION['user_role'] = $userData['role'];
    
    echo "Login successful as student (role: " . $userData['role'] . ")" . PHP_EOL;
    
    // Now check the condition
    if (isset($_SESSION['user_id']) && $_SESSION['user_role'] == 0) {
        echo "✓ Should show: Đăng Ký Khóa Học button" . PHP_EOL;
    } else {
        echo "✗ Should show: Đăng Nhập Để Đăng Ký button" . PHP_EOL;
        echo "  user_id: " . ($_SESSION['user_id'] ?? 'NOT SET') . PHP_EOL;
        echo "  user_role: " . ($_SESSION['user_role'] ?? 'NOT SET') . PHP_EOL;
    }
} else {
    echo "Login failed" . PHP_EOL;
}
?>
