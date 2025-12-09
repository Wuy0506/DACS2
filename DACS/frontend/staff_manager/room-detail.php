<?php
// Get room_id from URL
$roomId = isset($_GET['room_id']) ? intval($_GET['room_id']) : 0;
?>
<!DOCTYPE html>
<html lang="en" style="height: auto;">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Chi tiết Phòng | DMS</title>
    <link rel="icon" href="../uploads/logo.png" />
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="../plugins/fontawesome-free/css/all.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="../dist/css/adminlte.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="../dist/css/custom.css">
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="../plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css">
    
    <style>
        .table-success {
            background-color: #d4edda !important;
        }
        
        .empty-bed-row {
            background-color: #d4edda !important;
        }
    </style>
</head>

<body class="sidebar-mini layout-fixed control-sidebar-slide-open layout-navbar-fixed text-sm">
    <div class="wrapper">
        <?php include('includes/include.php'); ?>
        
        <!-- Content Wrapper -->
        <div class="content-wrapper pt-3">
            <section class="content text-dark">
                <div class="container-fluid">
                    <!-- Back Button -->
                    <div class="mb-3">
                        <a href="rooms-management.php" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Quay lại
                        </a>
                    </div>
                    
                    <!-- Table of People in Room -->
                    <div class="card card-outline card-primary">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-users"></i> Danh sách Người trong Phòng</h3>
                        </div>
                        <div class="card-body">
                            <table class="table table-bordered table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>Giường</th>
                                        <th>Họ tên</th>
                                        <th>MSSV</th>
                                        <th>Email</th>
                                        <th>SĐT</th>
                                        <th>Khoa</th>
                                        <th>Ngành</th>
                                        <th>Giới tính</th>
                                        <th>Thời gian</th>
                                        <th>Trạng thái</th>
                                        <th>Hành động</th>
                                    </tr>
                                </thead>
                                <tbody id="peopleTableBody">
                                    <tr>
                                        <td colspan="11" class="text-center">
                                            <div class="spinner-border" role="status">
                                                <span class="sr-only">Loading...</span>
                                            </div>
                                            <p>Đang tải...</p>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </section>
        </div>
        
        <?php include 'includes/footer.php'; ?>
    </div>

    <!-- jQuery -->
    <script src="../plugins/jquery/jquery.min.js"></script>
    <!-- Bootstrap 4 -->
    <script src="../plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <!-- SweetAlert2 -->
    <script src="../plugins/sweetalert2/sweetalert2.min.js"></script>
    <!-- AdminLTE App -->
    <script src="../dist/js/adminlte.js"></script>
    
    <script>
        
    <?php include('includes/login_session.php'); ?>
        const roomId = <?php echo $roomId; ?>;
        
        // Toast Notification
        window.alert_toast = function($msg = 'TEST', $bg = 'success', $pos = '') {
            var Toast = Swal.mixin({
                toast: true,
                position: $pos || 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true
            });
            Toast.fire({
                icon: $bg,
                title: $msg
            });
        }
        
        // Load room detail
        $(document).ready(function() {
            if (roomId === 0) {
                alert('Room ID không hợp lệ');
                window.location.href = 'rooms-management.php';
                return;
            }
            
            loadRoomDetail();
        });
        
        function loadRoomDetail() {
            $.ajax({
                url: '../../backend/controller/RoomManagementController.php?action=get-room-detail&room_id=' + roomId,
                method: 'GET',
                dataType: 'json',
                success: function(response) {
                    if(response.success) {
                        displayPeopleTable(response.data.beds);
                    } else {
                        alert_toast('Lỗi: ' + response.message, 'error');
                        setTimeout(() => {
                            window.location.href = 'rooms-management.php';
                        }, 2000);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error loading room detail:', error);
                    alert_toast('Lỗi tải thông tin phòng', 'error');
                    setTimeout(() => {
                        window.location.href = 'rooms-management.php';
                    }, 2000);
                }
            });
        }
        
        function displayPeopleTable(beds) {
            if (!beds || beds.length === 0) {
                $('#peopleTableBody').html('<tr><td colspan="11" class="text-center text-muted">Không có giường nào trong phòng</td></tr>');
                return;
            }
            
            const html = beds.map(bed => {
                const isOccupied = bed.bed_status === 'Đang sử dụng' && bed.full_name;
                const statusBadge = isOccupied 
                    ? '<span class="badge badge-danger">Đang sử dụng</span>' 
                    : '<span class="badge badge-success">Trống</span>';
                
                if (!isOccupied) {
                    // Empty bed row
                    return `
                        <tr class="empty-bed-row">
                            <td class="text-center"><strong>Giường ${bed.bed_number}</strong></td>
                            <td colspan="8" class="text-center text-muted"><em>Giường trống</em></td>
                            <td class="text-center">${statusBadge}</td>
                            <td class="text-center">-</td>
                        </tr>
                    `;
                }
                
                // Occupied bed row
                return `
                    <tr>
                        <td class="text-center"><strong>Giường ${bed.bed_number}</strong></td>
                        <td>${bed.full_name || 'N/A'}</td>
                        <td>${bed.student_code || 'N/A'}</td>
                        <td>${bed.email || 'N/A'}</td>
                        <td>${bed.phone || 'N/A'}</td>
                        <td>${bed.faculty || 'N/A'}</td>
                        <td>${bed.major || 'N/A'}</td>
                        <td>${bed.gender || 'N/A'}</td>
                        <td class="text-nowrap">
                            ${bed.start_date && bed.end_date 
                                ? formatDate(bed.start_date) + '<br><small>đến</small><br>' + formatDate(bed.end_date)
                                : 'N/A'
                            }
                        </td>
                        <td class="text-center">${statusBadge}</td>
                        <td class="text-center">
                            <div class="btn-group btn-group-sm" role="group">
                                <button class="btn btn-danger" onclick="removeStudentFromBed(${bed.registration_id}, '${bed.full_name}')" title="Xóa khỏi giường">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                `;
            }).join('');
            
            $('#peopleTableBody').html(html);
        }
        
        function formatDate(dateString) {
            if (!dateString) return 'N/A';
            const date = new Date(dateString);
            return date.toLocaleDateString('vi-VN');
        }
        
        // Remove student from bed
        function removeStudentFromBed(registrationId, studentName) {
            Swal.fire({
                title: 'Xác nhận xóa',
                html: `Bạn có chắc muốn xóa sinh viên <strong>${studentName}</strong> khỏi giường?<br><small class="text-danger">Hành động này sẽ hủy đăng ký phòng của sinh viên.</small>`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Xóa',
                confirmButtonColor: '#d33',
                cancelButtonText: 'Hủy'
            }).then((result) => {
                if (result.isConfirmed) {
                    start_loader();
                    $.ajax({
                        url: '../../backend/controller/RoomManagementController.php?action=remove-student',
                        method: 'POST',
                        data: JSON.stringify({
                            registration_id: registrationId
                        }),
                        contentType: 'application/json',
                        dataType: 'json',
                        success: function(response) {
                            end_loader();
                            if(response.success) {
                                alert_toast(response.message, 'success');
                                loadRoomDetail(); // Reload room detail
                            } else {
                                alert_toast('Lỗi: ' + response.message, 'error');
                            }
                        },
                        error: function(xhr, status, error) {
                            end_loader();
                            console.error('Error removing student:', error);
                            alert_toast('Lỗi xóa sinh viên', 'error');
                        }
                    });
                }
            });
        }
    </script>
</body>
</html>
