 
<!DOCTYPE html>
<html lang="en" style="height: auto;">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Quản lý Thu Chi | DMS</title>
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
    <!-- Custom CSS -->
    <link rel="stylesheet" href="../dist/css/custom.css">
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="../plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css">
    <!-- SheetJS for Excel import -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    
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
    </style>
</head>
<body class="sidebar-mini layout-fixed control-sidebar-slide-open layout-navbar-fixed text-sm">
    <div class="wrapper">
        <!-- Navbar -->
        <nav class="main-header navbar navbar-expand navbar-light shadow text-sm">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
                </li>
                <li class="nav-item d-none d-sm-inline-block">
                    <a href="#" class="nav-link">School Dormitory Management System - Admin</a>
                </li>
            </ul>
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <div class="btn-group nav-link">
                        <button type="button" class="btn btn-rounded badge badge-light dropdown-toggle dropdown-icon" data-toggle="dropdown">
                            <span><img src="../uploads/avatars/1.png" class="img-circle elevation-2 user-img" alt="User Image"></span>
                            <span class="ml-3" id="user-display">Admin User</span>
                        </button>
                        <div class="dropdown-menu" role="menu">
                            <a class="dropdown-item" href="#"><span class="fa fa-user"></span> My Account</a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="#" onclick="handleLogout()"><span class="fas fa-sign-out-alt"></span> Logout</a>
                        </div>
                    </div>
                </li>
            </ul>
        </nav>

        <!-- Main Sidebar -->
        <?php include_once 'includes/include.php'; ?>
        <!-- <aside class="main-sidebar sidebar-light-maroon elevation-4 sidebar-no-expand">
            <a href="dashboard.html" class="brand-link bg-gradient-maroon text-sm text-light">
                <img src="../uploads/logo.png" alt="Logo" class="brand-image img-circle elevation-3" style="opacity: .8;width: 1.5rem;height: 1.5rem;max-height: unset">
                <span class="brand-text font-weight-light">DMS - PHP</span>
            </a>
            
            <div class="sidebar">
                <nav class="mt-1">
                    <ul class="nav nav-pills nav-sidebar flex-column text-sm nav-compact nav-flat" data-widget="treeview" role="menu">
                        <li class="nav-item">
                            <a href="dashboard.html" class="nav-link">
                                <i class="nav-icon fas fa-tachometer-alt"></i>
                                <p>Dashboard</p>
                            </a>
                        </li>
                        <li class="nav-header">Main</li>
                        <li class="nav-item">
                            <a href="students.html" class="nav-link">
                                <i class="nav-icon fas fa-users"></i>
                                <p>Student List</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="accounts.html" class="nav-link">
                                <i class="nav-icon fas fa-file"></i>
                                <p>Accounts</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="payment.php" class="nav-link active bg-gradient-maroon">
                                <i class="nav-icon fas fa-money-bill"></i>
                                <p>Quản lý Thu Chi</p>
                            </a>
                        </li>
                        <li class="nav-header">Reports</li>
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="nav-icon far fa-circle"></i>
                                <p>Monthly Collection Report</p>
                            </a>
                        </li>
                        <li class="nav-header">Master List</li>
                        <li class="nav-item">
                            <a href="dorms.html" class="nav-link">
                                <i class="nav-icon fas fa-building"></i>
                                <p>Dorm List</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="rooms.html" class="nav-link">
                                <i class="nav-icon fas fa-door-closed"></i>
                                <p>List of Rooms</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="rooms-management.html" class="nav-link">
                                <i class="nav-icon fas fa-door-open"></i>
                                <p>Room Management</p>
                            </a>
                        </li>
                        <li class="nav-header">Maintenance</li>
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="nav-icon fas fa-users-cog"></i>
                                <p>User List</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="nav-icon fas fa-tools"></i>
                                <p>Settings</p>
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
        </aside> -->

        <!-- Content Wrapper -->
        <div class="content-wrapper pt-3">
            <section class="content text-dark">
                <div class="container-fluid">
                    <!-- Nav tabs -->
                    <ul class="nav nav-tabs" id="paymentTabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="payments-tab" data-toggle="tab" href="#paymentsTab" role="tab">
                                <i class="fas fa-money-bill"></i> Thanh toán
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="utility-tab" data-toggle="tab" href="#utilityTab" role="tab">
                                <i class="fas fa-bolt"></i> Hóa đơn Điện/Nước
                            </a>
                        </li>
                    </ul>

                    <!-- Tab panes -->
                    <div class="tab-content" id="paymentTabContent">
                        <!-- Tab Thanh toán -->
                        <div class="tab-pane fade show active" id="paymentsTab" role="tabpanel">
                            <div class="card card-outline rounded-0 card-maroon">
                                <div class="card-header">
                                    <h3 class="card-title">Danh sách Thu Chi</h3>
                                    <div class="card-tools">
                                        <button class="btn btn-flat btn-success mr-2" onclick="generateMonthlyInvoices()">
                                            <span class="fas fa-calendar-plus"></span> Tạo hóa đơn tháng
                                        </button>
                                        <button class="btn btn-flat btn-info mr-2" onclick="viewStatistics()">
                                            <span class="fas fa-chart-bar"></span> Báo cáo
                                        </button>
                                        <button class="btn btn-flat btn-primary" onclick="addPayment()">
                                            <span class="fas fa-plus"></span> Thêm thanh toán
                                        </button>
                                    </div>
                                </div>
                                <div class="card-body">
                            <div class="container-fluid">
                                <!-- Bộ lọc -->
                                <div class="row mb-3">
                                    <div class="col-md-3">
                                        <select class="form-control" id="filterType" onchange="loadPayments()">
                                            <option value="">-- Tất cả loại --</option>
                                            <option value="Phòng">Phòng</option>
                                            <option value="Điện">Điện</option>
                                            <option value="Nước">Nước</option>
                                            <option value="Khác">Khác</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <input type="date" class="form-control" id="filterFromDate" onchange="loadPayments()" placeholder="Từ ngày">
                                    </div>
                                    <div class="col-md-3">
                                        <input type="date" class="form-control" id="filterToDate" onchange="loadPayments()" placeholder="Đến ngày">
                                    </div>
                                    <div class="col-md-3">
                                        <input type="text" class="form-control" id="filterSearch" onkeyup="loadPayments()" placeholder="Tìm kiếm...">
                                    </div>
                                </div>
                                <table class="table table-hover table-striped table-bordered" id="paymentsTable">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Ngày thanh toán</th>
                                            <th>Sinh viên</th>
                                            <th>Loại</th>
                                            <th>Số tiền</th>
                                            <th>Phương thức</th>
                                            <th>Mô tả</th>
                                            <th>Hành động</th>
                                        </tr>
                                    </thead>
                                    <tbody id="paymentTableBody">
                                        <tr>
                                            <td colspan="8" class="text-center">Đang tải dữ liệu...</td>
                                        </tr>
                                    </tbody>
                                </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tab Hóa đơn Điện/Nước -->
                    <div class="tab-pane fade" id="utilityTab" role="tabpanel">
                        <div class="card card-outline rounded-0 card-maroon">
                            <div class="card-header">
                                <h3 class="card-title">Nhập Hóa đơn Điện/Nước theo Phòng</h3>
                                <div class="card-tools">
                                    <button class="btn btn-flat btn-warning mr-2" onclick="downloadExcelTemplate()">
                                        <span class="fas fa-download"></span> Tải mẫu Excel
                                    </button>
                                    <button class="btn btn-flat btn-info mr-2" data-toggle="modal" data-target="#importExcelModal">
                                        <span class="fas fa-file-excel"></span> Import Excel
                                    </button>
                                    <button class="btn btn-flat btn-success" onclick="saveAllUtilityInvoices()">
                                        <span class="fas fa-save"></span> Lưu tất cả
                                    </button>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="container-fluid">
                                    <!-- Chọn tháng/năm -->
                                    <div class="row mb-3 align-items-center">
                                        <div class="col-md-2">
                                            <label class="font-weight-bold mb-0">Tháng:</label>
                                            <select class="form-control" id="utilityMonth" onchange="loadRoomsForUtility()">
                                                <option value="1">Tháng 1</option>
                                                <option value="2">Tháng 2</option>
                                                <option value="3">Tháng 3</option>
                                                <option value="4">Tháng 4</option>
                                                <option value="5">Tháng 5</option>
                                                <option value="6">Tháng 6</option>
                                                <option value="7">Tháng 7</option>
                                                <option value="8">Tháng 8</option>
                                                <option value="9">Tháng 9</option>
                                                <option value="10">Tháng 10</option>
                                                <option value="11">Tháng 11</option>
                                                <option value="12">Tháng 12</option>
                                            </select>
                                        </div>
                                        <div class="col-md-2">
                                            <label class="font-weight-bold mb-0">Năm:</label>
                                            <select class="form-control" id="utilityYear" onchange="loadRoomsForUtility()">
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="font-weight-bold mb-0">&nbsp;</label>
                                            <div class="alert alert-info mb-0 py-2">
                                                <i class="fas fa-info-circle"></i> Nhập số điện/nước cho từng phòng rồi nhấn "Lưu tất cả"
                                            </div>
                                        </div>
                                    </div>
                                    <table class="table table-hover table-bordered" id="utilityRoomsTable">
                                        <thead class="bg-light">
                                            <tr>
                                                <th width="5%">#</th>
                                                <th width="20%">Phòng</th>
                                                <th width="20%">Điện tiêu thụ (kWh)</th>
                                                <th width="20%">Nước tiêu thụ (m³)</th>
                                                <th width="20%">Tổng tiền (VNĐ)</th>
                                                <th width="15%">Trạng thái</th>
                                            </tr>
                                        </thead>
                                        <tbody id="utilityRoomsTableBody">
                                            <tr>
                                                <td colspan="6" class="text-center">Đang tải danh sách phòng...</td>
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
        
        <!-- Modals -->
        <div class="modal fade" id="uni_modal" role='dialog'>
            <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                <div class="modal-content rounded-0">
                    <div class="modal-header">
                        <h5 class="modal-title"></h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body"></div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary rounded-0" id='submit'>Lưu</button>
                        <button type="button" class="btn btn-secondary rounded-0" data-dismiss="modal">Hủy</button>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="modal fade" id="view_modal" role='dialog'>
            <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                <div class="modal-content rounded-0">
                    <div class="modal-header">
                        <h5 class="modal-title"></h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body"></div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary rounded-0" data-dismiss="modal">Đóng</button>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="modal fade" id="confirm_modal" role='dialog'>
            <div class="modal-dialog modal-md modal-dialog-centered" role="document">
                <div class="modal-content rounded-0">
                    <div class="modal-header">
                        <h5 class="modal-title">Confirmation</h5>
                    </div>
                    <div class="modal-body">
                        <div id="delete_content"></div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary rounded-0" id='confirm' onclick="">Continue</button>
                        <button type="button" class="btn btn-secondary rounded-0" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Import Excel -->
        <div class="modal fade" id="importExcelModal" role='dialog'>
            <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                <div class="modal-content rounded-0">
                    <div class="modal-header bg-warning">
                        <h5 class="modal-title"><i class="fas fa-file-excel"></i> Nhập Hóa Đơn Từ File Excel</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-info">
                            <h6><i class="icon fas fa-info-circle"></i> Hướng dẫn:</h6>
                            <ol class="mb-0">
                                <li>Tải file mẫu Excel bằng nút "Tải mẫu Excel"</li>
                                <li>Điền thông tin chỉ số điện/nước vào file mẫu</li>
                                <li>Upload file đã điền vào hệ thống</li>
                                <li>Hệ thống sẽ tự động tính toán và tạo hóa đơn</li>
                            </ol>
                        </div>
                        <form id="importExcelForm" enctype="multipart/form-data">
                            <div class="form-group">
                                <label>Chọn file Excel <span class="text-danger">*</span></label>
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" id="excelFile" name="excel_file" accept=".xls,.xlsx" required>
                                    <label class="custom-file-label" for="excelFile">Chọn file...</label>
                                </div>
                                <small class="form-text text-muted">Chỉ chấp nhận file .xls hoặc .xlsx (tối đa 5MB)</small>
                            </div>
                            <div id="importProgress" class="progress" style="display: none;">
                                <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 0%"></div>
                            </div>
                            <div id="importResult" style="display: none;"></div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-warning" onclick="handleImportExcel()">
                            <i class="fas fa-upload"></i> Upload & Import
                        </button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Footer -->
        <footer class="main-footer text-sm">
            <strong>Copyright © <span id="current-year"></span>.</strong>
            All rights reserved.
            <div class="float-right d-none d-sm-inline-block">
                <b>DMS - PHP (by: <a href="mailto:oretnom23@gmail.com" target="blank">oretnom23</a>)</b> v1.0
            </div>
        </footer>
    </div>
    <!-- ./wrapper -->

    <!-- jQuery -->
    <script src="../plugins/jquery/jquery.min.js"></script>
    <!-- jQuery UI -->
    <script src="../plugins/jquery-ui/jquery-ui.min.js"></script>
    <!-- SweetAlert2 -->
    <script src="../plugins/sweetalert2/sweetalert2.min.js"></script>
    <script>
        $.widget.bridge('uibutton', $.ui.button);
        var _base_url_ = '../';
        document.getElementById('current-year').textContent = new Date().getFullYear();
    </script>
    <!-- Bootstrap 4 -->
    <script src="../plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <!-- DataTables -->
    <script src="../plugins/datatables/jquery.dataTables.min.js"></script>
    <script src="../plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
    <script src="../plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
    <script src="../plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
    <!-- Select2 -->
    <script src="../plugins/select2/js/select2.full.min.js"></script>
    <!-- Summernote -->
    <script src="../plugins/summernote/summernote-bs4.min.js"></script>
    <!-- AdminLTE App -->
    <script src="../dist/js/adminlte.js"></script>
    
    <script>
        // Kiểm tra đăng nhập
        if (localStorage.getItem('loggedIn') !== 'true') window.location.replace('index.php');
        
        // Hiển thị username
        const username = localStorage.getItem('username') || 'Admin';
        document.getElementById('user-display').textContent = username;
        
        function handleLogout() {
            localStorage.removeItem('loggedIn');
            localStorage.removeItem('username');
            window.location.replace('index.php');
        }
        
        // Preloader Functions
        function start_loader() {
            if ($('#preloader').length === 0) {
                $('body').append('<div id="preloader" style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9999; display: flex; align-items: center; justify-content: center;"><div class="spinner-border text-light" role="status" style="width: 3rem; height: 3rem;"><span class="sr-only">Loading...</span></div></div>');
            }
        }
        
        function end_loader() {
            $('#preloader').fadeOut('fast', function() { $(this).remove(); });
        }
        
        // Toast Notification
        window.alert_toast = function($msg = 'TEST', $bg = 'success', $pos = '') {
            Swal.mixin({
                toast: true, position: $pos || 'top-end', showConfirmButton: false, timer: 3000, timerProgressBar: true
            }).fire({ icon: $bg, title: $msg });
        }
        
        // Universal Modal
        window.uni_modal = function($title = '', $url = '', $size = "") {
            start_loader();
            $('#uni_modal .modal-title').html($title);
            $('#uni_modal .modal-body').html('<div class="alert alert-info"><i class="fa fa-info-circle"></i> Demo: This would load form from: <code>' + $url + '</code></div>');
            if ($size != '') {
                $('#uni_modal .modal-dialog').addClass($size + ' modal-dialog-centered');
            } else {
                $('#uni_modal .modal-dialog').removeAttr("class").addClass("modal-dialog modal-md modal-dialog-centered");
            }
            $('#uni_modal').modal({ show: true, backdrop: 'static', keyboard: false, focus: true });
            end_loader();
        }
        
        // Confirmation Modal
        window._conf = function($msg = '', $func = '', $params = []) {
            $('#confirm_modal #confirm').attr('onclick', $func + "(" + $params.join(',') + ")");
            $('#confirm_modal #delete_content').html($msg);
            $('#confirm_modal').modal('show');
        }
        
        // Biến lưu trữ DataTable
        var paymentTable = null;
        
        // Load danh sách thanh toán
        function loadPayments() {
            start_loader();
            
            const filters = {
                payment_type: $('#filterType').val(),
                from_date: $('#filterFromDate').val(),
                to_date: $('#filterToDate').val(),
                search: $('#filterSearch').val()
            };
            
            const params = new URLSearchParams();
            params.append('action', 'getAll');
            Object.keys(filters).forEach(key => {
                if (filters[key]) params.append(key, filters[key]);
            });
            
            fetch('../../backend/payment.php?' + params.toString())
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        renderPayments(data.data);
                    } else {
                        alert_toast(data.message, 'error');
                    }
                    end_loader();
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert_toast('Lỗi khi tải dữ liệu', 'error');
                    end_loader();
                });
        }
        
        // Render bảng thanh toán
        function renderPayments(payments) {
            const tbody = $('#paymentTableBody');
            tbody.empty();
            
            if (payments.length === 0) {
                tbody.html('<tr><td colspan="8" class="text-center">Không có dữ liệu</td></tr>');
                return;
            }
            
            payments.forEach((payment, index) => {
                const paymentTypeClass = {
                    'Phòng': 'badge-primary',
                    'Điện': 'badge-warning',
                    'Nước': 'badge-info',
                    'Khác': 'badge-secondary'
                }[payment.payment_type] || 'badge-secondary';
                
                const row = `
                    <tr>
                        <td class="text-center">${index + 1}</td>
                        <td>${formatDateTime(payment.payment_date)}</td>
                        <td>
                            <div style="line-height:1em">
                                <div>${payment.student_name}</div>
                                <div class="text-muted small">${payment.student_code || 'N/A'}</div>
                            </div>
                        </td>
                        <td class="text-center">
                            <span class="badge ${paymentTypeClass}">${payment.payment_type}</span>
                        </td>
                        <td class="text-right">${formatMoney(payment.amount)}</td>
                        <td>${payment.payment_method}</td>
                        <td>${payment.description || ''}</td>
                        <td align="center">
                            <div class="btn-group">
                                <button type="button" class="btn btn-flat p-1 btn-default btn-sm dropdown-toggle dropdown-icon" data-toggle="dropdown">
                                    Hành động
                                </button>
                                <div class="dropdown-menu" role="menu">
                                    <a class="dropdown-item" href="#" onclick="viewPayment(${payment.payment_id})">
                                        <span class="fa fa-eye text-dark"></span> Xem
                                    </a>
                                </div>
                            </div>
                        </td>
                    </tr>
                `;
                tbody.append(row);
            });
            
            // Initialize/Reinitialize DataTable
            if (paymentTable) {
                paymentTable.destroy();
            }
            paymentTable = $('#paymentsTable').DataTable({
                columnDefs: [{ orderable: false, targets: [7] }],
                order: [[1, 'desc']],
                pageLength: 25
            });
            $('.dataTable td, .dataTable th').addClass('py-1 px-2 align-middle');
        }
        
        // Format DateTime
        function formatDateTime(dateStr) {
            if (!dateStr) return '';
            const date = new Date(dateStr);
            return date.toLocaleString('vi-VN');
        }
        
        // Format Money
        function formatMoney(amount) {
            return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(amount);
        }
        
        // Thêm thanh toán
        function addPayment() {
            $('#uni_modal .modal-title').html('<i class="fa fa-plus"></i> Thêm thanh toán');
            $('#uni_modal .modal-body').html(`
                <form id="paymentForm">
                    <div class="form-group">
                        <label>Sinh viên <span class="text-danger">*</span></label>
                        <select class="form-control select2" name="student_id" id="studentSelect" required>
                            <option value="">-- Chọn sinh viên --</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Loại thanh toán <span class="text-danger">*</span></label>
                        <select class="form-control" name="payment_type" required>
                            <option value="">-- Chọn loại --</option>
                            <option value="Phòng">Phòng</option>
                            <option value="Điện">Điện</option>
                            <option value="Nước">Nước</option>
                            <option value="Khác">Khác</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Số tiền <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" name="amount" min="0" step="1000" required>
                    </div>
                    <div class="form-group">
                        <label>Ngày thanh toán <span class="text-danger">*</span></label>
                        <input type="datetime-local" class="form-control" name="payment_date" required>
                    </div>
                    <div class="form-group">
                        <label>Phương thức thanh toán</label>
                        <select class="form-control" name="payment_method">
                            <option value="Chuyển khoản online">Chuyển khoản online</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Mô tả</label>
                        <textarea class="form-control" name="description" rows="3"></textarea>
                    </div>
                </form>
            `);
            
            // Load danh sách sinh viên
            fetch('../../backend/payment.php?action=getActiveStudents')
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        const select = $('#studentSelect');
                        data.data.forEach(student => {
                            select.append(`<option value="${student.student_id}">${student.student_name} (${student.student_code})</option>`);
                        });
                        $('.select2').select2({ theme: 'bootstrap4', width: '100%' });
                    }
                });
            
            // Set default datetime
            $('input[name="payment_date"]').val(new Date().toISOString().slice(0, 16));
            
            $('#uni_modal').modal('show');
            $('#submit').off('click').on('click', function() {
                submitPaymentForm('create');
            });
        }
        
        // Submit form thanh toán
        function submitPaymentForm(action) {
            const form = $('#paymentForm')[0];
            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }
            
            start_loader();
            
            const formData = new FormData(form);
            const data = { action: action };
            formData.forEach((value, key) => {
                data[key] = value;
            });
            
            fetch('../../backend/payment.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(result => {
                if (result.status === 'success') {
                    alert_toast(result.message, 'success');
                    $('#uni_modal').modal('hide');
                    loadPayments();
                } else {
                    alert_toast(result.message, 'error');
                }
                end_loader();
            })
            .catch(error => {
                console.error('Error:', error);
                alert_toast('Lỗi khi lưu dữ liệu', 'error');
                end_loader();
            });
        }
        
        // Xem chi tiết thanh toán
        function viewPayment(id) {
            start_loader();
            fetch('../../backend/payment.php?action=getById&id=' + id)
                .then(response => response.json())
                .then(result => {
                    if (result.status === 'success') {
                        const payment = result.data;
                        const paymentTypeClass = {
                            'Phòng': 'badge-primary',
                            'Điện': 'badge-warning',
                            'Nước': 'badge-info',
                            'Khác': 'badge-secondary'
                        }[payment.payment_type] || 'badge-secondary';
                        
                        $('#view_modal .modal-title').html('<i class="fa fa-eye"></i> Chi tiết thanh toán');
                        $('#view_modal .modal-body').html(`
                            <table class="table table-bordered">
                                <tr>
                                    <th width="30%">Mã thanh toán</th>
                                    <td>#${payment.payment_id}</td>
                                </tr>
                                <tr>
                                    <th>Sinh viên</th>
                                    <td>${payment.student_name} (${payment.student_code})</td>
                                </tr>
                                <tr>
                                    <th>Email</th>
                                    <td>${payment.student_email}</td>
                                </tr>
                                <tr>
                                    <th>Số điện thoại</th>
                                    <td>${payment.student_phone}</td>
                                </tr>
                                <tr>
                                    <th>Phòng</th>
                                    <td>Tòa ${payment.building} - Tầng ${payment.floor}</td>
                                </tr>
                                <tr>
                                    <th>Loại thanh toán</th>
                                    <td><span class="badge ${paymentTypeClass}">${payment.payment_type}</span></td>
                                </tr>
                                <tr>
                                    <th>Số tiền</th>
                                    <td class="text-danger font-weight-bold">${formatMoney(payment.amount)}</td>
                                </tr>
                                <tr>
                                    <th>Ngày thanh toán</th>
                                    <td>${formatDateTime(payment.payment_date)}</td>
                                </tr>
                                <tr>
                                    <th>Phương thức</th>
                                    <td>${payment.payment_method}</td>
                                </tr>
                                <tr>
                                    <th>Mô tả</th>
                                    <td>${payment.description || 'Không có'}</td>
                                </tr>
                            </table>
                        `);
                        $('#view_modal').modal('show');
                    } else {
                        alert_toast(result.message, 'error');
                    }
                    end_loader();
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert_toast('Lỗi khi tải dữ liệu', 'error');
                    end_loader();
                });
        }
        
        // Xem báo cáo thống kê
        function viewStatistics() {
            start_loader();
            const year = new Date().getFullYear();
            
            fetch(`../../backend/payment.php?action=getStatistics&year=${year}`)
                .then(response => response.json())
                .then(result => {
                    if (result.status === 'success') {
                        const stats = result.data;
                        const byType = stats.by_type;
                        
                        let typeRows = '';
                        ['Phòng', 'Điện', 'Nước', 'Khác'].forEach(type => {
                            const data = byType[type] || { count: 0, total: 0 };
                            typeRows += `
                                <tr>
                                    <td>${type}</td>
                                    <td class="text-center">${data.count}</td>
                                    <td class="text-right">${formatMoney(data.total)}</td>
                                </tr>
                            `;
                        });
                        
                        $('#view_modal .modal-title').html('<i class="fa fa-chart-bar"></i> Báo cáo thống kê năm ' + year);
                        $('#view_modal .modal-body').html(`
                            <h5>Tổng quan</h5>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <div class="info-box">
                                        <span class="info-box-icon bg-info"><i class="fas fa-list"></i></span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">Tổng số giao dịch</span>
                                            <span class="info-box-number">${stats.total.total_count}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="info-box">
                                        <span class="info-box-icon bg-success"><i class="fas fa-money-bill"></i></span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">Tổng thu</span>
                                            <span class="info-box-number">${formatMoney(stats.total.total_amount)}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <h5>Theo loại thanh toán</h5>
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Loại</th>
                                        <th class="text-center">Số lượng</th>
                                        <th class="text-right">Tổng tiền</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    ${typeRows}
                                </tbody>
                            </table>
                        `);
                        $('#view_modal').modal('show');
                    } else {
                        alert_toast(result.message, 'error');
                    }
                    end_loader();
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert_toast('Lỗi khi tải báo cáo', 'error');
                    end_loader();
                });
        }
        
        // Tạo hóa đơn tháng
        function generateMonthlyInvoices() {
            const now = new Date();
            const currentMonth = now.getMonth() + 1;
            const currentYear = now.getFullYear();
            
            $('#uni_modal .modal-title').html('<i class="fa fa-calendar-plus"></i> Tạo hóa đơn tháng');
            $('#uni_modal .modal-body').html(`
                <form id="invoiceForm">
                    <div class="form-group">
                        <label>Tháng <span class="text-danger">*</span></label>
                        <select class="form-control" name="month" required>
                            ${Array.from({length: 12}, (_, i) => i + 1).map(m => 
                                `<option value="${m}" ${m === currentMonth ? 'selected' : ''}>Tháng ${m}</option>`
                            ).join('')}
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Năm <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" name="year" value="${currentYear}" min="2020" max="2100" required>
                    </div>
                    <div class="form-group">
                        <label>Loại hóa đơn <span class="text-danger">*</span></label>
                        <div>
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="typeRoom" value="Phòng" checked>
                                <label class="custom-control-label" for="typeRoom">Phòng</label>
                            </div>
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="typeElectric" value="Điện">
                                <label class="custom-control-label" for="typeElectric">Điện</label>
                            </div>
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="typeWater" value="Nước">
                                <label class="custom-control-label" for="typeWater">Nước</label>
                            </div>
                        </div>
                    </div>
                    <div class="alert alert-info">
                        <i class="fa fa-info-circle"></i> Hệ thống sẽ tự động tạo hóa đơn cho tất cả sinh viên đang có hợp đồng hiệu lực.
                    </div>
                </form>
            `);
            
            $('#uni_modal').modal('show');
            $('#submit').off('click').on('click', function() {
                const form = $('#invoiceForm')[0];
                if (!form.checkValidity()) {
                    form.reportValidity();
                    return;
                }
                
                const payment_types = [];
                if ($('#typeRoom').is(':checked')) payment_types.push('Phòng');
                if ($('#typeElectric').is(':checked')) payment_types.push('Điện');
                if ($('#typeWater').is(':checked')) payment_types.push('Nước');
                
                if (payment_types.length === 0) {
                    alert_toast('Vui lòng chọn ít nhất một loại hóa đơn', 'warning');
                    return;
                }
                
                start_loader();
                
                const formData = new FormData(form);
                const data = {
                    action: 'generateMonthlyInvoices',
                    month: formData.get('month'),
                    year: formData.get('year'),
                    payment_types: payment_types
                };
                
                fetch('../../backend/payment.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(data)
                })
                .then(response => response.json())
                .then(result => {
                    if (result.status === 'success') {
                        alert_toast(`Tạo thành công ${result.data.success_count} hóa đơn`, 'success');
                        if (result.data.error_count > 0) {
                            console.log('Errors:', result.data.errors);
                        }
                        $('#uni_modal').modal('hide');
                        loadPayments();
                    } else {
                        alert_toast(result.message, 'error');
                    }
                    end_loader();
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert_toast('Lỗi khi tạo hóa đơn', 'error');
                    end_loader();
                });
            });
        }
        
        // ==================== UTILITY INVOICE FUNCTIONS ====================
        
        // Giá điện/nước (lấy từ SystemSettings)
        let electricPrice = 3000;
        let waterPrice = 20000;
        let roomsList = [];
        let existingInvoices = {}; // Lưu hóa đơn đã có theo room_id
        
        // Load giá điện/nước từ SystemSettings
        function loadUtilityPrices() {
            console.log('🔄 Loading utility prices...');
            return fetch('../../backend/controller/UtilityInvoiceController.php?action=get-utility-prices')
                .then(response => {
                    console.log('✅ Prices API response status:', response.status);
                    return response.text();
                })
                .then(text => {
                    console.log('📄 Raw response:', text);
                    try {
                        const result = JSON.parse(text);
                        if (result.status === 'success') {
                            electricPrice = parseFloat(result.data.electric_price) || 3000;
                            waterPrice = parseFloat(result.data.water_price) || 20000;
                            console.log('✅ Loaded prices - Electric:', electricPrice, 'Water:', waterPrice);
                        } else {
                            console.error('❌ API returned error:', result.message);
                        }
                    } catch (e) {
                        console.error('❌ JSON parse error:', e);
                        console.error('Response was:', text);
                    }
                })
                .catch(error => {
                    console.error('❌ Error loading prices:', error);
                    // Giữ giá mặc định nếu lỗi
                    electricPrice = 3000;
                    waterPrice = 20000;
                });
        }
        
        // Populate năm cho dropdown
        function populateUtilityYearFilter() {
            const yearSelect = $('#utilityYear');
            const currentYear = new Date().getFullYear();
            yearSelect.empty();
            for (let i = currentYear; i >= currentYear - 2; i--) {
                yearSelect.append(`<option value="${i}">${i}</option>`);
            }
            yearSelect.val(currentYear);
            
            // Set tháng hiện tại
            $('#utilityMonth').val(new Date().getMonth() + 1);
        }
        
        // Load danh sách phòng đang có người ở và hóa đơn đã có
        function loadRoomsForUtility() {
            const month = $('#utilityMonth').val();
            const year = $('#utilityYear').val();
            
            console.log('🔄 Loading rooms for month:', month, 'year:', year);
            $('#utilityRoomsTableBody').html('<tr><td colspan="6" class="text-center">Đang tải danh sách phòng...</td></tr>');
            
            const roomsUrl = `../../backend/controller/UtilityInvoiceController.php?action=get-occupied-rooms&month=${month}&year=${year}`;
            const invoicesUrl = `../../backend/controller/UtilityInvoiceController.php?action=get-all-invoices&month=${month}&year=${year}`;
            
            console.log('📡 API URLs:', {rooms: roomsUrl, invoices: invoicesUrl});
            
            Promise.all([
                fetch(roomsUrl).then(r => {
                    console.log('✅ Rooms API status:', r.status);
                    return r.text().then(text => {
                        console.log('📄 Rooms raw response:', text);
                        try {
                            return JSON.parse(text);
                        } catch (e) {
                            console.error('❌ Rooms JSON parse error:', e);
                            throw new Error('Invalid JSON from rooms API');
                        }
                    });
                }),
                fetch(invoicesUrl).then(r => {
                    console.log('✅ Invoices API status:', r.status);
                    return r.text().then(text => {
                        console.log('📄 Invoices raw response:', text);
                        try {
                            return JSON.parse(text);
                        } catch (e) {
                            console.error('❌ Invoices JSON parse error:', e);
                            return {status: 'error', message: 'Invalid JSON'};
                        }
                    });
                })
            ])
            .then(([roomsResult, invoicesResult]) => {
                console.log('📊 Rooms result:', roomsResult);
                console.log('📊 Invoices result:', invoicesResult);
                
                if (roomsResult.status === 'success') {
                    roomsList = roomsResult.data || [];
                    console.log('✅ Found', roomsList.length, 'occupied rooms');
                    
                    existingInvoices = {};
                    if (invoicesResult.status === 'success' && invoicesResult.data) {
                        invoicesResult.data.forEach(inv => {
                            existingInvoices[inv.room_id] = inv;
                        });
                        console.log('✅ Found', Object.keys(existingInvoices).length, 'existing invoices');
                    }
                    
                    displayRoomsForUtility(roomsList, month, year);
                } else {
                    console.error('❌ Room load error:', roomsResult);
                    $('#utilityRoomsTableBody').html('<tr><td colspan="6" class="text-center text-danger">' + (roomsResult.message || 'Lỗi tải danh sách phòng') + '</td></tr>');
                }
            })
            .catch(error => {
                console.error('❌ Fatal error:', error);
                $('#utilityRoomsTableBody').html('<tr><td colspan="6" class="text-center text-danger">Lỗi tải dữ liệu: ' + error.message + '</td></tr>');
            });
        }
        
        // Hiển thị bảng phòng để nhập điện/nước
        function displayRoomsForUtility(rooms, month, year) {
            const tbody = $('#utilityRoomsTableBody');
            tbody.empty();
            
            if (!rooms || rooms.length === 0) {
                tbody.append('<tr><td colspan="6" class="text-center text-muted">Không có phòng nào đang có người ở trong tháng này</td></tr>');
                return;
            }
            
            rooms.forEach((room, index) => {
                const existingInv = existingInvoices[room.room_id];
                const isPaid = existingInv && existingInv.status === 'Đã thanh toán';
                const electricVal = existingInv ? existingInv.electric_usage : 0;
                const waterVal = existingInv ? existingInv.water_usage : 0;
                const totalAmount = (electricVal * electricPrice) + (waterVal * waterPrice);
                const statusBadge = isPaid 
                    ? '<span class="badge badge-success">Đã thanh toán</span>' 
                    : (existingInv ? '<span class="badge badge-warning">Chờ thanh toán</span>' : '<span class="badge badge-secondary">Chưa nhập</span>');

                const activeCount = room.active_registrations || 0;
                const roomName = room.room_name ? room.room_name : `Tầng ${room.floor}`;
                const occupancyInfo = `<div class="d-flex justify-content-between align-items-center">
                    <span class="badge badge-pill badge-light border"><i class="fas fa-user-friends mr-1"></i>${activeCount} người</span>
                    <span class="text-muted small">${room.capacity - room.available_beds}/${room.capacity} giường sử dụng</span>
                </div>`;
                
                const row = `
                    <tr data-room-id="${room.room_id}" data-invoice-id="${existingInv ? existingInv.invoice_id : ''}">
                        <td class="text-center align-middle">${index + 1}</td>
                        <td>
                            <div class="d-flex flex-column">
                                <span class="font-weight-bold mb-1">Tòa ${room.building} • ${roomName}</span>
                                ${occupancyInfo}
                            </div>
                        </td>
                        <td>
                            <input type="number" step="0.01" min="0" 
                                class="form-control form-control-sm electric-input" 
                                value="${electricVal}" 
                                onchange="calculateRowTotal(this)"
                                ${isPaid ? 'disabled' : ''}>
                        </td>
                        <td>
                            <input type="number" step="0.01" min="0" 
                                class="form-control form-control-sm water-input" 
                                value="${waterVal}"
                                onchange="calculateRowTotal(this)"
                                ${isPaid ? 'disabled' : ''}>
                        </td>
                        <td class="text-right total-amount">
                            <strong>${formatMoney(totalAmount)}</strong>
                        </td>
                        <td class="text-center">${statusBadge}</td>
                    </tr>
                `;
                tbody.append(row);
            });
        }
        
        // Tính tổng tiền cho 1 hàng
        function calculateRowTotal(input) {
            const row = $(input).closest('tr');
            const electric = parseFloat(row.find('.electric-input').val()) || 0;
            const water = parseFloat(row.find('.water-input').val()) || 0;
            const total = (electric * electricPrice) + (water * waterPrice);
            row.find('.total-amount').html(`<strong>${formatMoney(total)}</strong>`);
        }
        
        // Lưu tất cả hóa đơn
        function saveAllUtilityInvoices() {
            const month = $('#utilityMonth').val();
            const year = $('#utilityYear').val();
            const userId = localStorage.getItem('userId') || '1';
            
            const invoices = [];
            
            $('#utilityRoomsTableBody tr').each(function() {
                const row = $(this);
                const roomId = row.data('room-id');
                const invoiceId = row.data('invoice-id');
                
                if (!roomId) return; // Skip header row
                
                const electric = parseFloat(row.find('.electric-input').val()) || 0;
                const water = parseFloat(row.find('.water-input').val()) || 0;
                
                // Chỉ lưu nếu có nhập số điện hoặc nước
                if (electric > 0 || water > 0) {
                    invoices.push({
                        room_id: roomId,
                        invoice_id: invoiceId || null,
                        invoice_month: month,
                        invoice_year: year,
                        electric_usage: electric,
                        water_usage: water,
                        created_by: userId
                    });
                }
            });
            
            if (invoices.length === 0) {
                alert_toast('Chưa có dữ liệu điện/nước nào để lưu', 'warning');
                return;
            }
            
            start_loader();
            
            fetch('../../backend/controller/UtilityInvoiceController.php?action=save-bulk', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ invoices: invoices })
            })
            .then(response => response.json())
            .then(result => {
                if (result.status === 'success') {
                    alert_toast(`Đã lưu ${result.data.success} hóa đơn thành công!`, 'success');
                    loadRoomsForUtility(); // Reload để cập nhật trạng thái
                } else {
                    alert_toast(result.message, 'error');
                }
                end_loader();
            })
            .catch(error => {
                console.error('Error:', error);
                alert_toast('Lỗi khi lưu hóa đơn', 'error');
                end_loader();
            });
        }
        
        // Xóa hóa đơn
        function deleteUtilityInvoice(invoiceId) {
            if (!confirm('Bạn có chắc muốn xóa hóa đơn này?')) return;
            
            start_loader();
            
            fetch('../../backend/controller/UtilityInvoiceController.php?action=delete-invoice', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ invoice_id: invoiceId })
            })
            .then(response => response.json())
            .then(result => {
                if (result.status === 'success') {
                    alert_toast('Xóa hóa đơn thành công', 'success');
                    loadRoomsForUtility();
                } else {
                    alert_toast(result.message, 'error');
                }
                end_loader();
            })
            .catch(error => {
                console.error('Error:', error);
                alert_toast('Lỗi khi xóa hóa đơn', 'error');
                end_loader();
            });
        }
        
        // Placeholder functions cho compatibility
        function loadUtilityInvoices() {
            loadRoomsForUtility();
        }
        
        function loadRoomsList() {
            // Không cần nữa
        }
        
        // Tải mẫu Excel
        function downloadExcelTemplate() {
            const currentMonth = new Date().getMonth() + 1;
            const currentYear = new Date().getFullYear();
            
            // Tạo workbook với SheetJS
            const ws_data = [
                ['Tòa nhà', 'Phòng', 'Tháng', 'Năm', 'Chỉ số điện cũ', 'Chỉ số điện mới', 'Chỉ số nước cũ', 'Chỉ số nước mới', 'Ghi chú'],
                ['A', '101', currentMonth, currentYear, 100, 150, 20, 25, ''],
                ['A', '102', currentMonth, currentYear, 200, 250, 30, 35, ''],
                ['B', '201', currentMonth, currentYear, 150, 200, 25, 30, '']
            ];
            
            const ws = XLSX.utils.aoa_to_sheet(ws_data);
            
            // Đặt độ rộng cột
            ws['!cols'] = [
                { wch: 10 }, // Tòa nhà
                { wch: 10 }, // Phòng
                { wch: 8 },  // Tháng
                { wch: 8 },  // Năm
                { wch: 15 }, // Chỉ số điện cũ
                { wch: 15 }, // Chỉ số điện mới
                { wch: 15 }, // Chỉ số nước cũ
                { wch: 15 }, // Chỉ số nước mới
                { wch: 20 }  // Ghi chú
            ];
            
            const wb = XLSX.utils.book_new();
            XLSX.utils.book_append_sheet(wb, ws, 'Hóa đơn điện nước');
            
            // Download file
            XLSX.writeFile(wb, `Mau_Hoa_Don_Dien_Nuoc_${currentMonth}_${currentYear}.xlsx`);
            
            alert_toast('Đã tải mẫu Excel thành công!', 'success');
        }
        
        // Xử lý Import Excel
        function handleImportExcel() {
            const fileInput = document.getElementById('excelFile');
            const file = fileInput.files[0];
            
            if (!file) {
                alert_toast('Vui lòng chọn file Excel', 'warning');
                return;
            }
            
            // Kiểm tra định dạng file
            const allowedTypes = ['.xls', '.xlsx'];
            const fileExt = '.' + file.name.split('.').pop().toLowerCase();
            if (!allowedTypes.includes(fileExt)) {
                alert_toast('Chỉ chấp nhận file .xls hoặc .xlsx', 'error');
                return;
            }
            
            // Kiểm tra kích thước file (max 5MB)
            if (file.size > 5 * 1024 * 1024) {
                alert_toast('File quá lớn. Tối đa 5MB', 'error');
                return;
            }
            
            // Hiển thị progress
            $('#importProgress').show();
            $('#importProgress .progress-bar').css('width', '30%');
            $('#importResult').hide();
            
            const reader = new FileReader();
            
            reader.onload = function(e) {
                try {
                    $('#importProgress .progress-bar').css('width', '60%');
                    
                    const data = new Uint8Array(e.target.result);
                    const workbook = XLSX.read(data, { type: 'array' });
                    
                    // Đọc sheet đầu tiên
                    const firstSheetName = workbook.SheetNames[0];
                    const worksheet = workbook.Sheets[firstSheetName];
                    
                    // Chuyển đổi sang JSON
                    const jsonData = XLSX.utils.sheet_to_json(worksheet, { header: 1 });
                    
                    if (jsonData.length < 2) {
                        alert_toast('File Excel không có dữ liệu', 'error');
                        $('#importProgress').hide();
                        return;
                    }
                    
                    $('#importProgress .progress-bar').css('width', '80%');
                    
                    // Xử lý dữ liệu (bỏ qua header)
                    const importedData = [];
                    for (let i = 1; i < jsonData.length; i++) {
                        const row = jsonData[i];
                        if (!row || row.length === 0) continue;
                        
                        const building = row[0] || '';        // Tòa nhà
                        const roomNum = row[1] || '';         // Phòng
                        const month = parseInt(row[2]) || $('#utilityMonth').val();  // Tháng
                        const year = parseInt(row[3]) || $('#utilityYear').val();    // Năm
                        const electricOld = parseFloat(row[4]) || 0;   // Chỉ số điện cũ
                        const electricNew = parseFloat(row[5]) || 0;   // Chỉ số điện mới
                        const waterOld = parseFloat(row[6]) || 0;      // Chỉ số nước cũ
                        const waterNew = parseFloat(row[7]) || 0;      // Chỉ số nước mới
                        const notes = row[8] || '';           // Ghi chú
                        
                        // Tính toán tiêu thụ
                        const electricUsage = Math.max(0, electricNew - electricOld);
                        const waterUsage = Math.max(0, waterNew - waterOld);
                        const totalAmount = (electricUsage * electricPrice) + (waterUsage * waterPrice);
                        
                        importedData.push({
                            building: building,
                            roomNum: roomNum,
                            month: month,
                            year: year,
                            electricOld: electricOld,
                            electricNew: electricNew,
                            waterOld: waterOld,
                            waterNew: waterNew,
                            electricUsage: electricUsage,
                            waterUsage: waterUsage,
                            totalAmount: totalAmount,
                            notes: notes
                        });
                    }
                    
                    $('#importProgress .progress-bar').css('width', '100%');
                    
                    if (importedData.length === 0) {
                        alert_toast('Không có dữ liệu hợp lệ trong file', 'warning');
                        $('#importProgress').hide();
                        return;
                    }
                    
                    // Hiển thị kết quả preview
                    displayImportPreview(importedData);
                    
                    setTimeout(() => {
                        $('#importProgress').hide();
                    }, 500);
                    
                } catch (error) {
                    console.error('Error parsing Excel:', error);
                    alert_toast('Lỗi đọc file Excel: ' + error.message, 'error');
                    $('#importProgress').hide();
                }
            };
            
            reader.onerror = function(error) {
                console.error('FileReader error:', error);
                alert_toast('Lỗi đọc file', 'error');
                $('#importProgress').hide();
            };
            
            reader.readAsArrayBuffer(file);
        }
        
        // Hiển thị preview dữ liệu import
        function displayImportPreview(data) {
            let tableHtml = `
                <div class="table-responsive">
                    <table class="table table-bordered table-sm">
                        <thead class="bg-light">
                            <tr>
                                <th>#</th>
                                <th>Tòa nhà</th>
                                <th>Phòng</th>
                                <th>Tháng/Năm</th>
                                <th>Điện tiêu thụ (kWh)</th>
                                <th>Nước tiêu thụ (m³)</th>
                                <th>Tổng tiền</th>
                            </tr>
                        </thead>
                        <tbody>
            `;
            
            let totalElectric = 0;
            let totalWater = 0;
            let totalMoney = 0;
            
            data.forEach((item, index) => {
                totalElectric += item.electricUsage;
                totalWater += item.waterUsage;
                totalMoney += item.totalAmount;
                
                tableHtml += `
                    <tr>
                        <td>${index + 1}</td>
                        <td>${item.building}</td>
                        <td>${item.roomNum}</td>
                        <td>${item.month}/${item.year}</td>
                        <td class="text-right">${item.electricUsage.toFixed(2)}</td>
                        <td class="text-right">${item.waterUsage.toFixed(2)}</td>
                        <td class="text-right">${formatMoney(item.totalAmount)}</td>
                    </tr>
                `;
            });
            
            tableHtml += `
                        </tbody>
                        <tfoot class="bg-light font-weight-bold">
                            <tr>
                                <td colspan="4" class="text-right">Tổng cộng:</td>
                                <td class="text-right">${totalElectric.toFixed(2)} kWh</td>
                                <td class="text-right">${totalWater.toFixed(2)} m³</td>
                                <td class="text-right text-success">${formatMoney(totalMoney)}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <div class="mt-3">
                    <button class="btn btn-primary btn-block" onclick="applyImportedData()">
                        <i class="fas fa-check"></i> Áp dụng dữ liệu vào bảng
                    </button>
                </div>
            `;
            
            $('#importResult').html(tableHtml).show();
            
            // Lưu dữ liệu để áp dụng sau
            window.importedExcelData = data;
        }
        
        // Áp dụng dữ liệu import vào bảng chính
        function applyImportedData() {
            if (!window.importedExcelData || window.importedExcelData.length === 0) {
                alert_toast('Không có dữ liệu để áp dụng', 'warning');
                return;
            }
            
            const data = window.importedExcelData;
            
            // Cập nhật tháng/năm từ dữ liệu import (lấy từ dòng đầu tiên)
            if (data.length > 0) {
                $('#utilityMonth').val(data[0].month);
                $('#utilityYear').val(data[0].year);
            }
            
            // Đóng modal
            $('#importExcelModal').modal('hide');
            
            // Cập nhật bảng với dữ liệu import
            updateTableWithImportedData(data);
            
            alert_toast(`Đã áp dụng ${data.length} dòng dữ liệu thành công!`, 'success');
            
            // Clear dữ liệu tạm
            window.importedExcelData = null;
            $('#excelFile').val('');
            $('.custom-file-label').html('Chọn file...');
            $('#importResult').hide();
        }
        
        // Cập nhật bảng với dữ liệu đã import
        function updateTableWithImportedData(data) {
            const tbody = $('#utilityRoomsTableBody');
            tbody.empty();
            
            data.forEach((item, index) => {
                const totalAmount = item.totalAmount;
                
                const row = `
                    <tr data-room-id="" data-invoice-id="" data-building="${item.building}" data-room-num="${item.roomNum}">
                        <td class="text-center">${index + 1}</td>
                        <td><strong>Tòa ${item.building} - Phòng ${item.roomNum}</strong></td>
                        <td>
                            <input type="number" step="0.01" min="0" 
                                class="form-control form-control-sm electric-input" 
                                value="${item.electricUsage.toFixed(2)}" 
                                onchange="calculateRowTotal(this)">
                        </td>
                        <td>
                            <input type="number" step="0.01" min="0" 
                                class="form-control form-control-sm water-input" 
                                value="${item.waterUsage.toFixed(2)}"
                                onchange="calculateRowTotal(this)">
                        </td>
                        <td class="text-right total-amount">
                            <strong>${formatMoney(totalAmount)}</strong>
                        </td>
                        <td class="text-center"><span class="badge badge-info">Từ Excel</span></td>
                    </tr>
                `;
                tbody.append(row);
            });
        }
        
        // Xem chi tiết hóa đơn
        function viewUtilityInvoice(invoice_id) {
            start_loader();
            
            fetch(`../../backend/controller/UtilityInvoiceController.php?action=get-invoice-by-id&invoice_id=${invoice_id}`)
                .then(response => response.json())
                .then(result => {
                    end_loader();
                    
                    if (result.status === 'success') {
                        const inv = result.data;
                        const statusClass = {
                            'Chờ thanh toán': 'badge-warning',
                            'Đã thanh toán': 'badge-success',
                            'Quá hạn': 'badge-danger'
                        }[inv.status] || 'badge-secondary';
                        
                        $('#view_modal .modal-title').html(`<i class="fas fa-file-invoice"></i> Chi tiết hóa đơn #${inv.invoice_id}`);
                        $('#view_modal .modal-body').html(`
                            <div class="row">
                                <div class="col-md-6">
                                    <h6>Thông tin phòng</h6>
                                    <table class="table table-sm table-borderless">
                                        <tr><td><strong>Phòng:</strong></td><td>${inv.room_info}</td></tr>
                                        <tr><td><strong>Tháng/Năm:</strong></td><td>${inv.invoice_month}/${inv.invoice_year}</td></tr>
                                        <tr><td><strong>Trạng thái:</strong></td><td><span class="badge ${statusClass}">${inv.status}</span></td></tr>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <h6>Thông tin thanh toán</h6>
                                    <table class="table table-sm table-borderless">
                                        <tr><td><strong>Hạn thanh toán:</strong></td><td>${inv.due_date || '-'}</td></tr>
                                        <tr><td><strong>Người thanh toán:</strong></td><td>${inv.paid_by_name || '-'}</td></tr>
                                        <tr><td><strong>Ngày thanh toán:</strong></td><td>${inv.payment_date || '-'}</td></tr>
                                    </table>
                                </div>
                            </div>
                            <hr>
                            <h6>Chi tiết tiền điện</h6>
                            <table class="table table-bordered">
                                <tr>
                                    <td><strong>Chỉ số cũ:</strong></td><td class="text-right">${inv.electric_old_reading} kWh</td>
                                    <td><strong>Chỉ số mới:</strong></td><td class="text-right">${inv.electric_new_reading} kWh</td>
                                </tr>
                                <tr>
                                    <td><strong>Tiêu thụ:</strong></td><td class="text-right">${inv.electric_usage} kWh</td>
                                    <td><strong>Đơn giá:</strong></td><td class="text-right">${formatMoney(inv.electric_price_per_unit)}/kWh</td>
                                </tr>
                                <tr>
                                    <td colspan="3"><strong>Tổng tiền điện:</strong></td>
                                    <td class="text-right"><strong>${formatMoney(inv.electric_amount)}</strong></td>
                                </tr>
                            </table>
                            
                            <h6>Chi tiết tiền nước</h6>
                            <table class="table table-bordered">
                                <tr>
                                    <td><strong>Chỉ số cũ:</strong></td><td class="text-right">${inv.water_old_reading} m³</td>
                                    <td><strong>Chỉ số mới:</strong></td><td class="text-right">${inv.water_new_reading} m³</td>
                                </tr>
                                <tr>
                                    <td><strong>Tiêu thụ:</strong></td><td class="text-right">${inv.water_usage} m³</td>
                                    <td><strong>Đơn giá:</strong></td><td class="text-right">${formatMoney(inv.water_price_per_unit)}/m³</td>
                                </tr>
                                <tr>
                                    <td colspan="3"><strong>Tổng tiền nước:</strong></td>
                                    <td class="text-right"><strong>${formatMoney(inv.water_amount)}</strong></td>
                                </tr>
                            </table>
                            
                            <div class="alert alert-info">
                                <h5 class="mb-0">Tổng cộng: <strong class="float-right">${formatMoney(inv.total_amount)}</strong></h5>
                            </div>
                            
                            ${inv.notes ? `<p><strong>Ghi chú:</strong> ${inv.notes}</p>` : ''}
                        `);
                        $('#view_modal').modal('show');
                    } else {
                        alert_toast(result.message, 'error');
                    }
                })
                .catch(error => {
                    end_loader();
                    console.error('Error:', error);
                    alert_toast('Lỗi khi tải dữ liệu', 'error');
                });
        }
        
        // Xóa hóa đơn
        function deleteUtilityInvoice(invoice_id) {
            if (!confirm('Bạn có chắc muốn xóa hóa đơn này?')) return;
            
            start_loader();
            
            fetch('../../backend/controller/UtilityInvoiceController.php?action=delete-invoice', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ invoice_id: invoice_id })
            })
            .then(response => response.json())
            .then(result => {
                end_loader();
                
                if (result.status === 'success') {
                    alert_toast('Xóa hóa đơn thành công', 'success');
                    loadUtilityInvoices();
                } else {
                    alert_toast(result.message, 'error');
                }
            })
            .catch(error => {
                end_loader();
                console.error('Error:', error);
                alert_toast('Lỗi khi xóa hóa đơn', 'error');
            });
        }
        
        // Update file input label
        $('#excelFile').on('change', function() {
            const fileName = $(this).val().split('\\').pop();
            $(this).next('.custom-file-label').html(fileName);
        });
        
        // Populate year filter
        function populateYearFilter() {
            const currentYear = new Date().getFullYear();
            const yearSelect = $('#filterUtilityYear');
            for (let i = currentYear; i >= currentYear - 5; i--) {
                yearSelect.append(`<option value="${i}">${i}</option>`);
            }
            yearSelect.val(currentYear);
        }
        
        // Load dữ liệu khi trang được tải
        $(document).ready(function() {
            loadPayments();
            populateYearFilter();
            populateUtilityYearFilter(); // Populate năm cho tab điện/nước
            
            // Load giá điện/nước từ SystemSettings
            loadUtilityPrices()
                .finally(() => {
                    // Sau khi có giá, tải danh sách phòng lần đầu để đảm bảo người dùng thấy dữ liệu ngay
                    loadRoomsForUtility();
                });
            
            // Load utility invoices when tab is shown
            $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
                if ($(e.target).attr('href') === '#utilityTab') {
                    loadRoomsForUtility();
                }
            });
        });
    </script>
</body>
</html>

