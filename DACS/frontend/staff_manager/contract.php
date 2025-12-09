<?php
include_once __DIR__ . '/../../backend/permission.php';
checkPermission(['manager','staff']);
?>
<!DOCTYPE html>
<html lang="en" style="height: auto;">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Quản lý Hợp đồng | DMS</title>
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
  
<?php include('includes/include.php'); ?>

        <!-- Content Wrapper -->
        <div class="content-wrapper pt-3">
            <section class="content text-dark">
                <div class="container-fluid">
                    <div class="card card-outline rounded-0 card-maroon">
                        <div class="card-header">
                            <h3 class="card-title">Danh sách Hợp đồng Lưu trú</h3>
                            <div class="card-tools">
                                <button class="btn btn-flat btn-info btn-sm" onclick="loadStatistics()">
                                    <span class="fas fa-chart-bar"></span> Thống kê
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="container-fluid">
                                <table class="table table-hover table-striped table-bordered" id="contractsTable">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Mã HĐ</th>
                                            <th>Sinh viên</th>
                                            <th>Mã SV</th>
                                            <th>Phòng</th>
                                            <th>Ngày tạo</th>
                                            <th>Ngày hết hạn</th>
                                            <th>Trạng thái</th>
                                            <th>Thao tác</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- Dữ liệu sẽ được load bằng JavaScript -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
        
        <!-- Modals -->
        <div class="modal fade" id="uni_modal" role='dialog'>
            <div class="modal-dialog modal-md modal-dialog-centered" role="document">
                <div class="modal-content rounded-0">
                    <div class="modal-header">
                        <h5 class="modal-title"></h5>
                    </div>
                    <div class="modal-body"></div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary rounded-0" id='submit' onclick="$('#uni_modal form').submit()">Save</button>
                        <button type="button" class="btn btn-secondary rounded-0" data-dismiss="modal">Cancel</button>
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
        // Kiểm tra đăng nhập
        // if (localStorage.getItem('loggedIn') !== 'true') window.location.replace('index.php');
        
        // function handleLogout() {
        //     localStorage.removeItem('loggedIn');
        //     localStorage.removeItem('username');
        //     window.location.replace('index.php');
        // }
        
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
            $('#uni_modal .modal-footer').show();
            
            $.ajax({
                url: $url,
                method: 'GET',
                success: function(response) {
                    $('#uni_modal .modal-body').html(response);
                    if ($size != '') {
                        $('#uni_modal .modal-dialog').removeClass().addClass('modal-dialog ' + $size + ' modal-dialog-centered');
                    } else {
                        $('#uni_modal .modal-dialog').removeClass().addClass("modal-dialog modal-md modal-dialog-centered");
                    }
                    $('#uni_modal').modal({ show: true, backdrop: 'static', keyboard: false, focus: true });
                    end_loader();
                },
                error: function(xhr, status, error) {
                    end_loader();
                    $('#uni_modal .modal-body').html('<div class="alert alert-danger"><i class="fa fa-exclamation-triangle"></i> Lỗi: Không thể tải form từ: <code>' + $url + '</code><br>Chi tiết: ' + error + '</div>');
                    $('#uni_modal').modal({ show: true, backdrop: 'static', keyboard: false, focus: true });
                }
            });
        }
        
        // Confirmation Modal
        window._conf = function($msg = '', $func = '', $params = []) {
            $('#confirm_modal #confirm').attr('onclick', $func + "(" + $params.join(',') + ")");
            $('#confirm_modal #delete_content').html($msg);
            $('#confirm_modal').modal('show');
        }
        
        var contractsTable;
        
        // Load dữ liệu hợp đồng
        function loadContracts() {
            start_loader();
            $.ajax({
                url: '../../backend/contract.php?action=getAll',
                method: 'GET',
                dataType: 'json',
                success: function(response) {
                    end_loader();
                    if (response.status === 'success') {
                        displayContracts(response.data);
                    } else {
                        alert_toast(response.message, 'error');
                    }
                },
                error: function(xhr, status, error) {
                    end_loader();
                    alert_toast('Lỗi khi tải dữ liệu: ' + error, 'error');
                }
            });
        }
        
        // Hiển thị danh sách hợp đồng
        function displayContracts(contracts) {
            var tbody = $('#contractsTable tbody');
            tbody.empty();
            
            if (contracts.length === 0) {
                tbody.append('<tr><td colspan="9" class="text-center">Chưa có hợp đồng nào</td></tr>');
                return;
            }
            
            $.each(contracts, function(index, contract) {
                // 1. Xử lý Badge Trạng thái
                var statusBadge = '';
                if (contract.status === 'Hiệu lực') {
                    statusBadge = '<span class="badge badge-success px-3 rounded-pill">Hiệu lực</span>';
                } else if (contract.status === 'Hết hạn') {
                    statusBadge = '<span class="badge badge-secondary px-3 rounded-pill">Hết hạn</span>';
                } else if (contract.status === 'Chờ duyệt trả phòng') {
                    // Thêm badge màu vàng/cam cho trạng thái chờ
                    statusBadge = '<span class="badge badge-warning px-3 rounded-pill">Chờ duyệt trả phòng</span>';
                } else {
                    statusBadge = '<span class="badge badge-danger px-3 rounded-pill">Đã hủy</span>';
                }
                
                var row = '<tr>' +
                    '<td class="text-center">' + (index + 1) + '</td>' +
                    '<td class="text-center">' + contract.contract_id + '</td>' +
                    '<td>' + contract.student_name + '</td>' +
                    '<td class="text-center">' + (contract.student_code || 'N/A') + '</td>' +
                    '<td>' + contract.room_info + '</td>' +
                    '<td>' + contract.created_date + '</td>' +
                    '<td>' + contract.end_date + '</td>' +
                    '<td class="text-center">' + statusBadge + '</td>' +
                    '<td align="center">' +
                    '<div class="btn-group">' +
                    '<button type="button" class="btn btn-flat p-1 btn-default btn-sm dropdown-toggle dropdown-icon" data-toggle="dropdown">' +
                    'Thao tác' +
                    '</button>' +
                    '<div class="dropdown-menu" role="menu">' +
                    '<a class="dropdown-item" href="#" onclick="viewContract(' + contract.contract_id + ')">' +
                    '<span class="fa fa-eye text-dark"></span> Xem chi tiết' +
                    '</a>' +
                    '<div class="dropdown-divider"></div>' +
                    '<a class="dropdown-item" href="#" onclick="extendContract(' + contract.contract_id + ')">' +
                    '<span class="fa fa-calendar-plus text-info"></span> Gia hạn' +
                    '</a>' +
                    '<div class="dropdown-divider"></div>' +
                    '<a class="dropdown-item" href="#" onclick="changeRoomContract(' + contract.contract_id + ')">' +
                    '<span class="fa fa-exchange-alt text-primary"></span> Chuyển phòng' +
                    '</a>';
                
                // 2. LOGIC HIỂN THỊ NÚT CHẤM DỨT / DUYỆT TRẢ PHÒNG
                // Cho phép chấm dứt nếu đang "Hiệu lực" HOẶC "Chờ duyệt trả phòng"
                if (contract.status === 'Hiệu lực' || contract.status === 'Chờ duyệt trả phòng') {
                    
                    // Đổi tên nút và icon dựa theo ngữ cảnh cho chuyên nghiệp hơn
                    var btnLabel = (contract.status === 'Chờ duyệt trả phòng') ? 'Duyệt trả phòng' : 'Chấm dứt';
                    var btnIcon = (contract.status === 'Chờ duyệt trả phòng') ? 'fa-check-circle' : 'fa-ban';
                    var btnColor = (contract.status === 'Chờ duyệt trả phòng') ? 'text-success' : 'text-warning';

                    row += '<div class="dropdown-divider"></div>' +
                        '<a class="dropdown-item" href="#" onclick="terminateContract(' + contract.contract_id + ')">' +
                        '<span class="fa ' + btnIcon + ' ' + btnColor + '"></span> ' + btnLabel +
                        '</a>';
                }
                
                row += '<div class="dropdown-divider"></div>' +
                    '<a class="dropdown-item" href="#" onclick="deleteContract(' + contract.contract_id + ')">' +
                    '<span class="fa fa-trash text-danger"></span> Xóa' +
                    '</a>' +
                    '</div>' +
                    '</div>' +
                    '</td>' +
                    '</tr>';
                    
                tbody.append(row);
            });
            
            // Initialize hoặc refresh DataTable
            if (contractsTable) {
                contractsTable.destroy();
            }
            contractsTable = $('#contractsTable').DataTable({
                columnDefs: [{ orderable: false, targets: [8] }],
                order: [[0, 'asc']],
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/vi.json'
                }
            });
            $('.dataTable td, .dataTable th').addClass('py-1 px-2 align-middle');
        }
        
        // Xem chi tiết hợp đồng
        function viewContract(id) {
            uni_modal("<i class='fa fa-eye'></i> Chi tiết Hợp đồng", "contracts/view_contract.php?id=" + id, "modal-lg");
        }
        
        // Gia hạn hợp đồng
        function extendContract(id) {
            uni_modal("<i class='fa fa-calendar-plus'></i> Gia hạn Hợp đồng", "contracts/extend_contract.php?id=" + id);
        }
        
        // Chuyển phòng
        function changeRoomContract(id) {
            uni_modal("<i class='fa fa-exchange-alt'></i> Chuyển phòng", "contracts/change_room.php?id=" + id, "modal-lg");
        }
        
        // Chấm dứt hợp đồng
        function terminateContract(id) {
            _conf("Bạn có chắc chắn muốn chấm dứt hợp đồng này?<br>Hành động này sẽ giải phóng phòng cho sinh viên khác.", "confirm_terminate", [id]);
        }
        
        function confirm_terminate(id) {
            start_loader();
            $.ajax({
                url: '../../backend/contract.php',
                method: 'POST',
                data: JSON.stringify({
                    action: 'terminate',
                    contract_id: id
                }),
                contentType: 'application/json',
                dataType: 'json',
                success: function(response) {
                    end_loader();
                    if (response.status === 'success') {
                        $('#confirm_modal').modal('hide');
                        alert_toast(response.message, 'success');
                        loadContracts();
                    } else {
                        alert_toast(response.message, 'error');
                    }
                },
                error: function(xhr, status, error) {
                    end_loader();
                    alert_toast('Lỗi: ' + error, 'error');
                }
            });
        }
        
        // Xóa hợp đồng
        function deleteContract(id) {
            _conf("Bạn có chắc chắn muốn xóa vĩnh viễn hợp đồng này?<br><b>Cảnh báo:</b> Không thể khôi phục sau khi xóa!", "confirm_delete", [id]);
        }
        
        function confirm_delete(id) {
            start_loader();
            $.ajax({
                url: '../../backend/contract.php?id=' + id,
                method: 'DELETE',
                dataType: 'json',
                success: function(response) {
                    end_loader();
                    if (response.status === 'success') {
                        $('#confirm_modal').modal('hide');
                        alert_toast(response.message, 'success');
                        loadContracts();
                    } else {
                        alert_toast(response.message, 'error');
                    }
                },
                error: function(xhr, status, error) {
                    end_loader();
                    alert_toast('Lỗi: ' + error, 'error');
                }
            });
        }
        
        // Thống kê
        function loadStatistics() {
            start_loader();
            $.ajax({
                url: '../../backend/contract.php?action=getStatistics',
                method: 'GET',
                dataType: 'json',
                success: function(response) {
                    end_loader();
                    if (response.status === 'success') {
                        var stats = response.data;
                        var html = '<div class="row">';
                        html += '<div class="col-md-4"><div class="info-box bg-success"><span class="info-box-icon"><i class="fas fa-check"></i></span><div class="info-box-content"><span class="info-box-text">Hiệu lực</span><span class="info-box-number">' + (stats['Hiệu lực'] || 0) + '</span></div></div></div>';
                        html += '<div class="col-md-4"><div class="info-box bg-warning"><span class="info-box-icon"><i class="fas fa-clock"></i></span><div class="info-box-content"><span class="info-box-text">Hết hạn</span><span class="info-box-number">' + (stats['Hết hạn'] || 0) + '</span></div></div></div>';
                        html += '<div class="col-md-4"><div class="info-box bg-danger"><span class="info-box-icon"><i class="fas fa-ban"></i></span><div class="info-box-content"><span class="info-box-text">Đã hủy</span><span class="info-box-number">' + (stats['Đã hủy'] || 0) + '</span></div></div></div>';
                        html += '</div>';
                        
                        $('#uni_modal .modal-title').html('<i class="fas fa-chart-bar"></i> Thống kê Hợp đồng');
                        $('#uni_modal .modal-body').html(html);
                        $('#uni_modal .modal-footer').hide();
                        $('#uni_modal').modal('show');
                    } else {
                        alert_toast(response.message, 'error');
                    }
                },
                error: function(xhr, status, error) {
                    end_loader();
                    alert_toast('Lỗi: ' + error, 'error');
                }
            });
        }
        
        // Load dữ liệu khi trang được tải
        $(document).ready(function(){
            loadContracts();
        });
    </script>

</body>
</html>

