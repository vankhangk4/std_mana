<?php include VIEWS_PATH . '/layouts/header.php'; ?>

<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2>Quản Lý Khóa Học</h2>
            <p class="text-muted">Chào mừng, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!</p>
        </div>
        <a href="/std_mana/instructor/create" class="btn btn-success btn-lg">
            <i class="fa fa-plus"></i> Tạo Khóa Học Mới
        </a>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center py-4">
                    <h4 class="text-muted mb-2">Khóa Học</h4>
                    <h1 class="text-primary"><?php echo isset($courses) ? count($courses) : 0; ?></h1>
                    <small class="text-muted">Tổng số khóa học</small>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center py-4">
                    <h4 class="text-muted mb-2">Học Viên</h4>
                    <h1 class="text-success"><?php echo isset($total_students) ? $total_students : 0; ?></h1>
                    <small class="text-muted">Tổng số học viên đăng ký</small>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center py-4">
                    <h4 class="text-muted mb-2">Đang Xuất Bản</h4>
                    <h1 class="text-info">
                        <?php 
                        $published = 0;
                        if (isset($courses)) {
                            foreach ($courses as $c) {
                                if ($c['status'] === 'published') $published++;
                            }
                        }
                        echo $published;
                        ?>
                    </h1>
                    <small class="text-muted">Khóa học hoạt động</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Courses Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-bottom">
            <h4 class="mb-0">Danh Sách Khóa Học</h4>
        </div>
        <?php if (isset($courses) && count($courses) > 0): ?>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Khóa Học</th>
                        <th>Học Viên</th>
                        <th>Bài Giảng</th>
                        <th>Trạng Thái Duyệt</th>
                        <th>Đã Xuất Bản</th>
                        <th>Ngày Tạo</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $enrollment = new Enrollment();
                    $lesson = new Lesson();
                    foreach ($courses as $course): 
                        $course_students = count($enrollment->getCourseEnrollments($course['id']));
                        $course_lessons = count($lesson->getLessonsByCourse($course['id']));
                        
                        // Determine approval status display
                        $approval_status = $course['approval_status'] ?? null;
                        $status_text = 'Nháp';
                        $status_class = 'warning';
                        
                        if ($course['status'] === 'draft') {
                            $status_text = 'Nháp';
                            $status_class = 'warning';
                        } elseif ($approval_status === 'pending') {
                            $status_text = 'Đang chờ duyệt';
                            $status_class = 'info';
                        } elseif ($approval_status === 'approved') {
                            $status_text = 'Đã duyệt';
                            $status_class = 'success';
                        } elseif ($approval_status === 'rejected') {
                            $status_text = 'Từ chối';
                            $status_class = 'danger';
                        }
                    ?>
                    <tr>
                        <td>
                            <div>
                                <h6 class="mb-1">
                                    <a href="/std_mana/instructor/edit/<?php echo $course['id']; ?>" class="text-decoration-none text-dark">
                                        <?php echo htmlspecialchars($course['title']); ?>
                                    </a>
                                </h6>
                                <small class="text-muted"><?php echo htmlspecialchars(substr($course['description'] ?? '', 0, 60)); ?>...</small>
                            </div>
                        </td>
                        <td>
                            <span class="badge badge-pill badge-info"><?php echo $course_students; ?></span>
                        </td>
                        <td>
                            <span class="badge badge-pill badge-secondary"><?php echo $course_lessons; ?></span>
                        </td>
                        <td>
                            <span class="badge badge-<?php echo $status_class; ?>">
                                <?php echo $status_text; ?>
                            </span>
                            <?php if ($approval_status === 'rejected' && !empty($course['notes'])): ?>
                                <br>
                                <small class="text-danger mt-1" title="<?php echo htmlspecialchars($course['notes']); ?>">
                                    <i class="fa fa-exclamation-circle"></i> Lý do: <?php echo htmlspecialchars(substr($course['notes'], 0, 30)); ?>...
                                </small>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php 
                            if ($course['status'] === 'published') {
                                echo '<span class="badge badge-success">Có</span>';
                            } else {
                                echo '<span class="badge badge-secondary">Không</span>';
                            }
                            ?>
                        </td>
                        <td>
                            <small class="text-muted">
                                <?php echo isset($course['created_at']) ? date('d/m/Y', strtotime($course['created_at'])) : 'N/A'; ?>
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
                <i class="fa fa-info-circle"></i> Bạn chưa tạo khóa học nào. 
                <a href="/std_mana/instructor/create" class="alert-link">Tạo khóa học mới</a>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <!-- Rejected Courses Section -->
    <?php 
    if (isset($courses) && count($courses) > 0) {
        $rejected_courses = array_filter($courses, function($c) {
            return ($c['approval_status'] ?? null) === 'rejected';
        });
        
        if (count($rejected_courses) > 0): 
    ?>
    <div class="card border-0 shadow-sm mt-4">
        <div class="card-header bg-danger text-white border-bottom">
            <h4 class="mb-0"><i class="fa fa-times-circle"></i> Khóa Học Bị Từ Chối</h4>
        </div>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Khóa Học</th>
                        <th>Lý Do Từ Chối</th>
                        <th>Ngày Từ Chối</th>
                        <th>Hành Động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $lesson = new Lesson();
                    foreach ($rejected_courses as $course): 
                        $course_lessons = count($lesson->getLessonsByCourse($course['id']));
                    ?>
                    <tr>
                        <td>
                            <div>
                                <h6 class="mb-1">
                                    <a href="/std_mana/instructor/edit/<?php echo $course['id']; ?>" class="text-decoration-none text-dark">
                                        <?php echo htmlspecialchars($course['title']); ?>
                                    </a>
                                </h6>
                                <small class="text-muted"><?php echo $course_lessons; ?> bài giảng</small>
                            </div>
                        </td>
                        <td>
                            <small class="text-danger">
                                <?php echo !empty($course['notes']) ? htmlspecialchars($course['notes']) : 'Không có ghi chú'; ?>
                            </small>
                        </td>
                        <td>
                            <small class="text-muted">
                                <?php echo isset($course['updated_at']) ? date('d/m/Y H:i', strtotime($course['updated_at'])) : 'N/A'; ?>
                            </small>
                        </td>
                        <td>
                            <a href="/std_mana/instructor/edit/<?php echo $course['id']; ?>" class="btn btn-sm btn-primary">
                                <i class="fa fa-edit"></i> Sửa
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php endif; } ?>
</div>

<?php include VIEWS_PATH . '/layouts/footer.php'; ?>
