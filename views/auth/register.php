<?php include VIEWS_PATH . '/layouts/header.php'; ?>

<div class="container mt-5">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <h2>Đăng Ký</h2>

            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
            <?php endif; ?>

            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
            <?php endif; ?>

            <form method="POST" action="/auth/register">
                <div class="form-group">
                    <label for="name">Tên Đầy Đủ</label>
                    <input type="text" id="name" name="name" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="password">Mật Khẩu</label>
                    <input type="password" id="password" name="password" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="password_confirm">Xác Nhận Mật Khẩu</label>
                    <input type="password" id="password_confirm" name="password_confirm" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="role">Đăng Ký Như Là</label>
                    <select id="role" name="role" class="form-control" onchange="updateRoleInfo()">
                        <option value="student">Học Viên</option>
                        <option value="instructor">Giảng Viên</option>
                        <option value="admin">Quản Trị Viên</option>
                    </select>
                    <small class="form-text text-muted d-block mt-2" id="roleInfo">
                        Đăng ký như học viên để tham gia và học các khóa học.
                    </small>
                </div>

                <button type="submit" class="btn btn-primary btn-block">Đăng Ký</button>
                <a href="/auth/login" class="btn btn-link btn-block">Đã có tài khoản? Đăng nhập</a>
            </form>
        </div>
    </div>
</div>

<script>
function updateRoleInfo() {
    const role = document.getElementById('role').value;
    const roleInfo = document.getElementById('roleInfo');
    
    if (role === 'instructor') {
        roleInfo.innerHTML = '<i class="fa fa-info-circle text-info"></i> Đăng ký như giảng viên để tạo và quản lý các khóa học. Các khóa học được xuất bản phải được phê duyệt bởi quản trị viên trước khi công khai.';
    } else if (role === 'admin') {
        roleInfo.innerHTML = '<i class="fa fa-info-circle text-warning"></i> Đăng ký như quản trị viên để quản lý hệ thống, phê duyệt khóa học, và quản lý người dùng.';
    } else {
        roleInfo.innerHTML = 'Đăng ký như học viên để tham gia và học các khóa học.';
    }
}
</script>

<?php include VIEWS_PATH . '/layouts/footer.php'; ?>