<?php include VIEWS_PATH . '/layouts/header.php'; ?>

<div class="container mt-5">
    <h2>Dashboard Học Viên</h2>
    <p>Chào mừng <?php echo htmlspecialchars($_SESSION['user_name']); ?>!</p>

    <div class="row mt-4">
        <div class="col-md-3">
            <a href="/student/my-courses" class="btn-card text-decoration-none">
                <div class="card bg-info text-white" style="cursor: pointer; transition: transform 0.2s; min-height: 120px;">
                    <div class="card-body d-flex flex-column justify-content-center">
                        <h5>Khóa Học Đang Học</h5>
                        <h3><?php echo $ongoing_count; ?></h3>
                        <small>Bấm để xem chi tiết</small>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body d-flex flex-column justify-content-center" style="min-height: 120px;">
                    <h5>Đã Hoàn Thành</h5>
                    <h3><?php echo $completed_count; ?></h3>
                </div>
            </div>
        </div>
    </div>

    <?php if (count($ongoing_courses) > 0): ?>
    <h3 class="mt-5">Khóa Học Gần Đây</h3>
    <div class="row">
        <?php foreach (array_slice($ongoing_courses, 0, 3) as $course): ?>
        <div class="col-md-4 mb-3">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title"><?php echo htmlspecialchars($course['title']); ?></h5>
                    <p class="card-text text-muted"><?php echo htmlspecialchars(substr($course['description'], 0, 60)); ?>...</p>
                    <div class="mb-2">
                        <small>Tiến độ: <?php echo $course['progress']; ?>%</small>
                        <div class="progress" style="height: 5px;">
                            <div class="progress-bar" style="width: <?php echo $course['progress']; ?>%"></div>
                        </div>
                    </div>
                    <a href="/course/detail/<?php echo $course['course_id']; ?>" class="btn btn-sm btn-primary">Tiếp Tục Học</a>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <div class="mt-4">
        <a href="/student/my-courses" class="btn btn-primary">Xem Tất Cả Khóa Học</a>
    </div>
</div>

<?php include VIEWS_PATH . '/layouts/footer.php'; ?>
