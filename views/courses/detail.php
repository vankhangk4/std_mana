<?php include VIEWS_PATH . '/layouts/header.php'; ?>

<div class="container mt-5">
    <div class="row">
        <div class="col-md-8">
            <h2><?php echo htmlspecialchars($course['title']); ?></h2>
            <p class="text-muted">Giảng viên: <strong><?php echo htmlspecialchars($course['instructor_name']); ?></strong></p>
            <p class="text-muted">Danh mục: <?php echo htmlspecialchars($course['category_name']); ?></p>

            <hr>

            <h4>Mô Tả</h4>
            <p><?php echo nl2br(htmlspecialchars($course['description'])); ?></p>

            <h4 class="mt-4">Bài Học (<?php echo count($lessons); ?>)</h4>
            <div class="list-group">
                <?php foreach ($lessons as $lesson): ?>
                <a href="/lesson/view/<?php echo $lesson['id']; ?>" class="list-group-item list-group-item-action">
                    <h5><?php echo htmlspecialchars($lesson['title']); ?></h5>
                    <p class="text-muted mb-0"><?php echo htmlspecialchars(substr($lesson['description'], 0, 80)); ?>...</p>
                </a>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5>Giá: <span class="text-danger"><?php echo number_format($course['price'], 0); ?> đ</span></h5>
                    <p>Học viên: <?php echo $course['total_enrolled']; ?></p>
                    <p>Thời lượng: <?php echo $course['duration'] ?? 'Không xác định'; ?></p>

                    <?php if ($isEnrolled): ?>
                        <a href="/student/my-courses" class="btn btn-success btn-block">Đã Đăng Ký</a>
                        <a href="/enrollment/delete/<?php echo $course['id']; ?>" class="btn btn-danger btn-block mt-2" onclick="return confirm('Xác nhận hủy đăng ký?')">Hủy Đăng Ký</a>
                    <?php else: ?>
                        <?php if (isset($_SESSION['user_id']) && $_SESSION['user_role'] === 'student'): ?>
                            <a href="/enrollment/create/<?php echo $course['id']; ?>" class="btn btn-primary btn-block">Đăng Ký Khóa Học</a>
                        <?php else: ?>
                            <a href="/auth/login" class="btn btn-primary btn-block">Đăng Nhập Để Đăng Ký</a>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include VIEWS_PATH . '/layouts/footer.php'; ?>
