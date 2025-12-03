<?php
/**
 * Enrollment Controller
 * Handles student enrollment in courses
 */

class EnrollmentController {

    /**
     * Enroll student in course
     */
    public function create($course_id = null) {
        if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'student') {
            $_SESSION['error'] = 'Vui lòng đăng nhập để đăng ký khóa học';
            header('Location: /auth/login');
            exit;
        }

        if (!$course_id) {
            $_SESSION['error'] = 'Khóa học không hợp lệ';
            header('Location: /course');
            exit;
        }

        $enrollment = new Enrollment();
        $course = new Course();

        if (!$course->getCourseById($course_id)) {
            $_SESSION['error'] = 'Khóa học không tìm thấy';
            header('Location: /course');
            exit;
        }

        if ($enrollment->enroll($_SESSION['user_id'], $course_id)) {
            $_SESSION['success'] = 'Đăng ký khóa học thành công';
            header('Location: /student/my-courses');
        } else {
            $_SESSION['error'] = 'Bạn đã đăng ký khóa học này rồi';
            header('Location: /course/detail/' . $course_id);
        }
        exit;
    }

    /**
     * Unenroll from course
     */
    public function delete($course_id = null) {
        if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'student') {
            $_SESSION['error'] = 'Vui lòng đăng nhập';
            header('Location: /auth/login');
            exit;
        }

        if (!$course_id) {
            $_SESSION['error'] = 'Khóa học không hợp lệ';
            header('Location: /course');
            exit;
        }

        $enrollment = new Enrollment();
        
        if ($enrollment->unenroll($_SESSION['user_id'], $course_id)) {
            $_SESSION['success'] = 'Hủy đăng ký khóa học thành công';
            header('Location: /student/my-courses');
        } else {
            $_SESSION['error'] = 'Đã xảy ra lỗi';
            header('Location: /student/my-courses');
        }
        exit;
    }
}
?>
