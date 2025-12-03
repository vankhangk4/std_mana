<?php include VIEWS_PATH . '/layouts/header.php'; ?>

<div class="container mt-5">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <h2>Chỉnh Sửa Khóa Học</h2>

            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
            <?php endif; ?>

            <!-- Approval Status Alert -->
            <?php if (isset($course['approval_status'])): ?>
                <div class="alert alert-<?php 
                    echo $course['approval_status'] === 'approved' ? 'success' : 
                         ($course['approval_status'] === 'rejected' ? 'danger' : 'warning'); 
                ?>" role="alert">
                    <strong>Trạng Thái Phê Duyệt:</strong>
                    <?php 
                        echo $course['approval_status'] === 'approved' ? '✓ Đã Phê Duyệt' : 
                             ($course['approval_status'] === 'rejected' ? '✗ Bị Từ Chối' : '⏳ Chờ Phê Duyệt'); 
                    ?>
                    <?php if ($course['approval_status'] === 'pending' && $course['status'] === 'published'): ?>
                    <br><small>Khóa học của bạn đang chờ quản trị viên phê duyệt. Vui lòng chờ trong vài ngày.</small>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="/instructor/edit/<?php echo $course['id']; ?>">
                <div class="form-group">
                    <label for="title">Tên Khóa Học</label>
                    <input type="text" id="title" name="title" class="form-control" value="<?php echo htmlspecialchars($course['title']); ?>" required>
                </div>

                <div class="form-group">
                    <label for="description">Mô Tả</label>
                    <textarea id="description" name="description" class="form-control" rows="5" required><?php echo htmlspecialchars($course['description']); ?></textarea>
                </div>

                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="category_id">Danh Mục</label>
                        <select id="category_id" name="category_id" class="form-control">
                            <option value="1" <?php echo $course['category_id'] == 1 ? 'selected' : ''; ?>>Công Nghệ</option>
                            <option value="2" <?php echo $course['category_id'] == 2 ? 'selected' : ''; ?>>Kinh Doanh</option>
                            <option value="3" <?php echo $course['category_id'] == 3 ? 'selected' : ''; ?>>Sáng Tạo</option>
                        </select>
                    </div>

                    <div class="form-group col-md-6">
                        <label for="level">Mức Độ</label>
                        <select id="level" name="level" class="form-control">
                            <option value="Beginner" <?php echo $course['level'] === 'Beginner' ? 'selected' : ''; ?>>Beginner</option>
                            <option value="Intermediate" <?php echo $course['level'] === 'Intermediate' ? 'selected' : ''; ?>>Intermediate</option>
                            <option value="Advanced" <?php echo $course['level'] === 'Advanced' ? 'selected' : ''; ?>>Advanced</option>
                        </select>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="price">Giá (VNĐ)</label>
                        <input type="number" id="price" name="price" class="form-control" value="<?php echo $course['price']; ?>" step="1000">
                    </div>

                    <div class="form-group col-md-6">
                        <label for="duration_weeks">Thời Lượng (Tuần)</label>
                        <input type="number" id="duration_weeks" name="duration_weeks" class="form-control" value="<?php echo $course['duration_weeks']; ?>" min="1">
                    </div>
                </div>

                <div class="form-group">
                    <label for="status">Trạng Thái</label>
                    <select id="status" name="status" class="form-control">
                        <option value="draft" <?php echo $course['status'] === 'draft' ? 'selected' : ''; ?>>Nháp</option>
                        <option value="published" <?php echo $course['status'] === 'published' ? 'selected' : ''; ?>>Xuất Bản</option>
                        <option value="archived" <?php echo $course['status'] === 'archived' ? 'selected' : ''; ?>>Lưu Trữ</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-primary">Cập Nhật Khóa Học</button>
                <a href="/instructor/dashboard" class="btn btn-secondary">Hủy</a>
            </form>

            <hr class="my-5">

            <!-- Lesson Management Section -->
            <h3>Quản Lý Bài Giảng</h3>
            
            <!-- Add Lesson Form -->
            <div class="card mb-4">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">Thêm Bài Giảng Mới</h5>
                </div>
                <div class="card-body">
                    <form id="addLessonForm">
                        <div class="form-group">
                            <label for="lesson_title">Tên Bài Giảng</label>
                            <input type="text" id="lesson_title" name="lesson_title" class="form-control" placeholder="Nhập tên bài giảng" required>
                        </div>

                        <div class="form-group">
                            <label for="lesson_content">Nội Dung</label>
                            <textarea id="lesson_content" name="lesson_content" class="form-control" rows="4" placeholder="Nhập nội dung bài giảng"></textarea>
                        </div>

                        <div class="form-group">
                            <label for="video_url">URL Video (YouTube)</label>
                            <input type="text" id="video_url" name="video_url" class="form-control" placeholder="https://www.youtube.com/watch?v=...">
                            <small class="form-text text-muted">
                                Hỗ trợ định dạng: 
                                <br>• https://www.youtube.com/watch?v=dQw4w9WgXcQ
                                <br>• https://youtu.be/dQw4w9WgXcQ
                                <br>• https://www.youtube.com/embed/dQw4w9WgXcQ
                                <br><strong>Hệ thống sẽ tự động chuyển đổi sang định dạng embed</strong>
                            </small>
                        </div>

                        <div class="form-group">
                            <label for="order">Thứ Tự Bài Giảng</label>
                            <input type="number" id="order" name="order" class="form-control" value="<?php echo isset($lessons) ? count($lessons) + 1 : 1; ?>" min="1">
                        </div>

                        <button type="button" class="btn btn-success" onclick="addLesson(<?php echo $course['id']; ?>)">
                            <i class="fas fa-plus"></i> Thêm Bài Giảng
                        </button>
                    </form>
                </div>
            </div>

            <!-- Existing Lessons List -->
            <?php if (isset($lessons) && count($lessons) > 0): ?>
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">Danh Sách Bài Giảng (<?php echo count($lessons); ?>)</h5>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>STT</th>
                                <th>Tên Bài Giảng</th>
                                <th>Video</th>
                                <th>Hành Động</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($lessons as $index => $lesson): ?>
                            <tr>
                                <td><?php echo $index + 1; ?></td>
                                <td><?php echo htmlspecialchars($lesson['title']); ?></td>
                                <td>
                                    <?php if ($lesson['video_url']): ?>
                                        <span class="badge badge-info">📹 Có Video</span>
                                    <?php else: ?>
                                        <span class="badge badge-secondary">Không có video</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a href="/instructor/edit-lesson-form/<?php echo $lesson['id']; ?>" class="btn btn-sm btn-primary">
                                        <i class="fas fa-edit"></i> Sửa
                                    </a>
                                    <button class="btn btn-sm btn-danger" onclick="deleteLesson(<?php echo $lesson['id']; ?>)">
                                        <i class="fas fa-trash"></i> Xóa
                                    </button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php else: ?>
            <div class="alert alert-info">
                Khóa học này chưa có bài giảng nào. Hãy thêm bài giảng đầu tiên!
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
function addLesson(courseId) {
    const lessonTitle = document.getElementById('lesson_title').value.trim();
    const lessonContent = document.getElementById('lesson_content').value.trim();
    const videoUrl = document.getElementById('video_url').value.trim();
    const order = document.getElementById('order').value;

    if (!lessonTitle) {
        alert('Vui lòng nhập tên bài giảng');
        return;
    }

    const formData = new FormData();
    formData.append('lesson_title', lessonTitle);
    formData.append('lesson_content', lessonContent);
    formData.append('video_url', videoUrl);
    formData.append('order', order);

    fetch('/instructor/add-lesson/' + courseId, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Bài giảng đã được thêm thành công!');
            location.reload();
        } else {
            alert('Lỗi: ' + (data.message || 'Không thể thêm bài giảng'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Đã xảy ra lỗi khi thêm bài giảng');
    });
}

function deleteLesson(lessonId) {
    if (confirm('Bạn chắc chắn muốn xóa bài giảng này?')) {
        fetch('/instructor/delete-lesson/' + lessonId, {
            method: 'POST'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Bài giảng đã được xóa!');
                location.reload();
            } else {
                alert('Lỗi: ' + (data.message || 'Không thể xóa bài giảng'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Đã xảy ra lỗi khi xóa bài giảng');
        });
    }
}
</script>

<?php include VIEWS_PATH . '/layouts/footer.php'; ?>
