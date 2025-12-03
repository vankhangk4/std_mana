<?php include VIEWS_PATH . '/layouts/header.php'; ?>

<div class="container-fluid mt-3">
    <h2>Bảng Điều Khiển Quản Trị</h2>

    <div class="row mt-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h5>Tổng Người Dùng</h5>
                    <h3><?php echo $totalUsers; ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h5>Khóa Học</h5>
                    <h3><?php echo $totalCourses; ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <h5>Giảng Viên</h5>
                    <h3><?php echo $totalInstructors; ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <h5>Học Viên</h5>
                    <h3><?php echo $totalStudents; ?></h3>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include VIEWS_PATH . '/layouts/footer.php'; ?>
