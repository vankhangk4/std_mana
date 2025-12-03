<?php include VIEWS_PATH . '/layouts/header.php'; ?>

<div class="container mt-5">
    <div class="row">
        <div class="col-md-8">
            <h2><?php echo htmlspecialchars($lesson['title']); ?></h2>
            <p class="text-muted">Khóa học: <?php echo htmlspecialchars($course['title']); ?></p>
            
            <div class="alert alert-info">
                <strong>Tiến độ khóa học: <?php echo $course_progress; ?>%</strong>
                <div class="progress" style="height: 20px;">
                    <div class="progress-bar" role="progressbar" style="width: <?php echo $course_progress; ?>%" aria-valuenow="<?php echo $course_progress; ?>" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
            </div>
            
            <?php if (!empty($lesson['video_url'])): ?>
            <div class="mb-4">
                <iframe width="100%" height="400" src="<?php echo htmlspecialchars($lesson['video_url']); ?>" frameborder="0" allowfullscreen></iframe>
            </div>
            <?php endif; ?>

            <div class="lesson-content bg-light p-4 rounded">
                <h5>Nội Dung Bài Học</h5>
                <hr>
                <?php echo nl2br(htmlspecialchars($lesson['content'])); ?>
            </div>

            <div class="mt-4">
                <?php if ($is_lesson_completed): ?>
                    <button class="btn btn-success btn-lg" disabled>
                        <i class="fas fa-check-circle"></i> Bài Học Đã Hoàn Thành
                    </button>
                <?php else: ?>
                    <button class="btn btn-primary btn-lg" id="markCompleteBtn" onclick="markLessonComplete(<?php echo $course['id']; ?>, <?php echo $lesson['id']; ?>)">
                        <i class="fas fa-check"></i> Đánh Dấu Đã Hoàn Thành
                    </button>
                <?php endif; ?>
                <a href="/course/detail/<?php echo $course['id']; ?>" class="btn btn-secondary btn-lg">Quay Lại Khóa Học</a>
            </div>

            <?php if (isset($materials) && count($materials) > 0): ?>
            <h4 class="mt-5">Tài Liệu Học Tập</h4>
            <div class="list-group">
                <?php foreach ($materials as $material): ?>
                <a href="/assets/uploads/materials/<?php echo htmlspecialchars($material['file_path']); ?>" class="list-group-item list-group-item-action" download>
                    <h5><?php echo htmlspecialchars($material['filename']); ?></h5>
                    <p class="text-muted mb-0"><?php echo htmlspecialchars($material['file_type']); ?></p>
                </a>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>

        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title">Các Bài Học Khác</h5>
                    <div class="list-group list-group-flush">
                        <?php foreach ($lessons as $l): ?>
                        <a href="/student/lesson/<?php echo $course['id']; ?>/<?php echo $l['id']; ?>" 
                           class="list-group-item list-group-item-action <?php echo ($l['id'] == $lesson['id']) ? 'active' : ''; ?>">
                            <h6 class="mb-0"><?php echo htmlspecialchars($l['title']); ?></h6>
                            <?php if ($l['video_url']): ?>
                                <small class="text-muted">📹 Có Video</small>
                            <?php endif; ?>
                        </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            
            <a href="/course/detail/<?php echo $course['id']; ?>" class="btn btn-secondary btn-block">Quay Lại Khóa Học</a>
        </div>
    </div>
</div>

<script>
function markLessonComplete(courseId, lessonId) {
    fetch('/student/mark-lesson-complete/' + courseId + '/' + lessonId, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update progress bar
            document.querySelector('.progress-bar').style.width = data.progress + '%';
            document.querySelector('.progress-bar').setAttribute('aria-valuenow', data.progress);
            
            // Update button
            const btn = document.getElementById('markCompleteBtn');
            btn.innerHTML = '<i class="fas fa-check-circle"></i> Bài Học Đã Hoàn Thành';
            btn.className = 'btn btn-success btn-lg';
            btn.disabled = true;
            btn.onclick = null;
            
            // Show success message
            alert('Bài học đã được đánh dấu là hoàn thành!');
            location.reload();
        } else {
            alert('Lỗi: ' + (data.message || 'Không thể cập nhật tiến độ'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Đã xảy ra lỗi khi cập nhật tiến độ');
    });
}
</script>

<?php include VIEWS_PATH . '/layouts/footer.php'; ?>
