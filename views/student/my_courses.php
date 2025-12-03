<?php include VIEWS_PATH . '/layouts/header.php'; ?>

<div class="container mt-5">
    <h2>Khóa Học Của Tôi</h2>

    <?php if (isset($enrollments) && count($enrollments) > 0): ?>
        <div class="row">
            <?php 
            $lesson_progress = new LessonProgress();
            $user_id = $_SESSION['user_id'];
            
            foreach ($enrollments as $enrollment): 
                $calculated_progress = $lesson_progress->getCourseProgress($user_id, $enrollment['course_id']);
            ?>
            <div class="col-md-4 mb-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo htmlspecialchars($enrollment['title']); ?></h5>
                        <p class="card-text"><?php echo htmlspecialchars(substr($enrollment['description'], 0, 80)); ?>...</p>
                        <p class="text-muted">Giảng viên: <?php echo htmlspecialchars($enrollment['instructor_name']); ?></p>
                        <p>
                            <strong>Tiến độ: <?php echo $calculated_progress; ?>%</strong>
                            <div class="progress" style="height: 8px;">
                                <div class="progress-bar" style="width: <?php echo $calculated_progress; ?>%"></div>
                            </div>
                        </p>
                        <a href="/course/detail/<?php echo $enrollment['course_id']; ?>" class="btn btn-primary btn-sm">Xem Chi Tiết</a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="alert alert-info">Bạn chưa đăng ký khóa học nào. <a href="/course">Xem danh sách khóa học</a></div>
    <?php endif; ?>
</div>

<?php include VIEWS_PATH . '/layouts/footer.php'; ?>
