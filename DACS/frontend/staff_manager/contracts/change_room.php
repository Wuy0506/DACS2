<?php
// Lấy ID hợp đồng từ URL
$contract_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($contract_id <= 0) {
    echo '<div class="alert alert-danger">ID hợp đồng không hợp lệ</div>';
    exit;
}

// Gọi trực tiếp model
require_once __DIR__ . '/../../../backend/model/staff/ContractModel.php';
require_once __DIR__ . '/../../../backend/model/staff/RoomManagementModel.php';

$contractModel = new ContractModel();
$result = $contractModel->getContractById($contract_id);

if ($result['status'] !== 'success') {
    echo '<div class="alert alert-danger">' . htmlspecialchars($result['message']) . '</div>';
    exit;
}

$contract = $result['data'];

// Lấy danh sách phòng trống (phòng có available_beds > 0)
$roomModel = new RoomManagementModel();
$rooms_result = $roomModel->getAllRooms();
$all_rooms = $rooms_result['success'] ? $rooms_result['data'] : [];

// Lọc chỉ lấy phòng còn giường trống
$available_rooms = array_filter($all_rooms, function($room) {
    return isset($room['available_beds']) && $room['available_beds'] > 0;
});
?>

<style>
    .current-room-info {
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

<form id="changeRoomForm">
    <input type="hidden" name="contract_id" value="<?php echo $contract_id; ?>">
    
    <div class="current-room-info">
        <h6><i class="fas fa-info-circle"></i> Thông tin hiện tại</h6>
        <p class="mb-1"><strong>Sinh viên:</strong> <?php echo htmlspecialchars($contract['student_name']); ?></p>
        <p class="mb-1"><strong>Phòng hiện tại:</strong> Tòa <?php echo htmlspecialchars($contract['building']); ?> - Tầng <?php echo $contract['floor']; ?> (<?php echo $contract['room_id']; ?>)</p>
        <p class="mb-0"><strong>Giá thuê hiện tại:</strong> <?php echo number_format($contract['price_per_month'], 0, ',', '.'); ?> VNĐ/tháng</p>
    </div>
    
    <div class="form-group">
        <label for="new_room_id" class="form-label">Chọn phòng mới <span class="text-danger">*</span></label>
        <select class="form-control select2" id="new_room_id" name="new_room_id" required style="width: 100%;">
            <option value="">-- Chọn phòng --</option>
            <?php foreach ($available_rooms as $room): ?>
                <?php if ($room['room_id'] != $contract['room_id']): ?>
                    <option value="<?php echo $room['room_id']; ?>" 
                            data-price="<?php echo $room['price_per_month']; ?>"
                            data-available="<?php echo $room['available_beds']; ?>">
                        Tòa <?php echo htmlspecialchars($room['building']); ?> - Tầng <?php echo $room['floor']; ?> 
                        (<?php echo $room['room_id']; ?>) - 
                        Giá: <?php echo number_format($room['price_per_month'], 0, ',', '.'); ?> VNĐ - 
                        Còn trống: <?php echo $room['available_beds']; ?> giường
                    </option>
                <?php endif; ?>
            <?php endforeach; ?>
        </select>
        <small class="form-text text-muted">Chỉ hiển thị các phòng còn giường trống</small>
    </div>
    
    <div class="form-group">
        <label for="start_date" class="form-label">Ngày bắt đầu <span class="text-danger">*</span></label>
        <input type="date" class="form-control" id="start_date" name="start_date" 
               value="<?php echo date('Y-m-d'); ?>" required>
    </div>
    
    <div class="form-group">
        <label for="end_date" class="form-label">Ngày kết thúc <span class="text-danger">*</span></label>
        <input type="date" class="form-control" id="end_date" name="end_date" 
               value="<?php echo $contract['end_date']; ?>" required>
    </div>
    
    <div class="alert alert-warning">
        <i class="fas fa-exclamation-triangle"></i>
        <strong>Lưu ý:</strong>
        <ul class="mb-0">
            <li>Hợp đồng cũ sẽ bị chấm dứt</li>
            <li>Phòng cũ sẽ được giải phóng</li>
            <li>Hợp đồng mới sẽ được tạo với phòng mới</li>
        </ul>
    </div>
</form>

<script>
$(document).ready(function() {
    // Initialize Select2
    $('.select2').select2({
        theme: 'bootstrap4',
        dropdownParent: $('#uni_modal')
    });
    
    // Validate dates
    $('#start_date, #end_date').on('change', function() {
        var startDate = new Date($('#start_date').val());
        var endDate = new Date($('#end_date').val());
        
        if (endDate <= startDate) {
            alert('Ngày kết thúc phải sau ngày bắt đầu!');
            $('#end_date').val('');
        }
    });
});

// Submit form
$('#uni_modal form').submit(function(e) {
    e.preventDefault();
    
    var formData = {
        action: 'changeRoom',
        contract_id: parseInt($('input[name="contract_id"]').val()),
        new_room_id: parseInt($('#new_room_id').val()),
        start_date: $('#start_date').val(),
        end_date: $('#end_date').val()
    };
    
    if (!formData.new_room_id) {
        alert('Vui lòng chọn phòng mới!');
        return false;
    }
    
    start_loader();
    
    $.ajax({
        url: '../../backend/contract.php',
        method: 'POST',
        data: JSON.stringify(formData),
        contentType: 'application/json',
        dataType: 'json',
        success: function(response) {
            end_loader();
            if (response.status === 'success') {
                $('#uni_modal').modal('hide');
                alert_toast(response.message, 'success');
                loadContracts();
            } else {
                alert_toast('Lỗi: ' + response.message, 'error');
            }
        },
        error: function(xhr, status, error) {
            end_loader();
            alert_toast('Lỗi kết nối: ' + error, 'error');
        }
    });
    
    return false;
});
</script>
