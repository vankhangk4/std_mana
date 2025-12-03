<?php include VIEWS_PATH . '/layouts/header.php'; ?>

<div class="container mt-5">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <h2>Tạo Khóa Học Mới</h2>

            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
            <?php endif; ?>

            <form method="POST" action="/instructor/create">
                <div class="form-group">
                    <label for="title">Tên Khóa Học</label>
                    <input type="text" id="title" name="title" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="description">Mô Tả</label>
                    <textarea id="description" name="description" class="form-control" rows="5" required></textarea>
                </div>

                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="category_id">Danh Mục</label>
                        <select id="category_id" name="category_id" class="form-control">
                            <option value="1">Công Nghệ</option>
                            <option value="2">Kinh Doanh</option>
                            <option value="3">Sáng Tạo</option>
                        </select>
                    </div>

                    <div class="form-group col-md-6">
                        <label for="level">Mức Độ</label>
                        <select id="level" name="level" class="form-control">
                            <option value="Beginner">Beginner</option>
                            <option value="Intermediate">Intermediate</option>
                            <option value="Advanced">Advanced</option>
                        </select>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="price">Giá (VNĐ)</label>
                        <input type="number" id="price" name="price" class="form-control" value="0" step="1000">
                    </div>

                    <div class="form-group col-md-6">
                        <label for="duration_weeks">Thời Lượng (Tuần)</label>
                        <input type="number" id="duration_weeks" name="duration_weeks" class="form-control" value="1" min="1">
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">Tạo Khóa Học</button>
                <a href="/instructor/dashboard" class="btn btn-secondary">Hủy</a>
            </form>
        </div>
    </div>
</div>

<?php include VIEWS_PATH . '/layouts/footer.php'; ?>
