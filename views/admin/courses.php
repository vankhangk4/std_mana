<?php include VIEWS_PATH . '/layouts/header.php'; ?>

<div class="container mt-5">
    <div class="mb-4">
        <h2>Danh Sách Khóa Học</h2>
        <p class="text-muted">Quản lý tất cả khóa học đã được công bố</p>
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

    <!-- Courses Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-bottom">
            <h5 class="mb-0">Tổng Cộng: <strong><?php echo count($courses); ?></strong> khóa học</h5>
        </div>
        
        <?php if (count($courses) > 0): ?>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Tên Khóa Học</th>
                        <th>Giảng Viên</th>
                        <th>Danh Mục</th>
                        <th>Trạng Thái</th>
                        <th>Ngày Tạo</th>
                        <th>Hành Động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($courses as $course): ?>
                    <tr>
                        <td><?php echo $course['id']; ?></td>
                        <td>
                            <strong><?php echo htmlspecialchars($course['title']); ?></strong>
                            <br>
                            <small class="text-muted"><?php echo htmlspecialchars(substr($course['description'], 0, 50)); ?>...</small>
                        </td>
                        <td><?php echo htmlspecialchars($course['instructor_name']); ?></td>
                        <td><?php echo htmlspecialchars($course['category_name']); ?></td>
                        <td>
                            <?php 
                                $status = $course['approval_status'] ?? 'published';
                                $badge_class = '';
                                switch ($status) {
                                    case 'approved':
                                        $badge_class = 'badge-success';
                                        $status_text = 'Đã Phê Duyệt';
                                        break;
                                    case 'rejected':
                                        $badge_class = 'badge-danger';
                                        $status_text = 'Bị Từ Chối';
                                        break;
                                    case 'pending':
                                        $badge_class = 'badge-warning';
                                        $status_text = 'Chờ Duyệt';
                                        break;
                                    default:
                                        $badge_class = 'badge-info';
                                        $status_text = 'Công Bố';
                                }
                            ?>
                            <span class="badge <?php echo $badge_class; ?>"><?php echo $status_text; ?></span>
                        </td>
                        <td>
                            <small class="text-muted">
                                <?php echo date('d/m/Y', strtotime($course['created_at'])); ?>
                            </small>
                        </td>
                        <td>
                            <a href="/course/detail/<?php echo $course['id']; ?>" class="btn btn-sm btn-info" target="_blank">
                                <i class="fa fa-eye"></i> Xem
                            </a>
                            <a href="/instructor/edit/<?php echo $course['id']; ?>" class="btn btn-sm btn-warning">
                                <i class="fa fa-edit"></i> Sửa
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
        <div class="card-body">
            <div class="alert alert-info mb-0">
                <i class="fa fa-info-circle"></i> Không có khóa học nào được công bố.
            </div>
        </div>
        <?php endif; ?>
    </div>

    <a href="/admin/dashboard" class="btn btn-secondary mt-3">Quay Lại Dashboard</a>
</div>

<?php include VIEWS_PATH . '/layouts/footer.php'; ?>
