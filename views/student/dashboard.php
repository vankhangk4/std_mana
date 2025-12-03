<?php include VIEWS_PATH . '/layouts/header.php'; ?>

<div class="container mt-5">
    <h2>Dashboard Học Viên</h2>
    <p>Chào mừng <?php echo htmlspecialchars($_SESSION['user_name']); ?>!</p>

    <div class="row mt-4">
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <h5>Khóa Học Đang Học</h5>
                    <h3>--</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h5>Đã Hoàn Thành</h5>
                    <h3>--</h3>
                </div>
            </div>
        </div>
    </div>

    <h3 class="mt-5">Khóa Học Gần Đây</h3>
    <a href="/student/my-courses" class="btn btn-primary">Xem Tất Cả Khóa Học</a>
</div>

<?php include VIEWS_PATH . '/layouts/footer.php'; ?>
