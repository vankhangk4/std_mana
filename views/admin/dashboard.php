<?php include VIEWS_PATH . '/layouts/header.php'; ?>

<div class="container-fluid mt-3">
    <h2>Bảng Điều Khiển Quản Trị</h2>

    <div class="row mt-4">
        <div class="col-md-3">
            <a href="/admin/users" class="card-link text-decoration-none">
                <div class="card border-0 shadow-sm" style="cursor: pointer;">
                    <div class="card-body text-center py-4">
                        <h4 class="text-muted mb-2">Tổng Người Dùng</h4>
                        <h1 class="text-primary"><?php echo $totalUsers; ?></h1>
                        <small class="text-muted">Nhấp để xem chi tiết</small>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-3">
            <a href="/admin/courses" class="card-link text-decoration-none">
                <div class="card border-0 shadow-sm" style="cursor: pointer;">
                    <div class="card-body text-center py-4">
                        <h4 class="text-muted mb-2">Khóa Học</h4>
                        <h1 class="text-warning"><?php echo $totalCourses; ?></h1>
                        <small class="text-muted">Nhấp để xem chi tiết</small>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-3">
            <a href="/admin/approvals" class="card-link text-decoration-none">
                <div class="card border-0 shadow-sm" style="cursor: pointer;">
                    <div class="card-body text-center py-4">
                        <h4 class="text-muted mb-2">Chờ Duyệt</h4>
                        <h1 class="text-danger"><?php echo isset($pending_count) ? $pending_count : 0; ?></h1>
                        <small class="text-muted">Khóa học đang chờ duyệt</small>
                    </div>
                </div>
            </a>
        </div>

    <!-- Pending Approvals Alert -->
    <?php if (isset($pending_count) && $pending_count > 0): ?>
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <i class="fa fa-hourglass-half"></i>
                <strong><?php echo $pending_count; ?> khóa học</strong> đang chờ phê duyệt!
                <a href="/admin/approvals" class="alert-link">Xem ngay</a>
                <button type="button" class="close" data-dismiss="alert">
                    <span>&times;</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Pending Approvals List -->
    <?php if (isset($pending_approvals) && count($pending_approvals) > 0): ?>
    <div class="row mt-4">
        <div class="col-md-12">
            <h4 class="mb-3">Các Yêu Cầu Phê Duyệt Gần Đây</h4>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Khóa Học</th>
                            <th>Giảng Viên</th>
                            <th>Gửi Lúc</th>
                            <th>Hành Động</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach (array_slice($pending_approvals, 0, 5) as $approval): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($approval['title']); ?></td>
                            <td><?php echo htmlspecialchars($approval['instructor_name']); ?></td>
                            <td>
                                <small class="text-muted">
                                    <?php echo date('d/m/Y H:i', strtotime($approval['submitted_at'])); ?>
                                </small>
                            </td>
                            <td>
                                <a href="/admin/approvals" class="btn btn-sm btn-primary">
                                    <i class="fa fa-eye"></i> Xem Chi Tiết
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php endif; ?>
    <?php endif; ?>
</div>

<?php include VIEWS_PATH . '/layouts/footer.php'; ?>
