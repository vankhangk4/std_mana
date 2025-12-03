<?php include VIEWS_PATH . '/layouts/header.php'; ?>

<div class="container mt-5">
    <h2>Danh Sách Khóa Học</h2>

    <div class="row mb-4">
        <div class="col-md-6">
            <form method="GET" action="/course">
                <input type="text" name="search" class="form-control" placeholder="Tìm kiếm khóa học..." value="<?php echo htmlspecialchars($search); ?>">
            </form>
        </div>
        <div class="col-md-6">
            <select name="category" class="form-control" onchange="document.location='/course?category=' + this.value;">
                <option value="">-- Tất Cả Danh Mục --</option>
                <?php foreach ($categories as $cat): ?>
                <option value="<?php echo $cat['id']; ?>" <?php echo $cat['id'] == $category_id ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($cat['name']); ?>
                </option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>

    <div class="row">
        <?php if (count($courses) > 0): ?>
            <?php foreach ($courses as $course): ?>
            <div class="col-md-4 mb-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo htmlspecialchars($course['title']); ?></h5>
                        <p class="card-text"><?php echo htmlspecialchars(substr($course['description'], 0, 100)); ?>...</p>
                        <p><strong><?php echo htmlspecialchars($course['instructor_name']); ?></strong></p>
                        <p class="text-muted">Học viên: <?php echo $course['total_enrolled']; ?></p>
                        <p class="text-danger"><strong><?php echo number_format($course['price'], 0); ?> đ</strong></p>
                        <a href="/course/detail/<?php echo $course['id']; ?>" class="btn btn-primary btn-sm">Xem Chi Tiết</a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="alert alert-info">Không tìm thấy khóa học</div>
        <?php endif; ?>
    </div>
</div>

<?php include VIEWS_PATH . '/layouts/footer.php'; ?>
