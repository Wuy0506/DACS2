<?php
include_once __DIR__ . '/../../backend/permission.php';
checkPermission(['manager','staff']);
?>
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
        <!-- Navbar + Sidebar từ include.php -->
        <?php include_once 'includes/include.php'; ?>
        
        <!-- Content Wrapper -->
        <div class="content-wrapper pt-3">
            <section class="content text-dark">
                <div class="container-fluid">
                    <!-- Nội dung Thanh toán -->
                    <div class="tab-content" id="paymentTabContent">
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
                                            <th>Phòng</th>
                                            <th>Loại</th>
                                            <th>Số tiền</th>
                                            <th>Trạng thái</th>
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
                
                // Xác định trạng thái và badge class
                const status = payment.status || 'Chưa thanh toán';
                const statusClass = {
                    'Chưa thanh toán': 'badge-warning',
                    'Đã thanh toán': 'badge-success',
                    'Quá hạn': 'badge-danger'
                }[status] || 'badge-secondary';
                
                // Nút thanh toán chỉ hiện khi chưa thanh toán
                const payButton = status !== 'Đã thanh toán' 
                    ? `<a class="dropdown-item" href="#" onclick="markAsPaid(${payment.payment_id})">
                           <span class="fa fa-check text-success"></span> Xác nhận thanh toán
                       </a>`
                    : '';
                
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
                        <td>
                            <div style="line-height:1em">
                                <div>${payment.building}.${payment.room_name || 'N/A'}</div>
                            </div>
                        </td>
                        <td class="text-center">
                            <span class="badge ${paymentTypeClass}">${payment.payment_type}</span>
                        </td>
                        <td class="text-right">${formatMoney(payment.amount)}</td>
                        <td class="text-center">
                            <span class="badge ${statusClass}">${status}</span>
                        </td>
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
                                    ${payButton}
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
        
        // Xác nhận thanh toán (chuyển status sang "Đã thanh toán")
        function markAsPaid(paymentId) {
            if (!confirm('Xác nhận đã nhận thanh toán cho hóa đơn này?')) return;
            
            start_loader();
            
            fetch('../../backend/payment.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    action: 'markAsPaid',
                    payment_id: paymentId
                })
            })
            .then(response => response.json())
            .then(result => {
                if (result.status === 'success') {
                    alert_toast(result.message, 'success');
                    loadPayments(); // Reload danh sách
                } else {
                    alert_toast(result.message, 'error');
                }
                end_loader();
            })
            .catch(error => {
                console.error('Error:', error);
                alert_toast('Lỗi khi xác nhận thanh toán', 'error');
                end_loader();
            });
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
                                    <td>Tòa ${payment.building} - Tầng ${payment.floor} - Phòng ${payment.room_name} (${payment.registration_status})</td>
                                </tr>
                                <tr>
                                    <th>Loại thanh toán</th>
                                    <td><span class="badge ${paymentTypeClass}">${payment.payment_type}</span></td>
                                </tr>
                                <tr>
                                    <th>Số tiền</th>
                                    <td class="text-danger font-weight-bold">${formatMoney(payment.amount)} (${payment.status})</td>
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

        // Biến lưu giá điện/nước từ SystemSettings
        let utilityPrices = { electric: 3500, water: 25000 };
        
        // Load giá điện/nước từ hệ thống
        function loadUtilityPrices() {
            return fetch('../../backend/payment.php?action=getUtilityPrices')
                .then(response => response.json())
                .then(result => {
                    if (result.status === 'success') {
                        utilityPrices.electric = parseFloat(result.data.electric_price) || 3500;
                        utilityPrices.water = parseFloat(result.data.water_price) || 25000;
                    }
                    return utilityPrices;
                })
                .catch(() => utilityPrices);
        }

        // Tạo hóa đơn tháng
        function generateMonthlyInvoices() {
            const now = new Date();
            const currentMonth = now.getMonth() + 1;
            const currentYear = now.getFullYear();
            
            $('#uni_modal .modal-title').html('<i class="fa fa-calendar-plus"></i> Tạo hóa đơn tháng');
            $('#uni_modal .modal-body').html(`
                <form id="invoiceForm" onsubmit="return false;">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Tháng <span class="text-danger">*</span></label>
                                <select class="form-control" name="month" required>
                                    ${Array.from({length: 12}, (_, i) => i + 1).map(m => 
                                        `<option value="${m}" ${m === currentMonth ? 'selected' : ''}>Tháng ${m}</option>`
                                    ).join('')}
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Năm <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" name="year" value="${currentYear}" min="2020" max="2100" required>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Loại hóa đơn <span class="text-danger">*</span></label>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="custom-control custom-radio">
                                    <input type="radio" class="custom-control-input" id="typeRoom" name="invoiceType" value="Phòng" checked>
                                    <label class="custom-control-label" for="typeRoom">Tiền phòng</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="custom-control custom-radio">
                                    <input type="radio" class="custom-control-input" id="typeElectric" name="invoiceType" value="Điện">
                                    <label class="custom-control-label" for="typeElectric">Tiền điện</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="custom-control custom-radio">
                                    <input type="radio" class="custom-control-input" id="typeWater" name="invoiceType" value="Nước">
                                    <label class="custom-control-label" for="typeWater">Tiền nước</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="alert alert-info" id="invoiceInfo">
                        <i class="fa fa-info-circle"></i> Hệ thống sẽ tự động tạo hóa đơn tiền phòng cho tất cả sinh viên đang có hợp đồng hiệu lực.
                    </div>
                </form>
            `);
            
            // Xử lý khi thay đổi loại hóa đơn
            $('input[name="invoiceType"]').on('change', function() {
                const type = $(this).val();
                if (type === 'Phòng') {
                    $('#invoiceInfo').html('<i class="fa fa-info-circle"></i> Hệ thống sẽ tự động tạo hóa đơn tiền phòng cho tất cả sinh viên đang có hợp đồng hiệu lực.');
                } else if (type === 'Điện') {
                    $('#invoiceInfo').html(`<i class="fa fa-bolt text-warning"></i> Nhấn <strong>Lưu</strong> để mở bảng nhập số kWh điện cho từng phòng. <br><small>Giá điện: <strong>${formatMoney(utilityPrices.electric)}/kWh</strong></small>`);
                } else if (type === 'Nước') {
                    $('#invoiceInfo').html(`<i class="fa fa-tint text-info"></i> Nhấn <strong>Lưu</strong> để mở bảng nhập số m³ nước cho từng phòng. <br><small>Giá nước: <strong>${formatMoney(utilityPrices.water)}/m³</strong></small>`);
                }
            });
            
            // Load giá điện/nước
            loadUtilityPrices();
            
            $('#uni_modal').modal('show');
            $('#submit').off('click').on('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                const form = $('#invoiceForm')[0];
                if (!form.checkValidity()) {
                    form.reportValidity();
                    return false;
                }
                
                const invoiceType = $('input[name="invoiceType"]:checked').val();
                const month = $('select[name="month"]').val();
                const year = $('input[name="year"]').val();
                
                console.log('Invoice Type:', invoiceType, 'Month:', month, 'Year:', year);
                console.log('Is Electric:', invoiceType === 'Điện', 'Is Water:', invoiceType === 'Nước');
                
                // Kiểm tra cả tiếng Việt có dấu và không dấu
                if (invoiceType === 'Điện' || invoiceType === 'Dien' || invoiceType === 'Nước' || invoiceType === 'Nuoc') {
                    // Mở modal nhập số điện/nước
                    $('#uni_modal').modal('hide');
                    setTimeout(function() {
                        openUtilityInputModal(invoiceType, month, year);
                    }, 300);
                } else {
                    // Tạo hóa đơn tiền phòng như cũ
                    createRoomInvoices(month, year);
                }
                return false;
            });
        }
        
        // Tạo hóa đơn tiền phòng
        function createRoomInvoices(month, year) {
            start_loader();
            
            const data = {
                action: 'generateMonthlyInvoices',
                month: month,
                year: year,
                payment_types: ['Phòng']
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
        }
        
        // Mở modal nhập số điện/nước cho từng phòng
        function openUtilityInputModal(type, month, year) {
            console.log('Opening utility modal:', type, month, year);
            
            const isElectric = type === 'Điện';
            const unit = isElectric ? 'kWh' : 'm³';
            const price = isElectric ? utilityPrices.electric : utilityPrices.water;
            const icon = isElectric ? 'fa-bolt text-warning' : 'fa-tint text-info';
            const title = isElectric ? 'Nhập số điện tiêu thụ' : 'Nhập số nước tiêu thụ';
            
            console.log('Price:', price, 'Unit:', unit);
            
            $('#view_modal .modal-title').html(`<i class="fa ${icon}"></i> ${title} - Tháng ${month}/${year}`);
            $('#view_modal .modal-body').html(`
                <div class="mb-3">
                    <div class="alert alert-secondary py-2">
                        <strong>Đơn giá:</strong> ${formatMoney(price)}/${unit}
                    </div>
                </div>
                <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                    <table class="table table-bordered table-sm" id="utilityInputTable">
                        <thead class="bg-light sticky-top">
                            <tr>
                                <th style="width: 50px;">#</th>
                                <th>Phòng</th>
                                <th>Sinh viên</th>
                                <th style="width: 120px;">Số ${unit}</th>
                                <th style="width: 150px;">Thành tiền</th>
                            </tr>
                        </thead>
                        <tbody id="utilityInputBody">
                            <tr><td colspan="5" class="text-center"><div class="spinner-border spinner-border-sm"></div> Đang tải...</td></tr>
                        </tbody>
                        <tfoot class="bg-light">
                            <tr>
                                <th colspan="3" class="text-right">Tổng cộng:</th>
                                <th id="totalUsage">0 ${unit}</th>
                                <th id="totalAmount">${formatMoney(0)}</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <div class="mt-3">
                    <button type="button" class="btn btn-success" id="saveUtilityInvoices">
                        <i class="fa fa-save"></i> Tạo hóa đơn ${type}
                    </button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Hủy</button>
                </div>
            `);
            
            $('#view_modal').modal('show');
            
            // Load danh sách phòng có sinh viên
            loadRoomsForUtilityInput(type, month, year, unit, price);
        }
        
        // Load danh sách phòng để nhập điện/nước
        function loadRoomsForUtilityInput(type, month, year, unit, price) {
            fetch('../../backend/payment.php?action=getOccupiedRooms&month=' + month + '&year=' + year)
                .then(response => response.json())
                .then(result => {
                    const tbody = $('#utilityInputBody');
                    tbody.empty();
                    
                    if (result.status !== 'success' || !result.data || result.data.length === 0) {
                        tbody.html('<tr><td colspan="5" class="text-center text-muted">Không có phòng nào có sinh viên</td></tr>');
                        return;
                    }
                    
                    result.data.forEach((room, index) => {
                        tbody.append(`
                            <tr data-room-id="${room.room_id}" data-students='${JSON.stringify(room.students || [])}'>
                                <td class="text-center">${index + 1}</td>
                                <td><strong>Tòa ${room.building} - Tầng ${room.floor}</strong><br><small>${room.room_name || ''}</small></td>
                                <td><small>${(room.students || []).map(s => s.student_name).join(', ') || 'N/A'}</small></td>
                                <td>
                                    <input type="number" class="form-control form-control-sm utility-input" 
                                           min="0" step="0.1" value="0" 
                                           data-price="${price}" data-unit="${unit}">
                                </td>
                                <td class="text-right calculated-amount">${formatMoney(0)}</td>
                            </tr>
                        `);
                    });
                    
                    // Tính toán khi nhập số
                    $('.utility-input').on('input', function() {
                        const usage = parseFloat($(this).val()) || 0;
                        const unitPrice = parseFloat($(this).data('price'));
                        const amount = usage * unitPrice;
                        $(this).closest('tr').find('.calculated-amount').text(formatMoney(amount));
                        
                        // Cập nhật tổng
                        updateUtilityTotals(unit);
                    });
                    
                    // Xử lý nút lưu
                    $('#saveUtilityInvoices').off('click').on('click', function() {
                        saveUtilityInvoices(type, month, year, price);
                    });
                })
                .catch(error => {
                    console.error('Error:', error);
                    $('#utilityInputBody').html('<tr><td colspan="5" class="text-center text-danger">Lỗi tải dữ liệu</td></tr>');
                });
        }
        
        // Cập nhật tổng điện/nước
        function updateUtilityTotals(unit) {
            let totalUsage = 0;
            let totalAmount = 0;
            
            $('.utility-input').each(function() {
                const usage = parseFloat($(this).val()) || 0;
                const price = parseFloat($(this).data('price'));
                totalUsage += usage;
                totalAmount += usage * price;
            });
            
            $('#totalUsage').text(totalUsage.toFixed(1) + ' ' + unit);
            $('#totalAmount').text(formatMoney(totalAmount));
        }
        
        // Lưu hóa đơn điện/nước
        function saveUtilityInvoices(type, month, year, price) {
            const invoices = [];
            
            $('#utilityInputTable tbody tr').each(function() {
                const row = $(this);
                const roomId = row.data('room-id');
                const students = row.data('students') || [];
                const usage = parseFloat(row.find('.utility-input').val()) || 0;
                
                if (usage > 0 && students.length > 0) {
                    // Mỗi sinh viên trong phòng đều nhận hóa đơn với toàn bộ tiền của phòng
                    const totalAmountForRoom = usage * price;
                    
                    students.forEach(student => {
                        invoices.push({
                            student_id: student.student_id,
                            contract_id: student.contract_id || null,
                            payment_type: type,
                            amount: totalAmountForRoom,
                            description: `Tiền ${type.toLowerCase()} tháng ${month}/${year} - ${usage} ${type === 'Điện' ? 'kWh' : 'm³'} (tổng tiền phòng)`
                        });
                    });
                }
            });
            
            if (invoices.length === 0) {
                alert_toast('Vui lòng nhập số điện/nước cho ít nhất một phòng', 'warning');
                return;
            }
            
            start_loader();
            
            fetch('../../backend/payment.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    action: 'createUtilityInvoices',
                    month: month,
                    year: year,
                    invoices: invoices
                })
            })
            .then(response => response.json())
            .then(result => {
                if (result.status === 'success') {
                    alert_toast(`Tạo thành công ${result.data.success_count} hóa đơn ${type.toLowerCase()}`, 'success');
                    $('#view_modal').modal('hide');
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
        }

        // Khi trang load chỉ cần tải danh sách thanh toán
        $(document).ready(function() {
            loadPayments();
        });
    </script>
</body>
</html>

