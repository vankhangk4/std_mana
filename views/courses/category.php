<?php include VIEWS_PATH . '/layouts/header.php'; ?>

<div class="container mt-5">
    <div class="row mb-4">
        <div class="col-md-12">
            <h2><?php echo htmlspecialchars($category['name']); ?></h2>
            <p class="text-muted"><?php echo htmlspecialchars($category['description']); ?></p>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-12">
            <h5>Các Danh Mục Khác:</h5>
            <div class="btn-group" role="group">
                <a href="/course" class="btn btn-outline-secondary">Tất Cả</a>
                <?php foreach ($categories as $cat): ?>
                    <a href="/course/category/<?php echo $cat['id']; ?>" 
                       class="btn <?php echo $cat['id'] == $category_id ? 'btn-primary' : 'btn-outline-primary'; ?>">
                        <?php echo htmlspecialchars($cat['name']); ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <h3 class="mb-4">Khóa Học (<?php echo count($courses); ?>)</h3>
    
    <?php if (count($courses) > 0): ?>
        <div class="row">
            <?php foreach ($courses as $course): ?>
            <div class="col-md-4 mb-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo htmlspecialchars($course['title']); ?></h5>
                        <p class="card-text"><?php echo htmlspecialchars(substr($course['description'], 0, 80)); ?>...</p>
                        <p><strong><?php echo htmlspecialchars($course['instructor_name']); ?></strong></p>
                        <p class="text-muted">Học viên: <?php echo $course['total_enrolled']; ?></p>
                        <p class="text-danger"><strong><?php echo number_format($course['price'], 0); ?> đ</strong></p>
                        
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
                        
                        <a href="/course/detail/<?php echo $course['id']; ?>" class="btn btn-primary d-block mt-2">Xem Chi Tiết</a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="alert alert-info">
            Không có khóa học trong danh mục này.
        </div>
    <?php endif; ?>
</div>

<?php include VIEWS_PATH . '/layouts/footer.php'; ?>
