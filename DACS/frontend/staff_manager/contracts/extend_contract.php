<?php
// Lấy ID hợp đồng từ URL
$contract_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($contract_id <= 0) {
    echo '<div class="alert alert-danger">ID hợp đồng không hợp lệ</div>';
    exit;
}

// Gọi trực tiếp model
require_once __DIR__ . '/../../../backend/model/staff/ContractModel.php';
$contractModel = new ContractModel();
$result = $contractModel->getContractById($contract_id);

if ($result['status'] !== 'success') {
    echo '<div class="alert alert-danger">' . htmlspecialchars($result['message']) . '</div>';
    exit;
}

$contract = $result['data'];
?>

<style>
.contract-info {
    background-color: #e9ecef;
    padding: 15px;
    border-radius: 5px;
    margin-bottom: 20px;
}

.form-label {
    font-weight: bold;
    color: #495057;
}
</style>

<form id="extendContractForm">
    <input type="hidden" name="contract_id" value="<?php echo $contract_id; ?>">

    <div class="contract-info">
        <h6><i class="fas fa-info-circle"></i> Thông tin hợp đồng</h6>
        <p class="mb-1"><strong>Mã hợp đồng:</strong> #<?php echo $contract['contract_id']; ?></p>
        <p class="mb-1"><strong>Sinh viên:</strong> <?php echo htmlspecialchars($contract['student_name']); ?></p>
        <p class="mb-1"><strong>Phòng:</strong> Tòa <?php echo htmlspecialchars($contract['building']); ?> - Tầng
            <?php echo $contract['floor']; ?></p>
        <p class="mb-1"><strong>Ngày hết hạn hiện tại:</strong>
            <span
                class="text-danger font-weight-bold"><?php echo date('d/m/Y', strtotime($contract['end_date'])); ?></span>
        </p>
        <p class="mb-0"><strong>Trạng thái:</strong>
            <span class="badge badge-<?php echo $contract['status'] === 'Hiệu lực' ? 'success' : 'warning'; ?>">
                <?php echo $contract['status']; ?>
            </span>
        </p>
    </div>

    <div class="form-group">
        <label for="new_end_date" class="form-label">Ngày hết hạn mới <span class="text-danger">*</span></label>
        <input type="date" class="form-control" id="new_end_date" name="new_end_date"
            min="<?php echo date('Y-m-d', strtotime($contract['end_date'] . ' +1 day')); ?>" required>
        <small class="form-text text-muted">
            Ngày hết hạn mới phải sau ngày hết hạn hiện tại
            (<?php echo date('d/m/Y', strtotime($contract['end_date'])); ?>)
        </small>
    </div>

    <div class="form-group">
        <label for="extend_months" class="form-label">Hoặc gia hạn thêm (tháng)</label>
        <select class="form-control" id="extend_months" name="extend_months">
            <option value="">-- Chọn số tháng --</option>
            <option value="1">1 tháng</option>
            <option value="2">2 tháng</option>
            <option value="3">3 tháng</option>
            <option value="6">6 tháng</option>
            <option value="12">1 năm</option>
        </select>
        <small class="form-text text-muted">Chọn số tháng để tự động tính ngày hết hạn mới</small>
    </div>

    <div id="new_date_preview" class="alert alert-info" style="display: none;">
        <i class="fas fa-calendar-check"></i>
        <strong>Ngày hết hạn mới:</strong> <span id="preview_date"></span>
    </div>

    <div class="alert alert-warning">
        <i class="fas fa-exclamation-triangle"></i>
        <strong>Lưu ý:</strong> Việc gia hạn sẽ chỉ cập nhật ngày hết hạn hợp đồng, KHÔNG tự động tạo hóa đơn thanh
        toán.
    </div>
</form>

<script>
$(document).ready(function() {
    var currentEndDate = new Date('<?php echo $contract['end_date']; ?>');

    // Tự động tính ngày hết hạn mới khi chọn số tháng
    $('#extend_months').on('change', function() {
        var months = parseInt($(this).val());
        if (months > 0) {
            var newDate = new Date(currentEndDate);
            newDate.setMonth(newDate.getMonth() + months);

            var dateStr = newDate.toISOString().split('T')[0];
            $('#new_end_date').val(dateStr);

            // Hiển thị preview ngày
            $('#preview_date').text(formatDate(newDate));
            $('#new_date_preview').slideDown();

        } else {
            $('#new_date_preview').slideUp();
        }
    });

    // Hiển thị preview khi nhập trực tiếp
    $('#new_end_date').on('change', function() {
        var newDate = new Date($(this).val());
        if (newDate > currentEndDate) {
            $('#preview_date').text(formatDate(newDate));
            $('#new_date_preview').slideDown();
            $('#extend_months').val('');

        }
    });

    function formatDate(date) {
        var day = ('0' + date.getDate()).slice(-2);
        var month = ('0' + (date.getMonth() + 1)).slice(-2);
        var year = date.getFullYear();
        return day + '/' + month + '/' + year;
    }

    // Không còn tính/hiển thị hóa đơn khi gia hạn
});

// Submit form
var isSubmitting = false; // Biến chống submit nhiều lần

$('#uni_modal form').submit(function(e) {
    e.preventDefault();

    // Chống submit nhiều lần
    if (isSubmitting) {
        return false;
    }

    var newEndDate = $('#new_end_date').val(); // Format: YYYY-MM-DD
    var currentEndDate =
    '<?php echo date('Y-m-d', strtotime($contract['end_date'])); ?>'; // Đảm bảo format YYYY-MM-DD
    var extendMonths = parseInt($('#extend_months').val()) || 0;

    if (!newEndDate) {
        alert('Vui lòng chọn ngày hết hạn mới!');
        return false;
    }

    // Bỏ qua kiểm tra ngày - backend sẽ validate

    var formData = {
        action: 'extend',
        contract_id: parseInt($('input[name="contract_id"]').val()),
        new_end_date: newEndDate,
        extend_months: extendMonths
    };

    // Đánh dấu đang submit và disable button
    isSubmitting = true;
    $('#uni_modal #submit').prop('disabled', true).text('Đang xử lý...');

    start_loader();

    $.ajax({
        url: '../../backend/contract.php',
        method: 'POST',
        data: JSON.stringify(formData),
        contentType: 'application/json',
        dataType: 'json',
        success: function(response) {
            end_loader();
            isSubmitting = false;
            $('#uni_modal #submit').prop('disabled', false).text('Save');

            if (response.status === 'success') {
                $('#uni_modal').modal('hide');

                // Hiển thị thông báo chi tiết
                var message = response.message;
                if (response.invoice && response.invoice.success) {
                    message += '\n' + response.invoice.message;
                }
                alert_toast(message, 'success');
                loadContracts();
            } else {
                alert_toast('Lỗi: ' + response.message, 'error');
            }
        },
        error: function(xhr, status, error) {
            end_loader();
            isSubmitting = false;
            $('#uni_modal #submit').prop('disabled', false).text('Save');
            alert_toast('Lỗi kết nối: ' + error, 'error');
        }
    });

    return false;
});
</script>