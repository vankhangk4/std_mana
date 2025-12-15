<?php
class InstructorController {

    private function requireAuth() {
        if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 1) {
            header('Location: /std_mana/auth/login');
            exit;
        }
    }

    public function dashboard() {
        $this->requireAuth();

        $course = new Course();
        $enrollment = new Enrollment();
        $instructor_id = $_SESSION['user_id'];

        $courses = $course->getCoursesByInstructor($instructor_id);

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

    private function handleCreateCourse() {
        $title = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $category_id = intval($_POST['category_id'] ?? 1);
        $price = floatval($_POST['price'] ?? 0);
        $duration_weeks = intval($_POST['duration_weeks'] ?? 1);
        $level = $_POST['level'] ?? 'Beginner';

        if ($title === '') {
            $_SESSION['error'] = 'Tên khóa học không được để trống';
            header('Location: /std_mana/instructor/create');
            exit;
        }

        if ($description === '') {
            $_SESSION['error'] = 'Mô tả khóa học không được để trống';
            header('Location: /std_mana/instructor/create');
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
            header('Location: /std_mana/instructor/dashboard');
        } else {
            $_SESSION['error'] = 'Đã xảy ra lỗi khi tạo khóa học';
            header('Location: /std_mana/instructor/create');
        }
        exit;
    }

    public function edit($course_id = null) {
        $this->requireAuth();

        if (!$course_id) {
            $_SESSION['error'] = 'Khóa học không tồn tại';
            header('Location: /std_mana/instructor/dashboard');
            exit;
        }

        $course = new Course();
        $course_data = $course->getCourseById($course_id);

        if (!$course_data || $course_data['instructor_id'] != $_SESSION['user_id']) {
            $_SESSION['error'] = 'Bạn không có quyền chỉnh sửa khóa học này';
            header('Location: /std_mana/instructor/dashboard');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handleEditCourse($course_id);
        } else {
            $lesson = new Lesson();
            $lessons = $lesson->getLessonsByCourse($course_id);

            $this->render('instructor/edit', [
                'page_title' => 'Chỉnh Sửa Khóa Học',
                'course' => $course_data,
                'lessons' => $lessons
            ]);
        }
    }

    private function handleEditCourse($course_id) {
        $title = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $category_id = intval($_POST['category_id'] ?? 1);
        $price = floatval($_POST['price'] ?? 0);
        $duration_weeks = intval($_POST['duration_weeks'] ?? 1);
        $level = $_POST['level'] ?? 'Beginner';
        $status = $_POST['status'] ?? 'draft';

        if ($title === '') {
            $_SESSION['error'] = 'Tên khóa học không được để trống';
            header('Location: /std_mana/instructor/edit/' . $course_id);
            exit;
        }

        if ($description === '') {
            $_SESSION['error'] = 'Mô tả khóa học không được để trống';
            header('Location: /std_mana/instructor/edit/' . $course_id);
            exit;
        }

        $course = new Course();
        if ($course->update($course_id, [
            'title' => $title,
            'description' => $description,
            'category_id' => $category_id,
            'price' => $price,
            'duration_weeks' => $duration_weeks,
            'level' => $level,
            'status' => $status
        ])) {
            $_SESSION['success'] = 'Khóa học đã được cập nhật thành công';
            header('Location: /std_mana/instructor/dashboard');
        } else {
            $_SESSION['error'] = 'Đã xảy ra lỗi khi cập nhật khóa học';
            header('Location: /std_mana/instructor/edit/' . $course_id);
        }
        exit;
    }

    public function add_lesson($course_id = null) {
        $this->requireAuth();

        if (!$course_id || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(400);
            exit;
        }

        $lesson_title = trim($_POST['lesson_title'] ?? '');
        $lesson_content = trim($_POST['lesson_content'] ?? '');
        $video_url = trim($_POST['video_url'] ?? '');
        $order = intval($_POST['order'] ?? 1);

        if ($lesson_title === '') {
            http_response_code(400);
            exit;
        }

        $lesson = new Lesson();
        if ($lesson->create([
            'course_id' => $course_id,
            'title' => $lesson_title,
            'content' => $lesson_content,
            'video_url' => $video_url,
            'order' => $order
        ])) {
            echo json_encode(['success' => true]);
        } else {
            http_response_code(500);
        }
        exit;
    }

    public function delete_lesson($lesson_id = null) {
        $this->requireAuth();

        if (!$lesson_id) {
            http_response_code(400);
            exit;
        }

        $lesson = new Lesson();
        if ($lesson->delete($lesson_id)) {
            echo json_encode(['success' => true]);
        } else {
            http_response_code(500);
        }
        exit;
    }

    public function edit_lesson_form($lesson_id = null) {
        $this->requireAuth();

        if (!$lesson_id) {
            header('Location: /std_mana/instructor/dashboard');
            exit;
        }

        $lesson = new Lesson();
        $lesson_data = $lesson->getLessonById($lesson_id);

        if (!$lesson_data) {
            header('Location: /std_mana/instructor/dashboard');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handleEditLessonForm($lesson_id);
        } else {
            $course = new Course();
            $course_data = $course->getCourseById($lesson_data['course_id']);

            $this->render('instructor/lesson_edit', [
                'lesson' => $lesson_data,
                'course' => $course_data
            ]);
        }
    }

    private function handleEditLessonForm($lesson_id) {
        $lesson_title = trim($_POST['lesson_title'] ?? '');
        $lesson_content = trim($_POST['lesson_content'] ?? '');
        $video_url = trim($_POST['video_url'] ?? '');

        if ($lesson_title === '') {
            header('Location: /std_mana/instructor/dashboard');
            exit;
        }

        $lesson = new Lesson();
        if ($lesson->update($lesson_id, [
            'title' => $lesson_title,
            'content' => $lesson_content,
            'video_url' => $video_url
        ])) {
            header('Location: /std_mana/instructor/dashboard');
        } else {
            header('Location: /std_mana/instructor/dashboard');
        }
        exit;
    }

    protected function render($view, $data = []) {
        extract($data);
        require VIEWS_PATH . '/' . $view . '.php';
    }
}
?>
