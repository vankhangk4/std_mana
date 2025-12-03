<?php include VIEWS_PATH . '/layouts/header.php'; ?>

<div class="container mt-5">
    <div class="row">
        <div class="col-md-8">
            <h2><?php echo htmlspecialchars($lesson['title']); ?></h2>
            
            <?php if (!empty($lesson['video_url'])): ?>
            <div class="mb-4">
                <iframe width="100%" height="400" src="<?php echo htmlspecialchars($lesson['video_url']); ?>" frameborder="0" allowfullscreen></iframe>
            </div>
            <?php endif; ?>

            <div class="lesson-content">
                <?php echo nl2br(htmlspecialchars($lesson['content'])); ?>
            </div>

            <?php if (count($materials) > 0): ?>
            <h4 class="mt-5">Tài Liệu Học Tập</h4>
            <div class="list-group">
                <?php foreach ($materials as $material): ?>
                <a href="/assets/uploads/materials/<?php echo $material['file_path']; ?>" class="list-group-item list-group-item-action" download>
                    <h5><?php echo htmlspecialchars($material['title']); ?></h5>
                    <p class="text-muted mb-0"><?php echo $material['file_type']; ?> - <?php echo number_format($material['file_size'] / 1024, 2); ?> KB</p>
                </a>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5>Khóa học</h5>
                    <p><?php echo htmlspecialchars($course['title']); ?></p>
                    <a href="/course/detail/<?php echo $course['id']; ?>" class="btn btn-primary btn-block">Quay Lại</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include VIEWS_PATH . '/layouts/footer.php'; ?>
