<?php include VIEWS_PATH . '/layouts/header.php'; ?>

<div class="container mt-5">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <h2>Sửa Thông Tin Người Dùng</h2>

            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
            <?php endif; ?>

            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <form method="POST" action="/admin/edit_user/<?php echo $user['id']; ?>">
                        <div class="form-group">
                            <label for="email">Email (Không thể sửa)</label>
                            <input type="email" id="email" class="form-control" value="<?php echo htmlspecialchars($user['email']); ?>" disabled>
                        </div>

                        <div class="form-group">
                            <label for="fullname">Tên Người Dùng</label>
                            <input type="text" id="fullname" name="fullname" class="form-control" value="<?php echo htmlspecialchars($user['fullname']); ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="role">Vai Trò</label>
                            <select id="role" name="role" class="form-control" required>
                                <option value="0" <?php echo $user['role'] === 0 ? 'selected' : ''; ?>>Học Viên</option>
                                <option value="1" <?php echo $user['role'] === 1 ? 'selected' : ''; ?>>Giảng Viên</option>
                                <option value="2" <?php echo $user['role'] === 2 ? 'selected' : ''; ?>>Quản Trị Viên</option>
                                <option value="17" <?php echo $user['role'] === 17 ? 'selected' : ''; ?>>Quản Lý Phê Duyệt</option>
                            </select>
                            <small class="form-text text-muted d-block mt-2">
                                • <strong>Học Viên:</strong> Có thể học các khóa học<br>
                                • <strong>Giảng Viên:</strong> Có thể tạo và quản lý khóa học<br>
                                • <strong>Quản Trị Viên:</strong> Quản lý hệ thống toàn bộ<br>
                                • <strong>Quản Lý Phê Duyệt:</strong> Phê duyệt khóa học từ giảng viên
                            </small>
                        </div>

                        <div class="form-group">
                            <label>Thông Tin Tạo Tài Khoản</label>
                            <input type="text" class="form-control" value="<?php echo date('d/m/Y H:i', strtotime($user['created_at'])); ?>" disabled>
                        </div>

                        <button type="submit" class="btn btn-primary">Lưu Thay Đổi</button>
                        <a href="/admin/users" class="btn btn-secondary">Hủy</a>
                        <?php if ($user['id'] != $_SESSION['user_id']): ?>
                            <button type="button" class="btn btn-danger" onclick="deleteUser(<?php echo $user['id']; ?>, '<?php echo htmlspecialchars($user['fullname']); ?>')">
                                <i class="fa fa-trash"></i> Xóa Người Dùng
                            </button>
                        <?php endif; ?>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function deleteUser(userId, userName) {
    if (confirm('Bạn chắc chắn muốn xóa người dùng "' + userName + '"?\n\nHành động này không thể hoàn tác!')) {
        fetch('/admin/delete_user/' + userId, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Xóa người dùng thành công');
                window.location.href = '/admin/users';
            } else {
                alert('Lỗi: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Có lỗi xảy ra');
        });
    }
}
</script>
