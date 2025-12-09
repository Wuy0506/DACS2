<?php
include_once __DIR__ . '/../../backend/permission.php';
checkPermission(['manager','staff']);
?>
<!DOCTYPE html>
<html lang="en" style="height: auto;">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Room Registrations | DMS</title>
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
    
   
</head>
<body class="sidebar-mini layout-fixed control-sidebar-slide-open layout-navbar-fixed text-sm">
    <div class="wrapper">
      <?php include 'includes/include.php'; ?>
        <!-- Content Wrapper -->
        <div class="content-wrapper pt-3">
            <section class="content text-dark">
                <div class="container-fluid">
                    <!-- Statistics Cards -->
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <div class="small-box bg-warning">
                                <div class="inner">
                                    <h3 id="pendingCount">0</h3>
                                    <p>Chờ duyệt</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-clock"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="small-box bg-success">
                                <div class="inner">
                                    <h3 id="approvedCount">0</h3>
                                    <p>Đã duyệt</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-check"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="small-box bg-danger">
                                <div class="inner">
                                    <h3 id="rejectedCount">0</h3>
                                    <p>Từ chối</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-times"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                        <div class="small-box bg-secondary">
                            <div class="inner">
                                <h3 id="checkoutCount">0</h3>
                                <p>Đã trả phòng</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-sign-out-alt"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Registrations Table -->
                    <div class="card card-outline rounded-0 card-maroon">
                        <div class="card-header">
                            <h3 class="card-title">Danh sách đăng ký phòng</h3>
                            <div class="card-tools">
                                <div class="btn-group">
                                    <button class="btn btn-sm btn-default" onclick="filterByStatus('all')">
                                        <span class="fas fa-list"></span> Tất cả
                                    </button>
                                    <button class="btn btn-sm btn-warning" onclick="filterByStatus('Chờ duyệt')">
                                        <span class="fas fa-clock"></span> Chờ duyệt
                                    </button>
                                    <button class="btn btn-sm btn-success" onclick="filterByStatus('Đã duyệt')">
                                        <span class="fas fa-check"></span> Đã duyệt
                                    </button>
                                    <button class="btn btn-sm btn-danger" onclick="filterByStatus('Từ chối')">
                                        <span class="fas fa-times"></span> Từ chối
                                    </button>
                                    <button class="btn btn-sm btn-secondary" onclick="filterByStatus('Đã trả phòng')">
                                        <span class="fas fa-sign-out-alt"></span> Đã trả phòng
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="container-fluid">
                                <table class="table table-hover table-striped table-bordered" id="registrationsTable">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Ngày đăng ký</th>
                                            <th>Họ tên</th>
                                            <th>Email</th>
                                            <th>Điện thoại</th>
                                            <th>Khoa</th>
                                            <th>Phòng</th>
                                            <th>Giường</th>
                                            <th>Thời gian thuê</th>
                                            <th>Trạng thái</th>
                                            <th>Hành động</th>
                                        </tr>
                                    </thead>
                                    <tbody id="registrationsTableBody">
                                        <tr>
                                            <td colspan="11" class="text-center">Đang tải dữ liệu...</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
<?php include 'includes/footer.php'; ?>
    <!-- jQuery_PLACEHOLDER_SCRIPTS -->
    <script src="../plugins/jquery/jquery.min.js"></script>
    <!-- Bootstrap 4 -->
    <script src="../plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <!-- DataTables -->
    <script src="../plugins/datatables/jquery.dataTables.min.js"></script>
    <script src="../plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
    <!-- AdminLTE App -->
    <script src="../dist/js/adminlte.min.js"></script>
    
    <script>
    
        
    let dataTable;
        let currentFilter = 'all';
        
        // Initialize
        $(document).ready(function(){
            loadStatistics();
            loadRegistrations();
        });
        
        // Load statistics
        function loadStatistics() {
            $.ajax({
                url: '../../backend/controller/staff/RoomRegistrationController.php?action=get-statistics',
                method: 'GET',
                dataType: 'json',
                success: function(response) {
                    if(response.success) {
                        $('#pendingCount').text(response.data.pending);
                        $('#approvedCount').text(response.data.approved);
                        $('#rejectedCount').text(response.data.rejected);
                        $('#checkoutCount').text(response.data.checkout); // Giả sử backend trả về key 'checkout'
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error loading statistics:', error);
                }
            });
        }
        
        // Load registrations
        function loadRegistrations(status = null) {
            let url = '../../backend/controller/staff/RoomRegistrationController.php?action=get-all';
            if(status) {
                url = '../../backend/controller/staff/RoomRegistrationController.php?action=get-by-status&status=' + encodeURIComponent(status);
            }
            
            $.ajax({
                url: url,
                method: 'GET',
                dataType: 'json',
                success: function(response) {
                    if(response.success) {
                        displayRegistrations(response.data);
                    } else {
                        $('#registrationsTableBody').html('<tr><td colspan="11" class="text-center text-danger">' + response.message + '</td></tr>');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error loading registrations:', error);
                    $('#registrationsTableBody').html('<tr><td colspan="11" class="text-center text-danger">Lỗi tải dữ liệu</td></tr>');
                }
            });
        }
        
        // Display registrations in table
        function displayRegistrations(registrations) {
            // Destroy existing DataTable if it exists
            if(dataTable) {
                dataTable.destroy();
            }
            
            let html = '';
            
            if(registrations.length === 0) {
                html = '<tr><td colspan="11" class="text-center">Không có dữ liệu</td></tr>';
            } else {
                registrations.forEach(function(reg, index) {
                    let statusBadge = '';
                    if(reg.status === 'Chờ duyệt') {
                        statusBadge = '<span class="badge badge-warning">Chờ duyệt</span>';
                    } else if(reg.status === 'Đã duyệt') {
                        statusBadge = '<span class="badge badge-success">Đã duyệt</span>';
                    } else if(reg.status === 'Từ chối') {
                        statusBadge = '<span class="badge badge-danger">Từ chối</span>';
                    }else if(reg.status === 'Đã trả phòng') {
                        // Thêm đoạn này (Dùng màu xám badge-secondary hoặc màu xanh badge-info)
                        statusBadge = '<span class="badge badge-secondary">Đã trả phòng</span>'; 
                    }
                    
                    // Action buttons - giống accounts.php
                    let actionButtons = `
                        <div class="btn-group">
                            <button type="button" class="btn btn-flat p-1 btn-default btn-sm dropdown-toggle dropdown-icon" data-toggle="dropdown">
                                Action
                            </button>
                            <div class="dropdown-menu" role="menu">
                    `;
                

                                        // 1. Chỉ hiện nút DUYỆT nếu chưa duyệt VÀ chưa trả phòng
                    if(reg.status !== 'Đã duyệt' && reg.status !== 'Đã trả phòng') {
                        actionButtons += `
                            <a class="dropdown-item" href="#" onclick="changeStatus(${reg.registration_id}, 'approve'); return false;">
                                <span class="fa fa-check text-success"></span> Duyệt
                            </a>
                            <div class="dropdown-divider"></div>
                        `;
                    }

                    // 2. Chỉ hiện nút TỪ CHỐI nếu chưa từ chối VÀ chưa trả phòng
                    if(reg.status !== 'Từ chối' && reg.status !== 'Đã trả phòng') {
                        actionButtons += `
                            <a class="dropdown-item" href="#" onclick="changeStatus(${reg.registration_id}, 'reject'); return false;">
                                <span class="fa fa-times text-danger"></span> Từ chối
                            </a>
                            <div class="dropdown-divider"></div>
                        `;
                    }

                    // 3. (Tùy chọn) Nút Chờ duyệt có thể ẩn luôn nếu đã trả phòng
                    if(reg.status !== 'Chờ duyệt' && reg.status !== 'Đã trả phòng') {
                        actionButtons += `
                            <a class="dropdown-item" href="#" onclick="changeStatus(${reg.registration_id}, 'pending'); return false;">
                                <span class="fa fa-clock text-warning"></span> Chờ duyệt
                            </a>
                            <div class="dropdown-divider"></div>
                        `;
                    }
                    
                    // Xóa dropdown-divider cuối cùng nếu có
                    actionButtons = actionButtons.replace(/<div class="dropdown-divider"><\/div>\s*$/, '');
                    
                    actionButtons += `
                            </div>
                        </div>
                    `;
                  
                    
                    html += `
                        <tr>
                            <td class="text-center">${index + 1}</td>
                            <td>${formatDateTime(reg.request_date)}</td>
                            <td>${reg.full_name || 'N/A'}</td>
                            <td>${reg.email || 'N/A'}</td>
                            <td>${reg.phone || 'N/A'}</td>
                            <td>${reg.faculty || 'N/A'}</td>
                            <td>Tòa ${reg.building} - Tầng ${reg.floor} - Phong ${reg.room_name}</td>
                            <td class="text-center">${reg.bed_number || 'N/A'}</td>
                            <td>${formatDate(reg.start_date)} đến ${formatDate(reg.end_date)}</td>
                            <td class="text-center">${statusBadge}</td>
                            <td class="text-center">${actionButtons}</td>
                        </tr>
                    `;
                });
            }
            
            $('#registrationsTableBody').html(html);
            
            // Initialize DataTable
            dataTable = $('#registrationsTable').DataTable({
                columnDefs: [
                    { orderable: false, targets: [10] }
                ],
                order: [[1, 'desc']],
                pageLength: 25
            });
            
            $('.dataTable td, .dataTable th').addClass('py-1 px-2 align-middle');
        }
        
        // Filter by status
        function filterByStatus(status) {
            currentFilter = status;
            if(status === 'all') {
                loadRegistrations();
            } else {
                loadRegistrations(status);
            }
        }
        
        // Change registration status - Unified function
        function changeStatus(registrationId, action) {
            let confirmMessage = '';
            let actionUrl = '';
            
            switch(action) {
                case 'approve':
                    confirmMessage = 'Bạn có chắc chắn muốn DUYỆT đăng ký này?';
                    actionUrl = '../../backend/controller/staff/RoomRegistrationController.php?action=approve';
                    break;
                case 'reject':
                    confirmMessage = 'Bạn có chắc chắn muốn TỪ CHỐI đăng ký này?';
                    actionUrl = '../../backend/controller/staff/RoomRegistrationController.php?action=reject';
                    break;
                case 'pending':
                    confirmMessage = 'Bạn có chắc chắn muốn đặt lại về CHỜ DUYỆT?';
                    actionUrl = '../../backend/controller/staff/RoomRegistrationController.php?action=reset-to-pending';
                    break;
                default:
                    alert('Hành động không hợp lệ');
                    return;
            }
            
            if(!confirm(confirmMessage)) {
                return;
            }
            
            $.ajax({
                url: actionUrl,
                method: 'POST',
                data: JSON.stringify({
                    registration_id: registrationId
                }),
                contentType: 'application/json',
                dataType: 'json',
                success: function(response) {
                    if(response.success) {
                        alert(response.message);
                        loadStatistics();
                        if(currentFilter === 'all') {
                            loadRegistrations();
                        } else {
                            loadRegistrations(currentFilter);
                        }
                    } else {
                        alert('Lỗi: ' + response.message);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error changing status:', error);
                    alert('Lỗi khi thay đổi trạng thái');
                }
            });
        }
        
        // Format date
        function formatDate(dateString) {
            if(!dateString) return 'N/A';
            const date = new Date(dateString);
            return date.toLocaleDateString('vi-VN');
        }
        
        // Format datetime
        function formatDateTime(dateString) {
            if(!dateString) return 'N/A';
            const date = new Date(dateString);
            return date.toLocaleString('vi-VN');
        }
        
        // View registration details
        function viewDetails(registrationId) {
            $.ajax({
                url: '../../backend/controller/staff/RoomRegistrationController.php?action=get-by-id&id=' + registrationId,
                method: 'GET',
                dataType: 'json',
                success: function(response) {
                    if(response.success) {
                        const reg = response.data;
                        let statusBadge = '';
                        if(reg.status === 'Chờ duyệt') {
                            statusBadge = '<span class="badge badge-warning">Chờ duyệt</span>';
                        } else if(reg.status === 'Đã duyệt') {
                            statusBadge = '<span class="badge badge-success">Đã duyệt</span>';
                        } else if(reg.status === 'Từ chối') {
                            statusBadge = '<span class="badge badge-danger">Từ chối</span>';
                        }
                        
                        const detailsHtml = `
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>Họ tên:</strong> ${reg.full_name || 'N/A'}</p>
                                    <p><strong>Email:</strong> ${reg.email || 'N/A'}</p>
                                    <p><strong>Điện thoại:</strong> ${reg.phone || 'N/A'}</p>
                                    <p><strong>Khoa:</strong> ${reg.faculty || 'N/A'}</p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Phòng:</strong> Tòa ${reg.building} - Tầng ${reg.floor}</p>
                                    <p><strong>Giường:</strong> ${reg.bed_number || 'N/A'}</p>
                                    <p><strong>Thời gian thuê:</strong> ${formatDate(reg.start_date)} đến ${formatDate(reg.end_date)}</p>
                                    <p><strong>Ngày đăng ký:</strong> ${formatDateTime(reg.request_date)}</p>
                                    <p><strong>Trạng thái:</strong> ${statusBadge}</p>
                                </div>
                            </div>
                        `;
                        
                        // Hiển thị modal với thông tin chi tiết
                        alert('Chi tiết đăng ký:\n\n' + 
                              'Họ tên: ' + (reg.full_name || 'N/A') + '\n' +
                              'Email: ' + (reg.email || 'N/A') + '\n' +
                              'Điện thoại: ' + (reg.phone || 'N/A') + '\n' +
                              'Khoa: ' + (reg.faculty || 'N/A') + '\n' +
                              'Phòng: Tòa ' + reg.building + ' - Tầng ' + reg.floor + '\n' +
                              'Giường: ' + (reg.bed_number || 'N/A') + '\n' +
                              'Thời gian thuê: ' + formatDate(reg.start_date) + ' đến ' + formatDate(reg.end_date) + '\n' +
                              'Ngày đăng ký: ' + formatDateTime(reg.request_date) + '\n' +
                              'Trạng thái: ' + reg.status);
                    } else {
                        alert('Lỗi: ' + response.message);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error loading details:', error);
                    alert('Lỗi khi tải chi tiết đăng ký');
                }
            });
        }
    </script>

</body>
</html>


