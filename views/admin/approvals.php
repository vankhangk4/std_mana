<?php include VIEWS_PATH . '/layouts/header.php'; ?>

<div class="container mt-5">
    <div class="mb-4">
        <h2>Phê Duyệt Khóa Học</h2>
        <p class="text-muted">Quản lý yêu cầu phê duyệt khóa học từ giảng viên</p>
    </div>

    <!-- Pending Approvals -->
    <div class="mb-5">
        <h4 class="mb-3">
            <i class="fa fa-hourglass-half text-warning"></i> Chờ Phê Duyệt
            <span class="badge badge-warning"><?php echo count($pending_approvals); ?></span>
        </h4>
        
        <?php if (count($pending_approvals) > 0): ?>
            <?php foreach ($pending_approvals as $approval): ?>
            <div class="card mb-3 border-left border-warning">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <h5 class="card-title"><?php echo htmlspecialchars($approval['title']); ?></h5>
                            <p class="card-text text-muted">
                                <strong>Giảng viên:</strong> <?php echo htmlspecialchars($approval['instructor_name']); ?><br>
                                <strong>Mô tả:</strong> <?php echo htmlspecialchars(substr($approval['description'] ?? '', 0, 100)); ?>...
                            </p>
                            <small class="text-muted">
                                Gửi lúc: <?php echo date('d/m/Y H:i', strtotime($approval['submitted_at'])); ?>
                            </small>
                        </div>
                        <div class="col-md-4 text-right">
                            <button type="button" class="btn btn-success btn-sm" data-toggle="modal" 
                                    data-target="#approveModal<?php echo $approval['id']; ?>">
                                <i class="fa fa-check"></i> Phê Duyệt
                            </button>
                            <button type="button" class="btn btn-danger btn-sm" data-toggle="modal" 
                                    data-target="#rejectModal<?php echo $approval['id']; ?>">
                                <i class="fa fa-times"></i> Từ Chối
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Approve Modal -->
            <div class="modal fade" id="approveModal<?php echo $approval['id']; ?>" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header bg-success text-white">
                            <h5 class="modal-title">Phê Duyệt Khóa Học</h5>
                            <button type="button" class="close text-white" data-dismiss="modal">
                                <span>&times;</span>
                            </button>
                        </div>
                        <form method="POST" action="/admin/approve_course/<?php echo $approval['course_id']; ?>" onsubmit="approveFormHandler(event)">
                            <div class="modal-body">
                                <p>Bạn có chắc chắn muốn phê duyệt khóa học này?</p>
                                <div class="form-group">
                                    <label for="notes<?php echo $approval['id']; ?>">Ghi Chú (Tùy Chọn)</label>
                                    <textarea class="form-control" id="notes<?php echo $approval['id']; ?>" 
                                              name="notes" rows="3" placeholder="Nhập ghi chú phê duyệt..."></textarea>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Hủy</button>
                                <button type="submit" class="btn btn-success">Xác Nhận Phê Duyệt</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Reject Modal -->
            <div class="modal fade" id="rejectModal<?php echo $approval['id']; ?>" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header bg-danger text-white">
                            <h5 class="modal-title">Từ Chối Khóa Học</h5>
                            <button type="button" class="close text-white" data-dismiss="modal">
                                <span>&times;</span>
                            </button>
                        </div>
                        <form method="POST" action="/admin/reject_course/<?php echo $approval['course_id']; ?>" onsubmit="rejectFormHandler(event)">
                            <div class="modal-body">
                                <div class="form-group">
                                    <label for="reason<?php echo $approval['id']; ?>">Lý Do Từ Chối <span class="text-danger">*</span></label>
                                    <textarea class="form-control" id="reason<?php echo $approval['id']; ?>" 
                                              name="reason" rows="4" placeholder="Vui lòng nhập lý do từ chối..." required></textarea>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Hủy</button>
                                <button type="submit" class="btn btn-danger">Xác Nhận Từ Chối</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="alert alert-info">
                <i class="fa fa-check-circle"></i> Không có yêu cầu phê duyệt nào đang chờ xử lý.
            </div>
        <?php endif; ?>
    </div>

    <!-- Approved/Rejected Approvals -->
    <div class="mb-5">
        <h4 class="mb-3">
            <i class="fa fa-history text-info"></i> Lịch Sử Phê Duyệt
        </h4>
        
        <?php if (count($approved_approvals) > 0): ?>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Khóa Học</th>
                            <th>Giảng Viên</th>
                            <th>Trạng Thái</th>
                            <th>Phê Duyệt Bởi</th>
                            <th>Ngày</th>
                            <th>Ghi Chú</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($approved_approvals as $approval): ?>
                        <tr>
                            <td>
                                <strong><?php echo htmlspecialchars($approval['title']); ?></strong>
                            </td>
                            <td>
                                <?php echo htmlspecialchars($approval['instructor_name']); ?>
                            </td>
                            <td>
                                <span class="badge badge-<?php echo $approval['status'] === 'approved' ? 'success' : 'danger'; ?>">
                                    <?php echo $approval['status'] === 'approved' ? 'Đã Phê Duyệt' : 'Đã Từ Chối'; ?>
                                </span>
                            </td>
                            <td>
                                <?php echo $approval['reviewer_name'] ? htmlspecialchars($approval['reviewer_name']) : 'N/A'; ?>
                            </td>
                            <td>
                                <small class="text-muted">
                                    <?php echo $approval['reviewed_at'] ? date('d/m/Y H:i', strtotime($approval['reviewed_at'])) : 'N/A'; ?>
                                </small>
                            </td>
                            <td>
                                <small><?php echo htmlspecialchars($approval['notes'] ?? ''); ?></small>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="alert alert-info">
                <i class="fa fa-info-circle"></i> Chưa có lịch sử phê duyệt nào.
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
function approveFormHandler(e) {
    e.preventDefault();
    const form = e.target;
    const courseId = form.action.split('/').pop();
    const notes = form.querySelector('[name="notes"]').value;
    
    fetch(form.action, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: 'notes=' + encodeURIComponent(notes)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Khóa học đã được phê duyệt thành công!');
            location.reload();
        } else {
            alert('Lỗi: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Có lỗi xảy ra khi phê duyệt khóa học');
    });
}

function rejectFormHandler(e) {
    e.preventDefault();
    const form = e.target;
    const courseId = form.action.split('/').pop();
    const reason = form.querySelector('[name="reason"]').value;
    
    if (!reason.trim()) {
        alert('Vui lòng nhập lý do từ chối');
        return;
    }
    
    fetch(form.action, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: 'reason=' + encodeURIComponent(reason)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Khóa học đã được từ chối!');
            location.reload();
        } else {
            alert('Lỗi: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Có lỗi xảy ra khi từ chối khóa học');
    });
}
</script>

<?php include VIEWS_PATH . '/layouts/footer.php'; ?>
