<?php
/**
 * Instructor Controller
 * Handles instructor dashboard and course management
 */

class InstructorController {
    
    /**
     * Check if user is logged in and is an instructor
     */
    private function requireAuth() {
        if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 1) {
            header('Location: /auth/login');
            exit;
        }
    }

    /**
     * Show instructor dashboard
     */
    public function dashboard() {
        $this->requireAuth();
        
        $course = new Course();
        $enrollment = new Enrollment();
        $instructor_id = $_SESSION['user_id'];
        
        // Get instructor's courses
        $courses = $course->getCoursesByInstructor($instructor_id);
        
        // Calculate total students enrolled
        $total_students = 0;
        foreach ($courses as $course_data) {
            $course_enrollments = $enrollment->getCourseEnrollments($course_data['id']);
            $total_students += count($course_enrollments);
        }
        
        $this->render('instructor/dashboard', [
            'page_title' => 'Dashboard Giảng Viên',
            'courses' => $courses,
            'total_students' => $total_students
        ]);
    }

    /**
     * Show create course form
     */
    public function create() {
        $this->requireAuth();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handleCreateCourse();
        } else {
            $this->render('instructor/create', [
                'page_title' => 'Tạo Khóa Học Mới'
            ]);
        }
    }

    /**
     * Handle create course form submission
     */
    private function handleCreateCourse() {
        $title = isset($_POST['title']) ? trim($_POST['title']) : '';
        $description = isset($_POST['description']) ? trim($_POST['description']) : '';
        $category_id = isset($_POST['category_id']) ? intval($_POST['category_id']) : 1;
        $price = isset($_POST['price']) ? floatval($_POST['price']) : 0;
        $duration_weeks = isset($_POST['duration_weeks']) ? intval($_POST['duration_weeks']) : 1;
        $level = isset($_POST['level']) ? $_POST['level'] : 'Beginner';

        // Validation
        if (empty($title)) {
            $_SESSION['error'] = 'Tên khóa học không được để trống';
            header('Location: /instructor/create');
            exit;
        }

        if (empty($description)) {
            $_SESSION['error'] = 'Mô tả khóa học không được để trống';
            header('Location: /instructor/create');
            exit;
        }

        $course = new Course();
        if ($course->create([
            'title' => $title,
            'description' => $description,
            'category_id' => $category_id,
            'instructor_id' => $_SESSION['user_id'],
            'price' => $price,
            'duration_weeks' => $duration_weeks,
            'level' => $level,
            'status' => 'draft'
        ])) {
            $_SESSION['success'] = 'Khóa học đã được tạo thành công';
            header('Location: /instructor/dashboard');
        } else {
            $_SESSION['error'] = 'Đã xảy ra lỗi khi tạo khóa học';
            header('Location: /instructor/create');
        }
        exit;
    }

    /**
     * Show edit course form
     */
    public function edit($course_id = null) {
        $this->requireAuth();
        
        if (!$course_id) {
            $_SESSION['error'] = 'Khóa học không tồn tại';
            header('Location: /instructor/dashboard');
            exit;
        }

        $course = new Course();
        $course_data = $course->getCourseById($course_id);

        // Check if course belongs to instructor
        if (!$course_data || $course_data['instructor_id'] != $_SESSION['user_id']) {
            $_SESSION['error'] = 'Bạn không có quyền chỉnh sửa khóa học này';
            header('Location: /instructor/dashboard');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handleEditCourse($course_id);
        } else {
            // Load lessons for this course
            $lesson = new Lesson();
            $lessons = $lesson->getLessonsByCourse($course_id);
            
            $this->render('instructor/edit', [
                'page_title' => 'Chỉnh Sửa Khóa Học',
                'course' => $course_data,
                'lessons' => $lessons
            ]);
        }
    }

    /**
     * Handle edit course form submission
     */
    private function handleEditCourse($course_id) {
        $title = isset($_POST['title']) ? trim($_POST['title']) : '';
        $description = isset($_POST['description']) ? trim($_POST['description']) : '';
        $category_id = isset($_POST['category_id']) ? intval($_POST['category_id']) : 1;
        $price = isset($_POST['price']) ? floatval($_POST['price']) : 0;
        $duration_weeks = isset($_POST['duration_weeks']) ? intval($_POST['duration_weeks']) : 1;
        $level = isset($_POST['level']) ? $_POST['level'] : 'Beginner';
        $status = isset($_POST['status']) ? $_POST['status'] : 'draft';

        // Validation
        if (empty($title)) {
            $_SESSION['error'] = 'Tên khóa học không được để trống';
            header('Location: /instructor/edit/' . $course_id);
            exit;
        }

        if (empty($description)) {
            $_SESSION['error'] = 'Mô tả khóa học không được để trống';
            header('Location: /instructor/edit/' . $course_id);
            exit;
        }

        // Get current course to check previous status
        $course = new Course();
        $current_course = $course->getCourseById($course_id);
        $previous_status = $current_course['status'] ?? 'draft';

        if ($course->update($course_id, [
            'title' => $title,
            'description' => $description,
            'category_id' => $category_id,
            'price' => $price,
            'duration_weeks' => $duration_weeks,
            'level' => $level,
            'status' => $status
        ])) {
            // If changing from draft to published, create approval request
            if ($previous_status !== 'published' && $status === 'published') {
                $this->createApprovalRequest($course_id);
                $_SESSION['success'] = 'Khóa học đã được gửi để phê duyệt. Vui lòng chờ quản trị viên xem xét.';
            } else {
                $_SESSION['success'] = 'Khóa học đã được cập nhật thành công';
            }
            header('Location: /instructor/dashboard');
        } else {
            $_SESSION['error'] = 'Đã xảy ra lỗi khi cập nhật khóa học';
            header('Location: /instructor/edit/' . $course_id);
        }
        exit;
    }

    /**
     * Create approval request for course
     */
    private function createApprovalRequest($course_id) {
        $db = Database::getInstance();
        
        // Check if approval request already exists
        $query = "SELECT id FROM course_approvals WHERE course_id = :course_id AND status = 'pending'";
        $existing = $db->query($query, [':course_id' => $course_id]);
        
        if (!empty($existing)) {
            return; // Already has pending request
        }
        
        // Create new approval request
        $query = "INSERT INTO course_approvals (course_id, instructor_id, status, submitted_at)
                  VALUES (:course_id, :instructor_id, 'pending', NOW())";
        
        $db->execute($query, [
            ':course_id' => $course_id,
            ':instructor_id' => $_SESSION['user_id']
        ]);
    }

    /**
     * Add lesson to course
     */
    public function add_lesson($course_id = null) {
        $this->requireAuth();
        
        if (!$course_id) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Course ID required']);
            exit;
        }

        // Verify course belongs to instructor
        $course = new Course();
        $course_data = $course->getCourseById($course_id);
        
        if (!$course_data || $course_data['instructor_id'] != $_SESSION['user_id']) {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            exit;
        }

        $lesson_title = isset($_POST['lesson_title']) ? trim($_POST['lesson_title']) : '';
        $lesson_content = isset($_POST['lesson_content']) ? trim($_POST['lesson_content']) : '';
        $video_url = isset($_POST['video_url']) ? trim($_POST['video_url']) : '';
        $order = isset($_POST['order']) ? intval($_POST['order']) : 1;

        if (empty($lesson_title)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Lesson title required']);
            exit;
        }

        // Convert YouTube URL to embed format if provided
        if (!empty($video_url)) {
            $video_url = $this->convertToEmbedUrl($video_url);
        }

        $lesson = new Lesson();
        if ($lesson->create([
            'course_id' => $course_id,
            'title' => $lesson_title,
            'content' => $lesson_content,
            'video_url' => $video_url,
            'order' => $order
        ])) {
            http_response_code(200);
            echo json_encode(['success' => true, 'message' => 'Lesson added successfully']);
        } else {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Error adding lesson']);
        }
        exit;
    }

    /**
     * Convert YouTube URL to embed format
     */
    private function convertToEmbedUrl($url) {
        // If already in embed format, return as is
        if (strpos($url, 'youtube.com/embed/') !== false) {
            return $url;
        }

        // Extract video ID from various YouTube URL formats
        $video_id = null;

        // Format: https://www.youtube.com/watch?v=VIDEO_ID
        if (preg_match('/youtube\.com\/watch\?v=([a-zA-Z0-9_-]+)/', $url, $matches)) {
            $video_id = $matches[1];
        }
        // Format: https://youtu.be/VIDEO_ID
        elseif (preg_match('/youtu\.be\/([a-zA-Z0-9_-]+)/', $url, $matches)) {
            $video_id = $matches[1];
        }
        // Format: https://www.youtube.com/embed/VIDEO_ID (already correct)
        elseif (preg_match('/youtube\.com\/embed\/([a-zA-Z0-9_-]+)/', $url, $matches)) {
            $video_id = $matches[1];
        }

        // If video ID found, return embed URL
        if ($video_id) {
            return 'https://www.youtube.com/embed/' . $video_id;
        }

        // If not a YouTube URL, return original
        return $url;
    }

    /**
     * Delete lesson
     */
    public function delete_lesson($lesson_id = null) {
        $this->requireAuth();
        
        if (!$lesson_id) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Lesson ID required']);
            exit;
        }

        $lesson_model = new Lesson();
        $lesson_data = $lesson_model->getLessonById($lesson_id);

        if (!$lesson_data) {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'Lesson not found']);
            exit;
        }

        // Verify course belongs to instructor
        $course = new Course();
        $course_data = $course->getCourseById($lesson_data['course_id']);
        
        if ($course_data['instructor_id'] != $_SESSION['user_id']) {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }

        if ($lesson_model->delete($lesson_id)) {
            http_response_code(200);
            echo json_encode(['success' => true, 'message' => 'Lesson deleted']);
        } else {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Error deleting lesson']);
        }
        exit;
    }

    /**
     * Show edit lesson form
     */
    public function edit_lesson_form($lesson_id = null) {
        $this->requireAuth();
        
        if (!$lesson_id) {
            $_SESSION['error'] = 'Bài học không tồn tại';
            header('Location: /instructor/dashboard');
            exit;
        }

        $lesson_model = new Lesson();
        $lesson_data = $lesson_model->getLessonById($lesson_id);

        if (!$lesson_data) {
            $_SESSION['error'] = 'Bài học không tồn tại';
            header('Location: /instructor/dashboard');
            exit;
        }

        // Verify course belongs to instructor
        $course = new Course();
        $course_data = $course->getCourseById($lesson_data['course_id']);
        
        if ($course_data['instructor_id'] != $_SESSION['user_id']) {
            $_SESSION['error'] = 'Bạn không có quyền chỉnh sửa bài học này';
            header('Location: /instructor/dashboard');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handleEditLessonForm($lesson_id);
        } else {
            $this->render('instructor/lesson_edit', [
                'page_title' => 'Chỉnh Sửa Bài Học',
                'lesson' => $lesson_data,
                'course' => $course_data
            ]);
        }
    }

    /**
     * Handle edit lesson form submission
     */
    private function handleEditLessonForm($lesson_id) {
        $lesson_title = isset($_POST['lesson_title']) ? trim($_POST['lesson_title']) : '';
        $lesson_content = isset($_POST['lesson_content']) ? trim($_POST['lesson_content']) : '';
        $video_url = isset($_POST['video_url']) ? trim($_POST['video_url']) : '';

        if (empty($lesson_title)) {
            $_SESSION['error'] = 'Tên bài học không được để trống';
            header('Location: /instructor/edit-lesson-form/' . $lesson_id);
            exit;
        }

        $lesson_model = new Lesson();
        $lesson_data = $lesson_model->getLessonById($lesson_id);

        // Verify course belongs to instructor
        $course = new Course();
        $course_data = $course->getCourseById($lesson_data['course_id']);
        
        if ($course_data['instructor_id'] != $_SESSION['user_id']) {
            $_SESSION['error'] = 'Bạn không có quyền chỉnh sửa bài học này';
            header('Location: /instructor/dashboard');
            exit;
        }

        // Convert YouTube URL to embed format if provided
        if (!empty($video_url)) {
            $video_url = $this->convertToEmbedUrl($video_url);
        }

        $update_data = [
            'title' => $lesson_title,
            'content' => $lesson_content
        ];
        
        if (!empty($video_url)) {
            $update_data['video_url'] = $video_url;
        }

        if ($lesson_model->update($lesson_id, $update_data)) {
            $_SESSION['success'] = 'Bài học đã được cập nhật thành công';
            header('Location: /instructor/edit/' . $lesson_data['course_id']);
        } else {
            $_SESSION['error'] = 'Đã xảy ra lỗi khi cập nhật bài học';
            header('Location: /instructor/edit-lesson-form/' . $lesson_id);
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
