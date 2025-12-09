<?php
include_once __DIR__ . '/../../backend/permission.php';
checkPermission(['manager','staff']);
?>
<!DOCTYPE html>
<html lang="vi" style="height: auto;">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Quản lý Yêu Cầu Sửa Chữa | DMS</title>
    <link rel="icon" href="../uploads/logo.png" />
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="../plugins/fontawesome-free/css/all.min.css">
    <!-- DataTables -->
    <link rel="stylesheet" href="../plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="../plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
    <link rel="stylesheet" href="../plugins/datatables-buttons/css/buttons.bootstrap4.min.css">
    <!-- Select2 -->
    <link rel="stylesheet" href="../plugins/select2/css/select2.min.css">
    <link rel="stylesheet" href="../plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="../dist/css/adminlte.css">
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="../plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css">
    
    <style>
        .user-img {
            position: absolute;
            height: 27px;
            width: 27px;
            object-fit: cover;
            left: -7%;
            top: -12%;
        }
        .btn-rounded {
            border-radius: 50px;
        }
        aside.main-sidebar {
            background-image: url('../uploads/default/portrait1.jpg') !important;
            background-repeat: no-repeat;
            background-size: cover;
            background-position: center center;
        }
        .status-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 500;
        }
        .status-pending {
            background-color: #fff3cd;
            color: #856404;
        }
        .status-in-progress {
            background-color: #cfe2ff;
            color: #084298;
        }
        .status-completed {
            background-color: #d1e7dd;
            color: #0f5132;
        }
        .status-rejected {
            background-color: #f8d7da;
            color: #842029;
        }
        .priority-badge {
            padding: 0.25rem 0.5rem;
            border-radius: 15px;
            font-size: 0.75rem;
        }
        .priority-normal {
            background-color: #e7f3ff;
            color: #0066cc;
        }
        .priority-urgent {
            background-color: #ffe7e7;
            color: #cc0000;
            font-weight: bold;
        }
        .stat-card {
            border-radius: 8px;
            padding: 15px;
            background: white;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .repair-image {
            max-width: 100%;
            max-height: 300px;
            border-radius: 8px;
        }
    </style>
</head>
<body class="sidebar-mini layout-fixed control-sidebar-slide-open layout-navbar-fixed text-sm">
    <div class="wrapper">
        <?php include_once 'includes/include.php'; ?>


        
        <!-- Content Wrapper -->
        <div class="content-wrapper pt-3">
            <section class="content text-dark">
                <div class="container-fluid">
                    <!-- Statistics Cards -->
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <div class="stat-card">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h3 class="mb-0" id="stat-pending">0</h3>
                                        <small class="text-muted">Chờ xử lý</small>
                                    </div>
                                    <i class="fas fa-clock fa-2x text-warning"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="stat-card">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h3 class="mb-0" id="stat-in-progress">0</h3>
                                        <small class="text-muted">Đang sửa</small>
                                    </div>
                                    <i class="fas fa-wrench fa-2x text-info"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="stat-card">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h3 class="mb-0" id="stat-completed">0</h3>
                                        <small class="text-muted">Hoàn thành</small>
                                    </div>
                                    <i class="fas fa-check-circle fa-2x text-success"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="stat-card">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h3 class="mb-0 text-danger" id="stat-urgent">0</h3>
                                        <small class="text-muted">Khẩn cấp chờ xử lý</small>
                                    </div>
                                    <i class="fas fa-exclamation-triangle fa-2x text-danger"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Main Card -->
                    <div class="card card-outline rounded-0 card-maroon">
                        <div class="card-header">
                            <h3 class="card-title">Danh sách Yêu Cầu Sửa Chữa</h3>
                            <div class="card-tools">
                                <button class="btn btn-flat btn-sm btn-info mr-2" onclick="loadRepairRequests()">
                                    <span class="fas fa-sync-alt"></span> Làm mới
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="container-fluid">
                                <!-- Filters -->
                                <div class="row mb-3">
                                    <div class="col-md-3">
                                        <select class="form-control" id="filterStatus" onchange="loadRepairRequests()">
                                            <option value="">-- Tất cả trạng thái --</option>
                                            <option value="Chờ xử lý">Chờ xử lý</option>
                                            <option value="Đang sửa">Đang sửa</option>
                                            <option value="Hoàn thành">Hoàn thành</option>
                                            <option value="Từ chối">Từ chối</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <select class="form-control" id="filterPriority" onchange="loadRepairRequests()">
                                            <option value="">-- Tất cả mức độ --</option>
                                            <option value="Khẩn cấp">Khẩn cấp</option>
                                            <option value="Thường">Thường</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <input type="text" class="form-control" id="filterSearch" onkeyup="filterTable()" placeholder="Tìm kiếm...">
                                    </div>
                                </div>

                                <!-- Table -->
                                <div class="table-responsive">
                                    <table class="table table-hover table-striped table-bordered" id="repairTable">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Phòng</th>
                                                <th>Sinh viên</th>
                                                <th>Mô tả</th>
                                                <th>Mức độ</th>
                                                <th>Trạng thái</th>
                                                <th>Ngày báo cáo</th>
                                                <th>Hành động</th>
                                            </tr>
                                        </thead>
                                        <tbody id="repairTableBody">
                                            <tr>
                                                <td colspan="8" class="text-center">Đang tải dữ liệu...</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
        
        <!-- View Detail Modal -->
        <div class="modal fade" id="detailModal" role='dialog'>
            <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
                <div class="modal-content rounded-0">
                    <div class="modal-header">
                        <h5 class="modal-title">Chi Tiết Yêu Cầu Sửa Chữa</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body" id="detailContent"></div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary rounded-0" data-dismiss="modal">Đóng</button>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Accept Modal (Mở rộng) -->
        <div class="modal fade" id="acceptModal" role='dialog'>
            <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                <div class="modal-content rounded-0">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title"><i class="fas fa-clipboard-check"></i> Tiếp Nhận Yêu Cầu Sửa Chữa</h5>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="acceptRepairId">

                        <!-- Thông tin yêu cầu (header gọn) -->
                        <div class="alert alert-info py-2 mb-3" id="acceptRequestInfo">
                            <div class="d-flex align-items-center">
                                <div class="mr-2"><i class="fas fa-info-circle"></i></div>
                                <div>
                                    <strong>Thông tin yêu cầu</strong>
                                    <div id="acceptRequestDetails" class="mt-1"></div>
                                </div>
                            </div>
                        </div>

                        <form id="acceptForm">
                            <div class="row">
                                <!-- Cột trái -->
                                <div class="col-md-6">
                                    <!-- Mức độ ưu tiên -->
                                    <div class="form-group mb-3">
                                        <label class="mb-1"><i class="fas fa-exclamation-circle"></i> Mức độ ưu tiên <span class="text-danger">*</span></label>
                                        <select class="form-control" id="acceptPriority" required>
                                            <option value="">-- Chọn mức độ --</option>
                                            <option value="Thường">Thường</option>
                                            <option value="Khẩn cấp">Khẩn cấp</option>
                                        </select>
                                        <small class="text-muted">Chọn lại mức độ sau khi đã kiểm tra thực tế.</small>
                                    </div>

                                    <!-- Người/đơn vị thực hiện -->
                                    <div class="form-group mb-3">
                                        <label class="mb-1"><i class="fas fa-user-hard-hat"></i> Người/đơn vị thực hiện</label>
                                        <select class="form-control" id="assignedTo">
                                            <option value="">-- Chọn nhân viên phụ trách --</option>
                                        </select>
                                        <small class="text-muted">Danh sách được lấy từ hệ thống nhân sự.</small>
                                    </div>

                                    <!-- Chi phí ước tính -->
                                    <div class="form-group mb-0">
                                        <label class="mb-1"><i class="fas fa-money-bill-wave"></i> Chi phí ước tính (VNĐ)</label>
                                        <input type="number" class="form-control" id="estimatedCost"
                                            placeholder="Nhập số tiền ước tính" min="0" step="1000">
                                        <small class="text-muted">Có thể để trống nếu chưa có báo giá.</small>
                                    </div>
                                </div>

                                <!-- Cột phải -->
                                <div class="col-md-6">
                                    <!-- Ghi chú tình trạng -->
                                    <div class="form-group mb-3">
                                        <label class="mb-1"><i class="fas fa-sticky-note"></i> Ghi chú tình trạng hư hỏng <span class="text-danger">*</span></label>
                                        <textarea class="form-control" id="staffNotes" rows="4" required
                                            placeholder="Ghi ngắn gọn tình trạng sau khi kiểm tra."></textarea>
                                    </div>

                                    <!-- Báo cáo gửi quản lý -->
                                    <div class="form-group mb-0">
                                        <label class="mb-1"><i class="fas fa-file-alt"></i> Báo cáo gửi Quản lý <span class="text-danger">*</span></label>
                                        <textarea class="form-control" id="repairReport" rows="4" required
                                            placeholder="Tóm tắt: tình trạng, nguyên nhân, phương án, chi phí & thời gian dự kiến."></textarea>
                                        <small class="text-info"><i class="fas fa-info-circle"></i> Nội dung này sẽ được gửi cho Quản lý để phê duyệt.</small>
                                    </div>
                                </div>
                            </div>
                        </form>

                        <div class="alert alert-warning mt-3 mb-0 py-2">
                            <div class="d-flex">
                                <div class="mr-2"><i class="fas fa-info-circle"></i></div>
                                <div>
                                    <strong>Sau khi tiếp nhận:</strong>
                                    <div class="mt-1 small">
                                        - Trạng thái yêu cầu chuyển sang <strong>"Đang sửa"</strong>.<br>
                                        - Báo cáo được gửi lên Quản lý để <strong>chờ phê duyệt</strong>.<br>
                                        - Bạn là người chịu trách nhiệm chính cho yêu cầu này.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary rounded-0" onclick="confirmAccept()">
                            <i class="fas fa-check"></i> Tiếp nhận và gửi báo cáo
                        </button>
                        <button type="button" class="btn btn-secondary rounded-0" data-dismiss="modal">Hủy</button>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Complete Modal -->
        <div class="modal fade" id="completeModal" role='dialog'>
            <div class="modal-dialog modal-md modal-dialog-centered" role="document">
                <div class="modal-content rounded-0">
                    <div class="modal-header">
                        <h5 class="modal-title">Hoàn Thành Sửa Chữa</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="completeRepairId">
                        <div class="form-group">
                            <label>Chi phí thực tế (VNĐ)</label>
                            <input type="number" class="form-control" id="actualCost" placeholder="Nhập chi phí thực tế (không bắt buộc)">
                        </div>
                        <p class="text-success"><i class="fas fa-check-circle"></i> Sau khi xác nhận, trạng thái sẽ chuyển sang "Hoàn thành"</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-success rounded-0" onclick="confirmComplete()">Hoàn thành</button>
                        <button type="button" class="btn btn-secondary rounded-0" data-dismiss="modal">Hủy</button>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Reject Modal -->
        <div class="modal fade" id="rejectModal" role='dialog'>
            <div class="modal-dialog modal-md modal-dialog-centered" role="document">
                <div class="modal-content rounded-0">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title">Từ Chối Yêu Cầu</h5>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="rejectRepairId">
                        <p class="text-danger"><i class="fas fa-exclamation-triangle"></i> Bạn có chắc chắn muốn từ chối yêu cầu này?</p>
                        <p>Hành động này không thể hoàn tác.</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger rounded-0" onclick="confirmReject()">Từ chối</button>
                        <button type="button" class="btn btn-secondary rounded-0" data-dismiss="modal">Hủy</button>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Footer -->
        <footer class="main-footer text-sm">
            <strong>Copyright © <span id="current-year"></span>.</strong>
            All rights reserved.
            <div class="float-right d-none d-sm-inline-block">
                <b>DMS - PHP</b> v1.0
            </div>
        </footer>
    </div>

    <!-- jQuery -->
    <script src="../plugins/jquery/jquery.min.js"></script>
    <!-- jQuery UI -->
    <script src="../plugins/jquery-ui/jquery-ui.min.js"></script>
    <!-- SweetAlert2 -->
    <script src="../plugins/sweetalert2/sweetalert2.min.js"></script>
    <script>
        $.widget.bridge('uibutton', $.ui.button);
        document.getElementById('current-year').textContent = new Date().getFullYear();
    </script>
    
    <!-- Bootstrap 4 -->
    <script src="../plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <!-- DataTables -->
    <script src="../plugins/datatables/jquery.dataTables.min.js"></script>
    <script src="../plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
    <script src="../plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
    <script src="../plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
    <!-- AdminLTE App -->
    <script src="../dist/js/adminlte.js"></script>
    
    <script>
        let allRepairRequests = [];
        let currentStaffId = localStorage.getItem('userId') || sessionStorage.getItem('userId') || 1;
        let staffDirectory = [];

        // Initialize
        $(document).ready(function() {
            // Lấy staff_id từ localStorage nếu có
            const storedUserId = localStorage.getItem('userId') || sessionStorage.getItem('userId');
            if (storedUserId) {
                currentStaffId = parseInt(storedUserId);
            }
            
            loadRepairRequests();
            loadStatistics();
            loadStaffList();
        });

        // Load staff list for assignment dropdown
        async function loadStaffList() {
            const selectEl = $('#assignedTo');
            selectEl.prop('disabled', true);
            selectEl.html('<option value="">Đang tải danh sách nhân viên...</option>');

            try {
                const response = await fetch('../../backend/staff_repair.php?action=get-staff-list');
                const data = await response.json();

                selectEl.empty();
                 
                if (data.success && data.staff && data.staff.length > 0) {
                    staffDirectory = data.staff;
                    data.staff.forEach(staff => {
                        const label = `${staff.full_name}${staff.position ? ' (' + staff.position + ')' : ''}`;
                        selectEl.append(`<option value="${staff.staff_id}" data-name="${staff.full_name}">${label}</option>`);
                    });
                    selectEl.prop('disabled', false);
                } else {
                    selectEl.append('<option value="">Chưa có nhân viên khả dụng</option>');
                }
            } catch (error) {
                console.error('Error loading staff list:', error);
                selectEl.html('<option value="">Không thể tải danh sách nhân viên</option>');
            }
        }

        // Load statistics
        async function loadStatistics() {
            try {
                const response = await fetch('../../backend/staff_repair.php?action=get-statistics');
                const data = await response.json();
                
                if (data.success) {
                    const stats = data.statistics;
                    $('#stat-pending').text(stats.pending);
                    $('#stat-in-progress').text(stats.in_progress);
                    $('#stat-completed').text(stats.completed);
                    $('#stat-urgent').text(stats.urgent_pending);
                }
            } catch (error) {
                console.error('Error loading statistics:', error);
            }
        }

        // Load repair requests
        async function loadRepairRequests() {
            try {
                const status = $('#filterStatus').val();
                const priority = $('#filterPriority').val();
                
                let url = '../../backend/staff_repair.php?action=get-all-requests';
                if (status) url += '&status=' + status;
                
                const response = await fetch(url);
                const data = await response.json();
                
                if (data.success) {
                    allRepairRequests = data.requests;
                    
                    // Filter by priority if needed
                    if (priority) {
                        allRepairRequests = allRepairRequests.filter(r => r.priority === priority);
                    }
                    
                    displayRepairRequests();
                    loadStatistics();
                } else {
                    showAlert('error', data.message || 'Lỗi khi tải dữ liệu');
                }
            } catch (error) {
                console.error('Error loading repair requests:', error);
                showAlert('error', 'Lỗi khi tải danh sách yêu cầu');
            }
        }

        // Display repair requests
        function displayRepairRequests() {
            const tbody = $('#repairTableBody');
            tbody.empty();
            
            if (allRepairRequests.length === 0) {
                tbody.append('<tr><td colspan="8" class="text-center">Không có dữ liệu</td></tr>');
                return;
            }
            
            allRepairRequests.forEach((request, index) => {
                const statusClass = {
                    'Chờ xử lý': 'status-pending',
                    'Đang sửa': 'status-in-progress',
                    'Hoàn thành': 'status-completed',
                    'Từ chối': 'status-rejected'
                }[request.status] || 'status-pending';
                
                const priorityClass = request.priority === 'Khẩn cấp' ? 'priority-urgent' : 'priority-normal';
                
                const dropdownItems = [];
                const addMenuItem = (html) => {
                    if (dropdownItems.length > 0) {
                        dropdownItems.push('<div class="dropdown-divider"></div>');
                    }
                    dropdownItems.push(html);
                };

                addMenuItem(`<a class="dropdown-item" href="javascript:void(0)" onclick="viewDetail(${request.repair_id})">
                    <span class="fas fa-eye text-info mr-2"></span>Xem chi tiết
                </a>`);

                if (request.status === 'Chờ xử lý') {
                    addMenuItem(`<a class="dropdown-item" href="javascript:void(0)" onclick="acceptRequest(${request.repair_id})">
                        <span class="fas fa-check text-success mr-2"></span>Tiếp nhận
                    </a>`);
                    addMenuItem(`<a class="dropdown-item" href="javascript:void(0)" onclick="rejectRequest(${request.repair_id})">
                        <span class="fas fa-times text-danger mr-2"></span>Từ chối
                    </a>`);
                } else if (request.status === 'Đang sửa') {
                    addMenuItem(`<a class="dropdown-item" href="javascript:void(0)" onclick="completeRequest(${request.repair_id})">
                        <span class="fas fa-check-double text-success mr-2"></span>Hoàn thành
                    </a>`);
                }

                const actions = `
                    <div class="btn-group">
                        <button type="button" class="btn btn-flat btn-default btn-sm dropdown-toggle dropdown-icon" data-toggle="dropdown">
                            Hành động
                        </button>
                        <div class="dropdown-menu dropdown-menu-right" role="menu">
                            ${dropdownItems.join('')}
                        </div>
                    </div>`;
                
                tbody.append(`
                    <tr>
                        <td>${index + 1}</td>
                        <td>${request.room_name}</td>
                        <td>
                            <strong>${request.student_name}</strong><br>
                            <small>${request.student_phone}</small>
                        </td>
                        <td>${truncateText(request.description, 50)}</td>
                        <td><span class="priority-badge ${priorityClass}">${request.priority}</span></td>
                        <td><span class="status-badge ${statusClass}">${request.status}</span></td>
                        <td>${formatDateTime(request.report_date)}</td>
                        <td>${actions}</td>
                    </tr>
                `);
            });
        }

        // Filter table
        function filterTable() {
            const searchText = $('#filterSearch').val().toLowerCase();
            $('#repairTableBody tr').filter(function() {
                $(this).toggle($(this).text().toLowerCase().indexOf(searchText) > -1);
            });
        }

        // View detail
        async function viewDetail(repairId) {
            try {
                const response = await fetch(`../../backend/staff_repair.php?action=get-request-details&repair_id=${repairId}`);
                const data = await response.json();
                
                if (data.success) {
                    const request = data.request;
                    const statusClass = {
                        'Chờ xử lý': 'status-pending',
                        'Đang sửa': 'status-in-progress',
                        'Hoàn thành': 'status-completed',
                        'Từ chối': 'status-rejected'
                    }[request.status] || 'status-pending';
                    
                    const priorityClass = request.priority === 'Khẩn cấp' ? 'priority-urgent' : 'priority-normal';
                    
                    const approvalStatusBadge = request.approval_status ? 
                        `<span class="badge badge-${request.approval_status === 'Đã phê duyệt' ? 'success' : request.approval_status === 'Từ chối phê duyệt' ? 'danger' : 'warning'}">${request.approval_status}</span>` : 
                        '<span class="text-muted">Chưa gửi</span>';
                    
                    $('#detailContent').html(`
                        <div class="row">
                            <div class="col-md-6">
                                <table class="table table-borderless table-sm">
                                    <tr>
                                        <th width="40%">Phòng:</th>
                                        <td>${request.room_name}</td>
                                    </tr>
                                    <tr>
                                        <th>Sinh viên:</th>
                                        <td>${request.student_name}</td>
                                    </tr>
                                    <tr>
                                        <th>Điện thoại:</th>
                                        <td>${request.student_phone}</td>
                                    </tr>
                                    <tr>
                                        <th>Email:</th>
                                        <td>${request.student_email}</td>
                                    </tr>
                                    <tr>
                                        <th>Trạng thái:</th>
                                        <td><span class="status-badge ${statusClass}">${request.status}</span></td>
                                    </tr>
                                    <tr>
                                        <th>Mức độ:</th>
                                        <td><span class="priority-badge ${priorityClass}">${request.priority}</span></td>
                                    </tr>
                                    <tr>
                                        <th>Ngày báo cáo:</th>
                                        <td>${formatDateTime(request.report_date)}</td>
                                    </tr>
                                    ${request.received_date ? `
                                    <tr>
                                        <th>Ngày tiếp nhận:</th>
                                        <td>${formatDateTime(request.received_date)}</td>
                                    </tr>
                                    ` : ''}
                                    ${request.staff_name ? `
                                    <tr>
                                        <th>Người xử lý:</th>
                                        <td>${request.staff_name}</td>
                                    </tr>
                                    ` : ''}
                                    ${request.assigned_to ? `
                                    <tr>
                                        <th>Người được chỉ định:</th>
                                        <td>${request.assigned_to}</td>
                                    </tr>
                                    ` : ''}
                                    ${request.estimated_cost ? `
                                    <tr>
                                        <th>Chi phí ước tính:</th>
                                        <td>${formatMoney(request.estimated_cost)} VNĐ</td>
                                    </tr>
                                    ` : ''}
                                    ${request.actual_cost ? `
                                    <tr>
                                        <th>Chi phí thực tế:</th>
                                        <td>${formatMoney(request.actual_cost)} VNĐ</td>
                                    </tr>
                                    ` : ''}
                                    <tr>
                                        <th>Phê duyệt:</th>
                                        <td>${approvalStatusBadge}</td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <h6><strong><i class="fas fa-exclamation-circle"></i> Mô tả sự cố ban đầu:</strong></h6>
                                <p class="border p-2 bg-light">${request.description}</p>
                                
                                ${request.staff_notes ? `
                                <h6><strong><i class="fas fa-sticky-note text-primary"></i> Ghi chú của nhân viên:</strong></h6>
                                <p class="border p-2 bg-info text-white">${request.staff_notes}</p>
                                ` : ''}
                                
                                ${request.image_url ? `
                                <h6><strong><i class="fas fa-image"></i> Hình ảnh:</strong></h6>
                                <img src="../../${request.image_url}" class="repair-image mb-3" alt="Hình ảnh sự cố">
                                ` : ''}
                            </div>
                        </div>
                        
                        ${request.repair_report ? `
                        <div class="row mt-3">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header bg-primary text-white">
                                        <h6 class="mb-0"><i class="fas fa-file-alt"></i> Báo Cáo Sửa Chữa (Gửi Quản Lý)</h6>
                                    </div>
                                    <div class="card-body">
                                        <pre style="white-space: pre-wrap; font-family: inherit;">${request.repair_report}</pre>
                                    </div>
                                </div>
                            </div>
                        </div>
                        ` : ''}
                    `);
                    
                    $('#detailModal').modal('show');
                } else {
                    showAlert('error', data.message || 'Lỗi khi tải chi tiết');
                }
            } catch (error) {
                console.error('Error loading detail:', error);
                showAlert('error', 'Lỗi khi tải chi tiết yêu cầu');
            }
        }

        // Accept request
        async function acceptRequest(repairId) {
            $('#acceptRepairId').val(repairId);
            
            // Reset form
            $('#acceptForm')[0].reset();
            
            // Load request details
            try {
                const response = await fetch(`../../backend/staff_repair.php?action=get-request-details&repair_id=${repairId}`);
                const data = await response.json();
                
                if (data.success) {
                    const request = data.request;
                    
                    // Hiển thị thông tin yêu cầu
                    $('#acceptRequestDetails').html(`
                        <div class="row mt-2">
                            <div class="col-md-6"><strong>Phòng:</strong> ${request.room_name}</div>
                            <div class="col-md-6"><strong>Sinh viên:</strong> ${request.student_name}</div>
                            <div class="col-md-12 mt-2"><strong>Mô tả ban đầu:</strong> ${request.description}</div>
                        </div>
                    `);
                    
                    // Điền sẵn mức độ ưu tiên hiện tại
                    $('#acceptPriority').val(request.priority);
                    
                    $('#acceptModal').modal('show');
                } else {
                    showAlert('error', 'Không thể tải thông tin yêu cầu');
                }
            } catch (error) {
                console.error('Error loading request:', error);
                showAlert('error', 'Lỗi khi tải thông tin yêu cầu');
            }
        }

        // Confirm accept
        async function confirmAccept() {
            // Validate form
            const priority = $('#acceptPriority').val();
            const staffNotes = $('#staffNotes').val().trim();
            const repairReport = $('#repairReport').val().trim();
            
            if (!priority || !staffNotes || !repairReport) {
                showAlert('error', 'Vui lòng điền đầy đủ các trường bắt buộc (*)');
                return;
            }
            
            const repairId = $('#acceptRepairId').val();
            const assignedStaffId = $('#assignedTo').val();
            let assignedTo = null;

            if (assignedStaffId) {
                const selectedOption = $('#assignedTo option:selected');
                assignedTo = selectedOption.data('name') || selectedOption.text();
            }
            const estimatedCost = $('#estimatedCost').val();
            
            // Confirm action
            const result = await Swal.fire({
                title: 'Xác nhận tiếp nhận?',
                html: `
                    <div class="text-left">
                        <p>Sau khi xác nhận:</p>
                        <ul>
                            <li>Yêu cầu chuyển sang trạng thái <strong>"Đang sửa"</strong></li>
                            <li>Báo cáo sẽ gửi lên Quản lý <strong>"Chờ phê duyệt"</strong></li>
                            <li>Bạn chịu trách nhiệm xử lý yêu cầu này</li>
                        </ul>
                    </div>
                `,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Xác nhận tiếp nhận',
                cancelButtonText: 'Hủy',
                confirmButtonColor: '#007bff'
            });
            
            if (!result.isConfirmed) return;
            
            try {
                const response = await fetch('../../backend/staff_repair.php?action=accept-request', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        repair_id: parseInt(repairId),
                        staff_id: currentStaffId,
                        priority: priority,
                        staff_notes: staffNotes,
                        assigned_to: assignedTo || null,
                        estimated_cost: estimatedCost ? parseFloat(estimatedCost) : null,
                        repair_report: repairReport
                    })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    showAlert('success', data.message || 'Đã tiếp nhận yêu cầu sửa chữa và gửi báo cáo lên Quản lý');
                    $('#acceptModal').modal('hide');
                    loadRepairRequests();
                } else {
                    showAlert('error', data.message || 'Lỗi khi tiếp nhận yêu cầu');
                }
            } catch (error) {
                console.error('Error accepting request:', error);
                showAlert('error', 'Lỗi khi tiếp nhận yêu cầu');
            }
        }

        // Complete request
        function completeRequest(repairId) {
            $('#completeRepairId').val(repairId);
            $('#actualCost').val('');
            $('#completeModal').modal('show');
        }

        // Confirm complete
        async function confirmComplete() {
            const repairId = $('#completeRepairId').val();
            const actualCost = $('#actualCost').val();
            
            try {
                const response = await fetch('../../backend/staff_repair.php?action=complete-request', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        repair_id: parseInt(repairId),
                        actual_cost: actualCost ? parseFloat(actualCost) : null
                    })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    showAlert('success', 'Đã hoàn thành sửa chữa');
                    $('#completeModal').modal('hide');
                    loadRepairRequests();
                } else {
                    showAlert('error', data.message || 'Lỗi khi hoàn thành');
                }
            } catch (error) {
                console.error('Error completing request:', error);
                showAlert('error', 'Lỗi khi hoàn thành sửa chữa');
            }
        }

        // Reject request
        function rejectRequest(repairId) {
            $('#rejectRepairId').val(repairId);
            $('#rejectModal').modal('show');
        }

        // Confirm reject
        async function confirmReject() {
            const repairId = $('#rejectRepairId').val();
            
            try {
                const response = await fetch('../../backend/staff_repair.php?action=reject-request', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        repair_id: parseInt(repairId),
                        staff_id: currentStaffId
                    })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    showAlert('success', 'Đã từ chối yêu cầu');
                    $('#rejectModal').modal('hide');
                    loadRepairRequests();
                } else {
                    showAlert('error', data.message || 'Lỗi khi từ chối');
                }
            } catch (error) {
                console.error('Error rejecting request:', error);
                showAlert('error', 'Lỗi khi từ chối yêu cầu');
            }
        }

        // Utility functions
        function formatDateTime(dateString) {
            const date = new Date(dateString);
            return date.toLocaleDateString('vi-VN') + ' ' + date.toLocaleTimeString('vi-VN', {hour: '2-digit', minute: '2-digit'});
        }

        function formatMoney(amount) {
            return new Intl.NumberFormat('vi-VN').format(amount);
        }

        function truncateText(text, maxLength) {
            if (text.length <= maxLength) return text;
            return text.substring(0, maxLength) + '...';
        }

        function showAlert(type, message) {
            Swal.fire({
                icon: type,
                title: type === 'success' ? 'Thành công!' : 'Lỗi!',
                text: message,
                timer: 3000,
                showConfirmButton: false
            });
        }

        function handleLogout() {
            Swal.fire({
                title: 'Đăng xuất?',
                text: 'Bạn có chắc chắn muốn đăng xuất?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Đăng xuất',
                cancelButtonText: 'Hủy'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = '../../backend/auth.php?logout=1';
                }
            });
        }
    </script>
   
</body>
</html>
