<?php include VIEWS_PATH . '/layouts/header.php'; ?>

<div class="container mt-5">
    <h1>Chào mừng đến nền tảng học trực tuyến</h1>
    
    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body text-center">
                    <h3><?php echo count($courses); ?></h3>
                    <p>Khóa Học</p>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-body text-center">
                    <h3><?php echo count($categories); ?></h3>
                    <p>Danh Mục</p>
                </div>
            </div>
        </div>
    </div>

    <h3 class="mt-5 mb-3">Các Danh Mục Khóa Học</h3>
    <div class="mb-5">
        <select class="form-control" onchange="if(this.value) window.location.href = this.value;">
            <option value="">-- Chọn Danh Mục --</option>
            <option value="/std_mana/course">Tất Cả Khóa Học</option>
            <?php foreach ($categories as $cat): ?>
                <option value="/std_mana/course/category/<?php echo $cat['id']; ?>">
                    <?php echo htmlspecialchars($cat['name']); ?>
                </option>
            <?php endforeach; ?>
        </select>
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
                    
                    <?php
                    $is_enrolled = isset($enrolled_courses) && in_array($course['id'], $enrolled_courses);
                    ?>
                    
                    <?php if (isset($_SESSION['user_id']) && $_SESSION['user_role'] == 0): ?>
                        <?php if ($is_enrolled): ?>
                            <span class="badge badge-success">Đã Đăng Ký</span>
                        <?php else: ?>
                            <span class="badge badge-secondary">Chưa Đăng Ký</span>
                        <?php endif; ?>
                    <?php endif; ?>
                    
                    <a href="/std_mana/course/detail/<?php echo $course['id']; ?>" class="btn btn-primary d-block mt-2">Xem Chi Tiết</a>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<?php include VIEWS_PATH . '/layouts/footer.php'; ?>
