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
    .contract-detail-label {
        font-weight: bold;
        color: #495057;
    }
    .contract-detail-value {
        color: #212529;
    }
    .info-section {
        background-color: #f8f9fa;
        padding: 15px;
        border-radius: 5px;
        margin-bottom: 15px;
    }
    .section-title {
        font-size: 1.1rem;
        font-weight: bold;
        color: #343a40;
        margin-bottom: 10px;
        border-bottom: 2px solid #dee2e6;
        padding-bottom: 5px;
    }
</style>

<div class="container-fluid">
    <div class="row">
        <!-- Thông tin hợp đồng -->
        <div class="col-md-12">
            <div class="info-section">
                <div class="section-title">
                    <i class="fas fa-file-contract"></i> Thông tin Hợp đồng
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <p class="mb-2">
                            <span class="contract-detail-label">Mã hợp đồng:</span>
                            <span class="contract-detail-value">#<?php echo $contract['contract_id']; ?></span>
                        </p>
                        <p class="mb-2">
                            <span class="contract-detail-label">Ngày tạo:</span>
                            <span class="contract-detail-value"><?php echo date('d/m/Y', strtotime($contract['created_date'])); ?></span>
                        </p>
                        <p class="mb-2">
                            <span class="contract-detail-label">Ngày hết hạn:</span>
                            <span class="contract-detail-value"><?php echo date('d/m/Y', strtotime($contract['end_date'])); ?></span>
                        </p>
                    </div>
                    <div class="col-md-6">
                        <p class="mb-2">
                            <span class="contract-detail-label">Trạng thái:</span>
                            <?php
                            $statusClass = '';
                            if ($contract['status'] === 'Hiệu lực') {
                                $statusClass = 'badge-success';
                            } elseif ($contract['status'] === 'Hết hạn') {
                                $statusClass = 'badge-warning';
                            } else {
                                $statusClass = 'badge-danger';
                            }
                            ?>
                            <span class="badge <?php echo $statusClass; ?> px-3"><?php echo $contract['status']; ?></span>
                        </p>
                        <p class="mb-2">
                            <span class="contract-detail-label">Mã đăng ký:</span>
                            <span class="contract-detail-value">#<?php echo $contract['registration_id']; ?></span>
                        </p>
                        <p class="mb-2">
                            <span class="contract-detail-label">Người duyệt:</span>
                            <span class="contract-detail-value"><?php echo $contract['approved_by_name'] ?? 'N/A'; ?></span>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Thông tin sinh viên -->
        <div class="col-md-6">
            <div class="info-section">
                <div class="section-title">
                    <i class="fas fa-user-graduate"></i> Thông tin Sinh viên
                </div>
                <p class="mb-2">
                    <span class="contract-detail-label">Họ tên:</span>
                    <span class="contract-detail-value"><?php echo htmlspecialchars($contract['student_name']); ?></span>
                </p>
                <p class="mb-2">
                    <span class="contract-detail-label">Mã sinh viên:</span>
                    <span class="contract-detail-value"><?php echo htmlspecialchars($contract['student_code'] ?? 'N/A'); ?></span>
                </p>
                <p class="mb-2">
                    <span class="contract-detail-label">Email:</span>
                    <span class="contract-detail-value"><?php echo htmlspecialchars($contract['student_email']); ?></span>
                </p>
                <p class="mb-2">
                    <span class="contract-detail-label">Số điện thoại:</span>
                    <span class="contract-detail-value"><?php echo htmlspecialchars($contract['student_phone']); ?></span>
                </p>
                <p class="mb-2">
                    <span class="contract-detail-label">Giới tính:</span>
                    <span class="contract-detail-value"><?php echo htmlspecialchars($contract['gender']); ?></span>
                </p>
                <p class="mb-2">
                    <span class="contract-detail-label">Ngày sinh:</span>
                    <span class="contract-detail-value"><?php echo date('d/m/Y', strtotime($contract['date_of_birth'])); ?></span>
                </p>
                <p class="mb-2">
                    <span class="contract-detail-label">Khoa:</span>
                    <span class="contract-detail-value"><?php echo htmlspecialchars($contract['faculty']); ?></span>
                </p>
                <p class="mb-2">
                    <span class="contract-detail-label">Chuyên ngành:</span>
                    <span class="contract-detail-value"><?php echo htmlspecialchars($contract['major']); ?></span>
                </p>
                <p class="mb-0">
                    <span class="contract-detail-label">Địa chỉ:</span>
                    <span class="contract-detail-value"><?php echo htmlspecialchars($contract['address']); ?></span>
                </p>
            </div>
        </div>

        <!-- Thông tin phòng -->
        <div class="col-md-6">
            <div class="info-section">
                <div class="section-title">
                    <i class="fas fa-door-open"></i> Thông tin Phòng
                </div>
                <p class="mb-2">
                    <span class="contract-detail-label">Mã phòng:</span>
                    <span class="contract-detail-value"><?php echo $contract['room_id']; ?></span>
                </p>
                <p class="mb-2">
                    <span class="contract-detail-label">Tòa nhà:</span>
                    <span class="contract-detail-value"><?php echo htmlspecialchars($contract['building']); ?></span>
                </p>
                <p class="mb-2">
                    <span class="contract-detail-label">Tầng:</span>
                    <span class="contract-detail-value"><?php echo $contract['floor']; ?></span>
                </p>
                <p class="mb-2">
                    <span class="contract-detail-label">Sức chứa:</span>
                    <span class="contract-detail-value"><?php echo $contract['capacity']; ?> người</span>
                </p>
                <p class="mb-2">
                    <span class="contract-detail-label">Giường trống:</span>
                    <span class="contract-detail-value"><?php echo $contract['available_beds']; ?> giường</span>
                </p>
                <p class="mb-2">
                    <span class="contract-detail-label">Giới hạn giới tính:</span>
                    <span class="contract-detail-value"><?php echo htmlspecialchars($contract['gender_restriction']); ?></span>
                </p>
                <p class="mb-2">
                    <span class="contract-detail-label">Giá thuê/tháng:</span>
                    <span class="contract-detail-value text-danger font-weight-bold">
                        <?php echo number_format($contract['price_per_month'], 0, ',', '.'); ?> VNĐ
                    </span>
                </p>
                <p class="mb-2">
                    <span class="contract-detail-label">Trạng thái phòng:</span>
                    <span class="badge badge-info"><?php echo htmlspecialchars($contract['room_status']); ?></span>
                </p>
                <p class="mb-0">
                    <span class="contract-detail-label">Thời gian đăng ký:</span>
                    <span class="contract-detail-value">
                        <?php echo date('d/m/Y', strtotime($contract['created_date'])); ?> - 
                        <?php echo date('d/m/Y', strtotime($contract['end_date'])); ?>
                    </span>
                </p>
            </div>
        </div>
    </div>
    
    <!-- Thông tin thời hạn -->
    <div class="row">
        <div class="col-md-12">
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i>
                <strong>Thời hạn hợp đồng:</strong>
                <?php
                $start = new DateTime($contract['created_date']);
                $end = new DateTime($contract['end_date']);
                $now = new DateTime();
                $interval = $start->diff($end);
                $remaining = $now->diff($end);
                
                // Tính tổng số tháng và ngày (bao gồm cả năm)
                $totalMonths = ($interval->y * 12) + $interval->m;
                $days = $interval->d;
                
                // Hiển thị thời hạn hợp đồng
                $durationText = '';
                if ($totalMonths > 0) {
                    $durationText .= $totalMonths . ' tháng';
                }
                if ($days > 0) {
                    if ($totalMonths > 0) {
                        $durationText .= ' ';
                    }
                    $durationText .= $days . ' ngày';
                }
                if (empty($durationText)) {
                    $durationText = '0 ngày';
                }
                echo $durationText;
                
                // Hiển thị thời gian còn lại
                if ($contract['status'] === 'Hiệu lực' && $end > $now) {
                    $remainingMonths = ($remaining->y * 12) + $remaining->m;
                    $remainingDays = $remaining->d;
                    
                    $remainingText = '';
                    if ($remainingMonths > 0) {
                        $remainingText .= $remainingMonths . ' tháng';
                    }
                    if ($remainingDays > 0) {
                        if ($remainingMonths > 0) {
                            $remainingText .= ' ';
                        }
                        $remainingText .= $remainingDays . ' ngày';
                    }
                    if (empty($remainingText)) {
                        $remainingText = '0 ngày';
                    }
                    echo ' - <span class="font-weight-bold">Còn lại: ' . $remainingText . '</span>';
                } elseif ($contract['status'] === 'Hiệu lực' && $end <= $now) {
                    echo ' - <span class="text-danger font-weight-bold">Đã hết hạn</span>';
                }
                ?>
            </div>
        </div>
    </div>
    
    <!-- Nút xuất PDF -->
    <div class="row">
        <div class="col-md-12 text-center">
            <a href="contracts/generate_contract_pdf.php?id=<?php echo $contract_id; ?>" 
               target="_blank" 
               class="btn btn-danger btn-lg">
                <i class="fas fa-file-pdf"></i> Xuất hợp đồng PDF
            </a>
            <p class="text-muted mt-2">
                <small>Click để xem và tải xuống hợp đồng dạng PDF</small>
            </p>
        </div>
    </div>
</div>
