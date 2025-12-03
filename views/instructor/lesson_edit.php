<?php include VIEWS_PATH . '/layouts/header.php'; ?>

<div class="container mt-5">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <h2>Chỉnh Sửa Bài Học</h2>
            <p class="text-muted">Khóa học: <strong><?php echo htmlspecialchars($course['title']); ?></strong></p>

            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
            <?php endif; ?>

            <form method="POST" action="/instructor/edit-lesson-form/<?php echo $lesson['id']; ?>">
                <div class="form-group">
                    <label for="lesson_title">Tên Bài Học</label>
                    <input type="text" id="lesson_title" name="lesson_title" class="form-control" 
                           value="<?php echo htmlspecialchars($lesson['title']); ?>" required>
                </div>

                <div class="form-group">
                    <label for="lesson_content">Nội Dung Bài Học</label>
                    <textarea id="lesson_content" name="lesson_content" class="form-control" rows="6"><?php echo htmlspecialchars($lesson['content'] ?? ''); ?></textarea>
                </div>

                <div class="form-group">
                    <label for="video_url">URL Video (YouTube)</label>
                    <input type="text" id="video_url" name="video_url" class="form-control" 
                           value="<?php echo htmlspecialchars($lesson['video_url'] ?? ''); ?>" 
                           placeholder="https://www.youtube.com/watch?v=...">
                    <small class="form-text text-muted">
                        Hỗ trợ định dạng: 
                        <br>• https://www.youtube.com/watch?v=VIDEO_ID
                        <br>• https://youtu.be/VIDEO_ID
                        <br>• https://www.youtube.com/embed/VIDEO_ID
                        <br><strong>Hệ thống sẽ tự động chuyển đổi sang định dạng embed</strong>
                    </small>
                </div>

                <button type="submit" class="btn btn-primary">Cập Nhật Bài Học</button>
                <a href="/instructor/edit/<?php echo $course['id']; ?>" class="btn btn-secondary">Hủy</a>
            </form>
        </div>
    </div>
</div>

<?php include VIEWS_PATH . '/layouts/footer.php'; ?>
