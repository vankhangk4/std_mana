<?php include VIEWS_PATH . '/layouts/header.php'; ?>

<div class="container mt-5">
    <h1>Chào mừng đến nền tảng học trực tuyến</h1>
    
    <div class="row mt-4">
        <div class="col-md-3">
            <div class="card">
                <div class="card-body text-center">
                    <h3><?php echo count($courses); ?></h3>
                    <p>Khóa Học</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body text-center">
                    <h3><?php echo count($categories); ?></h3>
                    <p>Danh Mục</p>
                </div>
            </div>
        </div>
    </div>

    <h2 class="mt-5">Khóa Học Nổi Bật</h2>
    <div class="row mt-3">
        <?php foreach (array_slice($courses, 0, 6) as $course): ?>
        <div class="col-md-4 mb-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title"><?php echo htmlspecialchars($course['title']); ?></h5>
                    <p class="card-text"><?php echo htmlspecialchars(substr($course['description'], 0, 100)); ?>...</p>
                    <p><strong><?php echo htmlspecialchars($course['instructor_name']); ?></strong></p>
                    <p class="text-muted">Học viên: <?php echo $course['total_enrolled']; ?></p>
                    <a href="/course/detail/<?php echo $course['id']; ?>" class="btn btn-primary">Xem Chi Tiết</a>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<?php include VIEWS_PATH . '/layouts/footer.php'; ?>
