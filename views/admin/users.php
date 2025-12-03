<?php include VIEWS_PATH . '/layouts/header.php'; ?>

<div class="container mt-5">
    <div class="mb-4">
        <h2><?php echo htmlspecialchars($role_name); ?></h2>
        <p class="text-muted">Quản lý người dùng hệ thống</p>
    </div>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
            <button type="button" class="close" data-dismiss="alert">
                <span>&times;</span>
            </button>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
            <button type="button" class="close" data-dismiss="alert">
                <span>&times;</span>
            </button>
        </div>
    <?php endif; ?>

    <!-- Filter Buttons -->
    <div class="mb-3">
        <a href="/admin/users" class="btn <?php echo $role === null ? 'btn-primary' : 'btn-outline-primary'; ?>">
            Tất Cả (<?php echo count($users); ?>)
        </a>
        <a href="/admin/users/0" class="btn <?php echo $role === 0 ? 'btn-success' : 'btn-outline-success'; ?>">
            Học Viên
        </a>
        <a href="/admin/users/1" class="btn <?php echo $role === 1 ? 'btn-info' : 'btn-outline-info'; ?>">
            Giảng Viên
        </a>
        <a href="/admin/users/2" class="btn <?php echo $role === 2 ? 'btn-danger' : 'btn-outline-danger'; ?>">
            Quản Trị Viên
        </a>
    </div>

    <!-- Users Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-bottom">
            <h5 class="mb-0">Danh Sách Người Dùng</h5>
        </div>
        <?php if (count($users) > 0): ?>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Tên Người Dùng</th>
                            <th>Email</th>
                            <th>Vai Trò</th>
                            <th>Ngày Tạo</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?php echo $user['id']; ?></td>
                            <td>
                                <a href="/admin/edit_user/<?php echo $user['id']; ?>" class="text-decoration-none text-dark" style="cursor: pointer;">
                                    <strong><?php echo htmlspecialchars($user['fullname']); ?></strong>
                                </a>
                            </td>
                            <td><?php echo htmlspecialchars($user['email']); ?></td>
                            <td>
                                <span class="badge badge-<?php 
                                    echo $user['role'] == 0 ? 'success' : 
                                         ($user['role'] == 1 ? 'info' : 
                                         ($user['role'] == 2 ? 'danger' : 'warning'));
                                ?>">
                                    <?php 
                                    echo $user['role'] == 0 ? 'Học Viên' : 
                                         ($user['role'] == 1 ? 'Giảng Viên' : 
                                         ($user['role'] == 2 ? 'Quản Trị Viên' : 'Quản Lý Phê Duyệt'));
                                    ?>
                                </span>
                            </td>
                            <td>
                                <small class="text-muted">
                                    <?php echo date('d/m/Y', strtotime($user['created_at'])); ?>
                                </small>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="card-body">
                <div class="alert alert-info mb-0">
                    <i class="fa fa-info-circle"></i> Không có người dùng nào trong danh mục này.
                </div>
            </div>
        <?php endif; ?>
    </div>

    <a href="/admin/dashboard" class="btn btn-secondary mt-3">Quay Lại Dashboard</a>
</div>

<script>
function deleteUser(userId, userName) {
    if (confirm('Bạn chắc chắn muốn xóa người dùng "' + userName + '"?')) {
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
                location.reload();
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

<?php include VIEWS_PATH . '/layouts/footer.php'; ?>
