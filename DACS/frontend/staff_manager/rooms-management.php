<?php
include_once __DIR__ . '/../../backend/permission.php';
checkPermission(['manager','staff']);
?>
<!DOCTYPE html>
<html lang="en" style="height: auto;">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Room Management | DMS</title>
    <link rel="icon" href="../uploads/logo.png" />

    <!-- Font Awesome -->
    <link rel="stylesheet" href="../plugins/fontawesome-free/css/all.min.css">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <!-- Tempusdominus Bootstrap 4 -->
    <link rel="stylesheet" href="../plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css">
    <!-- iCheck -->
    <link rel="stylesheet" href="../plugins/icheck-bootstrap/icheck-bootstrap.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="../dist/css/adminlte.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="../dist/css/custom.css">
    <!-- overlayScrollbars -->
    <link rel="stylesheet" href="../plugins/overlayScrollbars/css/OverlayScrollbars.min.css">
    <!-- Daterange picker -->
    <link rel="stylesheet" href="../plugins/daterangepicker/daterangepicker.css">
    <!-- Summernote -->
    <link rel="stylesheet" href="../plugins/summernote/summernote-bs4.min.css">
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="../plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css">
    <link rel="stylesheet" href="css/roomss-management.css">
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

    /* Floor Layout Styles */
    .floor-section {
        background: #f8f9fa;
        border-radius: 10px;
        padding: 15px;
        margin-bottom: 20px;

    }

    .floor-header {
        background: linear-gradient(135deg, #ff0000cf 0%, #0056b3 100%);
        color: white;
        padding: 12px 18px;
        border-radius: 8px;
        margin-bottom: 12px;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .floor-header h4 {
        margin: 0;
        font-weight: 600;
        font-size: 1.1rem;
    }

    .floor-rooms {
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
    }

    .building-selector {
        background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
        padding: 20px 25px;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        margin-bottom: 25px;
        border: 1px solid #e0e0e0;
    }

    .building-selector label {
        font-size: 0.9rem;
        color: #495057;
    }

    .building-selector .form-select,
    .building-selector .form-control {
        border: 1px solid #ced4da;
        border-radius: 6px;
        padding: 8px 12px;
        font-size: 0.95rem;
    }

    .building-selector .form-select:focus,
    .building-selector .form-control:focus {
        border-color: #007bff;
        box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.15);
    }
    </style>
</head>

<body class="sidebar-mini layout-fixed control-sidebar-slide-open layout-navbar-fixed text-sm">
    <div class="wrapper">
        <?php include ('includes/include.php'); ?>
        <!-- Content Wrapper -->
        <div class="content-wrapper pt-3">
            <section class="content text-dark">
                <div class="container-fluid">
                    <!-- Room Management Section -->
                    <div class="row">
                        <div class="col-12">
                            <h1 class="mb-3"><i class="fas fa-door-open"></i> Room Management</h1>

                            <!-- Building Selector -->
                            <div class="building-selector">
                                <div class="row align-items-center">
                                    <div class="col-md-4">
                                        <label class="fw-bold mb-2"><i class="fas fa-building"></i> Chọn Tòa
                                            Nhà:</label>
                                        <select class="form-select" id="buildingSelect">
                                            <option value="">-- Chọn tòa nhà --</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="fw-bold mb-2"><i class="fas fa-filter"></i> Lọc Trạng
                                            Thái:</label>
                                        <select class="form-select" id="filterStatus">
                                            <option value="">Tất cả</option>
                                            <option value="Valiable">Còn trống</option>
                                            <option value="Unvaliable">Đã đầy/Bảo trì</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="fw-bold mb-2"><i class="fas fa-search"></i> Tìm Kiếm:</label>
                                        <input type="text" class="form-control" placeholder="Tìm phòng..."
                                            id="searchRoom">
                                    </div>
                                </div>
                            </div>

                            <!-- Filter and Search Bar -->
                            <div class="status-sort-bar">
                                <div class="status-sort-by-options">
                                </div>
                                <div>
                                    <button class="btn btn-success mr-2" onclick="openAddRoomModal()">
                                        <i class="fas fa-plus-circle mr-2"></i>Thêm phòng mới
                                    </button>
                                    <button class="btn btn-primary" onclick="openStudentSearchModal()">
                                        <i class="fas fa-user-plus mr-2"></i>Thêm sinh viên vào phòng
                                    </button>
                                </div>
                            </div>

                            <!-- Student Search Panel -->
                            <div id="studentSearchPanel" class="card mt-3" style="display: none;">
                                <div class="card-header bg-primary text-white">
                                    <h5 class="mb-0">
                                        <i class="fas fa-search"></i> Tìm kiếm sinh viên
                                        <button class="btn btn-sm btn-light float-right"
                                            onclick="closeStudentSearchPanel()">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="input-group mb-3">
                                        <input type="text" class="form-control"
                                            placeholder="Nhập tên, email, mã sinh viên..." id="studentSearchInput">
                                        <button class="btn btn-primary" onclick="searchStudents()">
                                            <i class="fas fa-search"></i> Tìm kiếm
                                        </button>
                                    </div>
                                    <div id="studentSearchResults" class="row">
                                        <!-- Kết quả tìm kiếm sẽ hiển thị ở đây -->
                                    </div>
                                </div>
                            </div>

                            <!-- Room Layout by Floor -->
                            <div id="roomList">
                                <!-- Rooms will be grouped by floor here -->
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
        <!-- /.content-wrapper -->

        <?php include('includes/footer.php'); ?>
    </div>
    <!-- ./wrapper -->

    <!-- jQuery -->
    <script src="../plugins/jquery/jquery.min.js"></script>
    <!-- jQuery UI 1.11.4 -->
    <script src="../plugins/jquery-ui/jquery-ui.min.js"></script>
    <!-- SweetAlert2 -->
    <script src="../plugins/sweetalert2/sweetalert2.min.js"></script>
    <!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
    <script>
    $.widget.bridge('uibutton', $.ui.button);
    var _base_url_ = '../';
    </script>
    <!-- Bootstrap 4 -->
    <script src="../plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <!-- ChartJS -->
    <script src="../plugins/chart.js/Chart.min.js"></script>
    <!-- Select2 -->
    <script src="../plugins/select2/js/select2.full.min.js"></script>
    <!-- daterangepicker -->
    <script src="../plugins/moment/moment.min.js"></script>
    <script src="../plugins/daterangepicker/daterangepicker.js"></script>
    <!-- Tempusdominus Bootstrap 4 -->
    <script src="../plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js"></script>
    <!-- Summernote -->
    <script src="../plugins/summernote/summernote-bs4.min.js"></script>
    <!-- overlayScrollbars -->
    <script src="../plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js"></script>
    <!-- AdminLTE App -->
    <script src="../dist/js/adminlte.js"></script>
    <script>
    // Preloader Functions
    function start_loader() {
        if ($('#preloader').length === 0) {
            $('body').append(
                '<div id="preloader" style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9999; display: flex; align-items: center; justify-content: center;"><div class="spinner-border text-light" role="status" style="width: 3rem; height: 3rem;"><span class="sr-only">Loading...</span></div></div>'
            );
        }
    }

    function end_loader() {
        $('#preloader').fadeOut('fast', function() {
            $(this).remove();
        });
    }

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

    // Universal Modal
    window.uni_modal = function($title = '', $url = '', $size = "") {
        start_loader();
        $('#uni_modal .modal-title').html($title);
        $('#uni_modal .modal-body').html(
            '<div class="alert alert-info"><i class="fa fa-info-circle"></i> Demo: This would load form from: <code>' +
            $url + '</code></div>');

        if ($size != '') {
            $('#uni_modal .modal-dialog').addClass($size + ' modal-dialog-centered');
        } else {
            $('#uni_modal .modal-dialog').removeAttr("class").addClass(
                "modal-dialog modal-md modal-dialog-centered");
        }

        $('#uni_modal').modal({
            show: true,
            backdrop: 'static',
            keyboard: false,
            focus: true
        });
        end_loader();
    }

    // Confirmation Modal
    window._conf = function($msg = '', $func = '', $params = []) {
        $('#confirm_modal #confirm').attr('onclick', $func + "(" + $params.join(',') + ")");
        $('#confirm_modal #delete_content').html($msg);
        $('#confirm_modal').modal('show');
    }

    // ========== ROOM MANAGEMENT ==========
    let allRoomsData = [];
    let currentBuilding = '';

    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function() {
        loadRooms();

        // Setup building selector
        document.getElementById('buildingSelect').addEventListener('change', function() {
            currentBuilding = this.value;
            renderRoomsByFloor();
        });

        // Setup search
        document.getElementById('searchRoom').addEventListener('input', renderRoomsByFloor);

        // Setup filter
        document.getElementById('filterStatus').addEventListener('change', renderRoomsByFloor);
    });

    // Load rooms from database
    function loadRooms() {
        $.ajax({
            url: '../../backend/controller/RoomManagementController.php?action=get-all-rooms',
            method: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    allRoomsData = response.data;
                    populateBuildingSelector();
                    renderRoomsByFloor();
                } else {
                    alert('Lỗi: ' + response.message);
                }
            },
            error: function(xhr, status, error) {
                console.error('Error loading rooms:', error);
                alert('Lỗi tải dữ liệu phòng');
            }
        });
    }

    // Populate building selector
    function populateBuildingSelector() {
        const buildings = [...new Set(allRoomsData.map(r => r.building))].sort();
        const select = document.getElementById('buildingSelect');

        select.innerHTML = '<option value="">-- Chọn tòa nhà --</option>' +
            buildings.map(b => `<option value="${b}">Tòa ${b}</option>`).join('');

        // Auto select first building
        if (buildings.length > 0 && !currentBuilding) {
            currentBuilding = buildings[0];
            select.value = currentBuilding;
        }
    }

    // Render rooms grouped by floor
    function renderRoomsByFloor() {
        const roomList = document.getElementById('roomList');

        if (!currentBuilding) {
            roomList.innerHTML =
                '<div class="alert alert-info"><i class="fas fa-info-circle"></i> Vui lòng chọn tòa nhà để xem danh sách phòng</div>';
            return;
        }

        // Filter rooms
        const searchTerm = document.getElementById('searchRoom').value.toLowerCase();
        const statusFilter = document.getElementById('filterStatus').value;

        let filtered = allRoomsData.filter(room => {
            const matchBuilding = room.building === currentBuilding;
            const matchSearch = searchTerm === '' ||
                room.building.toLowerCase().includes(searchTerm) ||
                room.floor.toString().includes(searchTerm) ||
                room.room_id.toString().includes(searchTerm);

            const matchStatus = statusFilter === '' ||
                (statusFilter === 'Valiable' && room.status === 'Trống') ||
                (statusFilter === 'Unvaliable' && (room.status === 'Đầy' || room.status === 'Bảo trì'));

            return matchBuilding && matchSearch && matchStatus;
        });

        if (filtered.length === 0) {
            roomList.innerHTML =
                '<div class="alert alert-warning"><i class="fas fa-exclamation-triangle"></i> Không tìm thấy phòng nào</div>';
            return;
        }

        // Group by floor
        const roomsByFloor = {};
        filtered.forEach(room => {
            if (!roomsByFloor[room.floor]) {
                roomsByFloor[room.floor] = [];
            }
            roomsByFloor[room.floor].push(room);
        });

        // Sort floors descending (top floor first)
        const floors = Object.keys(roomsByFloor).sort((a, b) => b - a);

        // Render by floor
        let html = '';
        floors.forEach(floor => {
            const rooms = roomsByFloor[floor];
            const totalRooms = rooms.length;
            const availableRooms = rooms.filter(r => r.status === 'Trống').length;

            html += `
                <div class="floor-section">
                    <div class="floor-header">
                        <h4><i class="fas fa-layer-group"></i> Tầng ${floor}</h4>
                        <span class="badge bg-light text-dark">${availableRooms}/${totalRooms} phòng trống</span>
                    </div>
                    <div class="floor-rooms">
                        ${rooms.map(room => renderRoomCard(room)).join('')}
                    </div>
                </div>
            `;
        });

        roomList.innerHTML = html;

        // Make room cards droppable after rendering
        setTimeout(makeRoomCardsDroppable, 100);
        setTimeout(initRoomDropdowns, 100);
    }

    // Initialize dropdown events to avoid layout issues
    function initRoomDropdowns() {
        $('.card-try-2 .dropdown').each(function() {
            const card = $(this).closest('.card-try-2');
            $(this)
                .off('show.bs.dropdown')
                .on('show.bs.dropdown', function() {
                    card.addClass('dropdown-open');
                })
                .off('hide.bs.dropdown')
                .on('hide.bs.dropdown', function() {
                    card.removeClass('dropdown-open');
                });
        });
    }

    // Render single room card
    function renderRoomCard(room) {
        const statusClass = getColorStatus(room.status);
        const roomName = `${room.building}-${room.room_name}`;
        const occupancy = `${room.occupied_beds || 0}/${room.capacity}`;

        return `
            <div style="flex: 0 0 auto; width: 160px;">
                <div class="card swiper-slide card-try-2 shadow-sm border-0 h-100" style="font-size: 0.85rem;">
                    <div class="image-content">
                        <span class="${statusClass}"></span>
                        <div class="card-image">
                            <p class="text-center mt-2 mb-1" style="font-size: 1rem;">
                                <strong>${roomName}</strong>
                            </p>
                        </div>
                    </div>
                    <div class="card-content" style="padding: 8px;">
                        <label class="mb-0 p-1" style="font-size: 0.8rem; line-height: 1.6;">
                            <i class="fas fa-bed"></i> ${occupancy}<br>
                            <i class="fas fa-dollar-sign"></i> ${formatPrice(room.price_per_month)}<br>
                            <i class="fas fa-venus-mars"></i> ${room.gender_restriction}
                        </label>
                    </div>
                    <div class="card-footer bg-white border-0 p-2 text-center" style="padding-bottom: 8px !important;">
                        <div class="dropdown w-100 mb-1">
                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle w-100" type="button" data-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-cogs"></i> Action
                            </button>
                            <div class="dropdown-menu dropdown-menu-right w-100 shadow-sm">
                                <a class="dropdown-item" href="#" onclick="viewRoomDetail(${room.room_id})">
                                    <i class="fas fa-eye text-info"></i> Xem chi tiết
                                </a>
                                <a class="dropdown-item" href="#" onclick="openEditRoomModal(${room.room_id})">
                                    <i class="fas fa-edit text-warning"></i> Sửa thông tin
                                </a>
                                ${room.status !== 'Bảo trì' ? `
                                <a class="dropdown-item" href="#" onclick="confirmMaintenance(${room.room_id})">
                                    <i class="fas fa-tools text-primary"></i> Đưa vào bảo trì
                                </a>` : `
                                <a class="dropdown-item" href="#" onclick="updateRoomStatus(${room.room_id}, 'Trống')">
                                    <i class="fas fa-check text-success"></i> Hoàn tất bảo trì
                                </a>`}
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item text-danger" href="#" onclick="confirmDeleteRoom(${room.room_id})">
                                    <i class="fas fa-trash"></i> Xóa phòng
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
    }

    // Format price
    function formatPrice(price) {
        return new Intl.NumberFormat('vi-VN').format(price) + ' VNĐ';
    }

    // Get color status
    function getColorStatus(status) {
        switch (status) {
            case 'Đầy':
                return 'overlay-unvalid';
            case 'Bảo trì':
                return 'overlay-maintenance';
            case 'Trống':
                return 'overlay-valid';
            default:
                return 'overlay-valid';
        }
    }

    // View room detail - navigate to detail page
    function viewRoomDetail(roomId) {
        window.location.href = 'room-detail.php?room_id=' + roomId;
    }

    // Confirm Maintenance
    function confirmMaintenance(roomId) {
        Swal.fire({
            title: 'Xác nhận đưa phòng vào bảo trì?',
            text: 'Phòng sẽ tạm thời không thể sử dụng cho đến khi hoàn tất bảo trì.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#007bff',
            cancelButtonColor: '#6c757d',
            confirmButtonText: '<i class="fas fa-tools"></i> Đưa vào bảo trì',
            cancelButtonText: '<i class="fas fa-times"></i> Hủy'
        }).then((result) => {
            if (result.isConfirmed) {
                updateRoomStatus(roomId, 'Bảo trì');
            }
        });
    }

    // Update room status
    function updateRoomStatus(roomId, newStatus) {
        start_loader();
        $.ajax({
            url: '../../backend/controller/RoomManagementController.php?action=update-room-status',
            method: 'POST',
            data: JSON.stringify({
                room_id: roomId,
                status: newStatus
            }),
            contentType: 'application/json',
            dataType: 'json',
            success: function(response) {
                end_loader();
                if (response.success) {
                    alert_toast(response.message, 'success');
                    loadRooms(); // Reload rooms
                } else {
                    alert_toast('Lỗi: ' + response.message, 'error');
                }
            },
            error: function(xhr, status, error) {
                end_loader();
                console.error('Error updating status:', error);
                alert_toast('Lỗi cập nhật trạng thái', 'error');
            }
        });
    }

    // ========== STUDENT SEARCH & DRAG DROP ==========

    // Open student search panel
    function openStudentSearchModal() {
        document.getElementById('studentSearchPanel').style.display = 'block';
        document.getElementById('studentSearchInput').focus();
    }

    // Close student search panel
    function closeStudentSearchPanel() {
        document.getElementById('studentSearchPanel').style.display = 'none';
        document.getElementById('studentSearchResults').innerHTML = '';
        document.getElementById('studentSearchInput').value = '';
    }

    // Search students
    function searchStudents() {
        const keyword = document.getElementById('studentSearchInput').value.trim();

        if (keyword.length < 2) {
            alert_toast('Vui lòng nhập ít nhất 2 ký tự', 'warning');
            return;
        }

        start_loader();
        $.ajax({
            url: '../../backend/controller/RoomManagementController.php?action=search-students&keyword=' +
                encodeURIComponent(keyword),
            method: 'GET',
            dataType: 'json',
            success: function(response) {
                end_loader();
                if (response.success) {
                    renderStudentResults(response.data);
                } else {
                    alert_toast('Lỗi: ' + response.message, 'error');
                    document.getElementById('studentSearchResults').innerHTML =
                        '<div class="col-12 text-center text-muted">Không tìm thấy sinh viên</div>';
                }
            },
            error: function(xhr, status, error) {
                end_loader();
                console.error('Error searching students:', error);
                alert_toast('Lỗi tìm kiếm sinh viên', 'error');
            }
        });
    }

    // Render student search results
    function renderStudentResults(students) {
        const resultsDiv = document.getElementById('studentSearchResults');

        if (!students || students.length === 0) {
            resultsDiv.innerHTML = '<div class="col-12 text-center text-muted">Không tìm thấy sinh viên</div>';
            return;
        }

        resultsDiv.innerHTML = students.map(student => `
            <div class="col-md-6 col-lg-4 mb-3">
                <div class="card h-100 student-card" draggable="true" 
                     ondragstart="dragStart(event, ${student.user_id}, '${student.full_name}')">
                    <div class="card-body">
                        <h6 class="card-title">
                            <i class="fas fa-user-graduate text-primary"></i> 
                            ${student.full_name}
                        </h6>
                        <p class="card-text small mb-1">
                            <i class="fas fa-id-card"></i> MSSV: ${student.student_id || 'N/A'}
                        </p>
                        <p class="card-text small mb-1">
                            <i class="fas fa-envelope"></i> ${student.email}
                        </p>
                        <p class="card-text small mb-1">
                            <i class="fas fa-graduation-cap"></i> ${student.faculty || 'N/A'}
                        </p>
                        ${student.current_room ? `
                            <div class="alert alert-info py-1 px-2 mt-2 mb-2">
                                <small><i class="fas fa-door-open"></i> Đang ở: Tòa ${student.current_room}</small>
                            </div>
                        ` : ''}
                        <div class="btn-group btn-group-sm w-100 mt-2" role="group">
                            <button class="btn btn-primary" onclick="showAssignRoomModal(${student.user_id}, '${student.full_name}')">
                                <i class="fas fa-plus"></i> Thêm vào phòng
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `).join('');

        // Make room cards droppable after rendering students
        setTimeout(makeRoomCardsDroppable, 100);
    }

    // Drag start
    let draggedStudentId = null;
    let draggedStudentName = '';

    function dragStart(event, studentId, studentName) {
        draggedStudentId = studentId;
        draggedStudentName = studentName;
        event.dataTransfer.effectAllowed = 'move';
        event.dataTransfer.setData('text/html', event.target.innerHTML);
    }

    // Make room cards droppable
    function makeRoomCardsDroppable() {
        $('.card-try-2').each(function() {
            const card = $(this);
            const roomId = card.find('button[onclick*="viewRoomDetail"]').attr('onclick').match(/\d+/)[0];

            card.on('dragover', function(e) {
                e.preventDefault();
                e.stopPropagation();
                $(this).addClass('border-primary');
            });

            card.on('dragleave', function(e) {
                $(this).removeClass('border-primary');
            });

            card.on('drop', function(e) {
                e.preventDefault();
                e.stopPropagation();
                $(this).removeClass('border-primary');

                if (draggedStudentId) {
                    confirmAssignStudent(draggedStudentId, draggedStudentName, roomId);
                }
            });
        });
    }

    // Show assign room modal
    function showAssignRoomModal(studentId, studentName) {
        Swal.fire({
            title: 'Chọn phòng',
            html: `
                <p>Thêm sinh viên <strong>${studentName}</strong> vào phòng:</p>
                <select id="roomSelect" class="form-control">
                    <option value="">-- Chọn phòng --</option>
                    ${allRoomsData.filter(r => r.status === 'Trống' && r.available_beds > 0).map(r => 
                        `<option value="${r.room_id}">Tòa ${r.building} - Tầng ${r.floor} (Còn ${r.available_beds} chỗ)</option>`
                    ).join('')}
                </select>
                <div class="mt-3">
                    <label>Ngày bắt đầu:</label>
                    <input type="date" id="startDate" class="form-control" value="${new Date().toISOString().split('T')[0]}">
                </div>
                <div class="mt-2">
                    <label>Ngày kết thúc:</label>
                    <input type="date" id="endDate" class="form-control" value="${new Date(new Date().setFullYear(new Date().getFullYear() + 1)).toISOString().split('T')[0]}">
                </div>
            `,
            showCancelButton: true,
            confirmButtonText: 'Thêm',
            cancelButtonText: 'Hủy',
            preConfirm: () => {
                const roomId = document.getElementById('roomSelect').value;
                const startDate = document.getElementById('startDate').value;
                const endDate = document.getElementById('endDate').value;

                if (!roomId) {
                    Swal.showValidationMessage('Vui lòng chọn phòng');
                    return false;
                }

                return {
                    roomId,
                    startDate,
                    endDate
                };
            }
        }).then((result) => {
            if (result.isConfirmed) {
                assignStudentToRoom(studentId, result.value.roomId, result.value.startDate, result.value
                    .endDate);
            }
        });
    }

    // Confirm assign student (from drag & drop)
    function confirmAssignStudent(studentId, studentName, roomId) {
        const room = allRoomsData.find(r => r.room_id == roomId);

        if (!room) {
            alert_toast('Không tìm thấy phòng', 'error');
            return;
        }

        if (room.status !== 'Trống' || room.available_beds <= 0) {
            alert_toast('Phòng không còn chỗ trống', 'warning');
            return;
        }

        Swal.fire({
            title: 'Xác nhận',
            html: `
                <p>Thêm sinh viên <strong>${studentName}</strong> vào phòng <strong>Tòa ${room.building} - Tầng ${room.floor}</strong>?</p>
                <div class="mt-3">
                    <label>Ngày bắt đầu:</label>
                    <input type="date" id="startDate" class="form-control" value="${new Date().toISOString().split('T')[0]}">
                </div>
                <div class="mt-2">
                    <label>Ngày kết thúc:</label>
                    <input type="date" id="endDate" class="form-control" value="${new Date(new Date().setFullYear(new Date().getFullYear() + 1)).toISOString().split('T')[0]}">
                </div>
            `,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Thêm',
            cancelButtonText: 'Hủy',
            preConfirm: () => {
                const startDate = document.getElementById('startDate').value;
                const endDate = document.getElementById('endDate').value;
                return {
                    startDate,
                    endDate
                };
            }
        }).then((result) => {
            if (result.isConfirmed) {
                assignStudentToRoom(studentId, roomId, result.value.startDate, result.value.endDate);
            }
        });
    }

    // Assign student to room
    function assignStudentToRoom(studentId, roomId, startDate, endDate) {
        start_loader();
        $.ajax({
            url: '../../backend/controller/RoomManagementController.php?action=assign-student',
            method: 'POST',
            data: JSON.stringify({
                student_id: studentId,
                room_id: roomId,
                start_date: startDate,
                end_date: endDate
            }),
            contentType: 'application/json',
            dataType: 'json',
            success: function(response) {
                end_loader();
                if (response.success) {
                    alert_toast(response.message, 'success');
                    loadRooms(); // Reload rooms
                    closeStudentSearchPanel(); // Close search panel
                } else {
                    alert_toast('Lỗi: ' + response.message, 'error');
                }
            },
            error: function(xhr, status, error) {
                end_loader();
                console.error('Error assigning student:', error);
                alert_toast('Lỗi thêm sinh viên vào phòng', 'error');
            }
        });
    }

    // Enter key to search
    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('studentSearchInput').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                searchStudents();
            }
        });
    });

    // ========== ROOM CRUD OPERATIONS ==========

    // Open Add Room Modal
    function openAddRoomModal() {
        Swal.fire({
            title: '<i class="fas fa-plus-circle"></i> Thêm Phòng Mới',
            html: `
                <form id="addRoomForm" class="text-left">
                    <div class="form-group">
                        <label>Tòa nhà <span class="text-danger">*</span></label>
                        <select class="form-control" id="add_building" required>
                            <option value=""></option>
                            <option value="A">Toà A</option>
                            <option value="B">Toà B</option>
                            <option value="C">Toà C</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Tầng <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="add_floor" required min="1" placeholder="VD: 1, 2, 3...">
                    </div>
                    <div class="form-group">
                        <label>Tên phòng <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="add_tenPhong" required min="1" ">
                    </div>
                    <div class="form-group">
                        <label>Sức chứa <span class="text-danger">*</span></label>
                        
                        <select class="form-control" id="add_capacity">
                            <option value=""></option>
                            <option value="4">4 người</option>
                            <option value="8">8 người</option>
                        </select>    
                    </div>
                    <div class="form-group">
                        <label>Giới tính</label>
                        <select class="form-control" id="add_gender">
                            <option value="">Không giới hạn</option>
                            <option value="Nam">Nam</option>
                            <option value="Nữ">Nữ</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Giá phòng/tháng (VNĐ) <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="add_price" required min="0" placeholder="VD: 500000">
                    </div>
                    <div class="form-group">
                        <label>Trạng thái</label>
                        <select class="form-control" id="add_status">
                            <option value="Trống">Trống</option>
                            <option value="Bảo trì">Bảo trì</option>
                        </select>
                    </div>
                </form>
            `,
            width: '500px',
            showCancelButton: true,
            confirmButtonText: '<i class="fas fa-save"></i> Lưu',
            cancelButtonText: '<i class="fas fa-times"></i> Hủy',
            confirmButtonColor: '#28a745',
            preConfirm: () => {
                const building = document.getElementById('add_building').value.trim();
                const floor = document.getElementById('add_floor').value;
                const room_name = document.getElementById('add_tenPhong').value;
                const capacity = document.getElementById('add_capacity').value;
                const gender = document.getElementById('add_gender').value;
                const price = document.getElementById('add_price').value;
                const status = document.getElementById('add_status').value;

                if (!building || !floor || !capacity || !price || !room_name) {
                    Swal.showValidationMessage('Vui lòng điền đầy đủ thông tin bắt buộc');
                    return false;
                }

                return {
                    building: building,
                    floor: parseInt(floor),
                    room_name: parseInt(room_name),
                    capacity: parseInt(capacity),
                    gender_restriction: gender || null,
                    price_per_month: parseFloat(price),
                    status: status
                };
            }
        }).then((result) => {
            if (result.isConfirmed) {
                createRoom(result.value);
            }
        });
    }

    // Create Room
    function createRoom(data) {
        start_loader();
        $.ajax({
            url: '../../backend/controller/RoomManagementController.php?action=create-room',
            method: 'POST',
            data: JSON.stringify(data),
            contentType: 'application/json',
            dataType: 'json',
            success: function(response) {
                end_loader();
                if (response.success) {
                    alert_toast('Thêm phòng thành công!', 'success');
                    loadRooms();
                } else {
                    alert_toast('Lỗi: ' + response.message, 'error');
                }
            },
            error: function(xhr, status, error) {
                end_loader();
                console.error('Error creating room:', error);
                alert_toast('Lỗi thêm phòng', 'error');
            }
        });
    }

    // Open Edit Room Modal
    function openEditRoomModal(roomId) {
        start_loader();
        $.ajax({
            url: '../../backend/controller/RoomManagementController.php?action=get-room-by-id&room_id=' +
                roomId,
            method: 'GET',
            dataType: 'json',
            success: function(response) {
                end_loader();
                if (response.success) {
                    showEditRoomForm(response.data);
                } else {
                    alert_toast('Lỗi: ' + response.message, 'error');
                }
            },
            error: function(xhr, status, error) {
                end_loader();
                console.error('Error loading room:', error);
                alert_toast('Lỗi tải thông tin phòng', 'error');
            }
        });
    }

    // Show Edit Room Form
    function showEditRoomForm(room) {
        Swal.fire({
            title: '<i class="fas fa-edit"></i> Sửa Thông Tin Phòng',
            html: `
                <form id="editRoomForm" class="text-left">
                    <input type="hidden" id="edit_room_id" value="${room.room_id}">
                    <div class="form-group">
                        <label>Tòa nhà <span class="text-danger">*</span></label>
                        <select class="form-control" id="edit_building" required>
                            <option value=""></option>
                            <option value="A" ${room.building === 'A' ? 'selected' : '' }>Toà A</option>
                            <option value="B" ${room.building === 'B' ? 'selected' : '' }>Toà B</option>
                            <option value="C" ${room.building === 'C' ? 'selected' : '' }>Toà C</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Tầng <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="edit_floor" value="${room.floor}" required min="1">
                    </div>
                    <div class="form-group">
                        <label>Tên Phòng<span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="edit_tenPhong" value="${room.room_name}" required min="1">
                    </div>
                    <div class="form-group">
                        <label>Sức chứa <span class="text-danger">*</span></label>
                        <select class="form-control" id="edit_capacity">
                            <option value="4" ${room.capacity == 4 ? 'selected' : ''}>4 người</option>
                            <option value="8" ${room.capacity == 8 ? 'selected' : ''}>8 người</option>
                        </select>
                        <small class="text-muted">Hiện có ${room.capacity - room.available_beds} người đang ở</small>
                    </div>
                    <div class="form-group">
                        <label>Giới tính</label>
                        <select class="form-control" id="edit_gender">
                            <option value="" ${!room.gender_restriction ? 'selected' : ''}>Không giới hạn</option>
                            <option value="Nam" ${room.gender_restriction === 'Nam' ? 'selected' : ''}>Nam</option>
                            <option value="Nữ" ${room.gender_restriction === 'Nữ' ? 'selected' : ''}>Nữ</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Giá phòng/tháng (VNĐ) <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="edit_price" value="${room.price_per_month}" required min="0">
                    </div>
                    <div class="form-group">
                        <label>Trạng thái</label>
                        <select class="form-control" id="edit_status">
                            <option value="Trống" ${room.status === 'Trống' ? 'selected' : ''}>Trống</option>
                            <option value="Đầy" ${room.status === 'Đầy' ? 'selected' : ''}>Đầy</option>
                            <option value="Bảo trì" ${room.status === 'Bảo trì' ? 'selected' : ''}>Bảo trì</option>
                        </select>
                    </div>
                </form>
            `,
            width: '500px',
            showCancelButton: true,
            confirmButtonText: '<i class="fas fa-save"></i> Cập nhật',
            cancelButtonText: '<i class="fas fa-times"></i> Hủy',
            confirmButtonColor: '#ffc107',
            preConfirm: () => {
                const roomId = document.getElementById('edit_room_id').value;
                const building = document.getElementById('edit_building').value.trim();
                const floor = document.getElementById('edit_floor').value;
                const room_name = document.getElementById('edit_tenPhong').value;
                const capacity = document.getElementById('edit_capacity').value;
                const gender = document.getElementById('edit_gender').value;
                const price = document.getElementById('edit_price').value;
                const status = document.getElementById('edit_status').value;

                if (!building || !floor || !capacity || !price || !room_name) {
                    Swal.showValidationMessage('Vui lòng điền đầy đủ thông tin bắt buộc');
                    return false;
                }

                return {
                    room_id: parseInt(roomId),
                    building: building,
                    floor: parseInt(floor),
                    room_name: parseInt(room_name),
                    capacity: parseInt(capacity),
                    gender_restriction: gender || null,
                    price_per_month: parseFloat(price),
                    status: status
                };
            }
        }).then((result) => {
            if (result.isConfirmed) {
                updateRoom(result.value);
            }
        });
    }

    // Update Room
    function updateRoom(data) {
        start_loader();
        $.ajax({
            url: '../../backend/controller/RoomManagementController.php?action=update-room',
            method: 'POST',
            data: JSON.stringify(data),
            contentType: 'application/json',
            dataType: 'json',
            success: function(response) {
                end_loader();
                if (response.success) {
                    alert_toast('Cập nhật phòng thành công!', 'success');
                    loadRooms();
                } else {
                    alert_toast('Lỗi: ' + response.message, 'error');
                }
            },
            error: function(xhr, status, error) {
                end_loader();
                console.error('Error updating room:', error);
                alert_toast('Lỗi cập nhật phòng', 'error');
            }
        });
    }

    // Confirm Delete Room
    function confirmDeleteRoom(roomId) {
        Swal.fire({
            title: 'Xác nhận xóa phòng?',
            text: 'Bạn có chắc chắn muốn xóa phòng này? Hành động này không thể hoàn tác!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: '<i class="fas fa-trash"></i> Xóa',
            cancelButtonText: '<i class="fas fa-times"></i> Hủy'
        }).then((result) => {
            if (result.isConfirmed) {
                deleteRoom(roomId);
            }
        });
    }

    // Delete Room
    function deleteRoom(roomId) {
        start_loader();
        $.ajax({
            url: '../../backend/controller/RoomManagementController.php?action=delete-room',
            method: 'POST',
            data: JSON.stringify({
                room_id: roomId
            }),
            contentType: 'application/json',
            dataType: 'json',
            success: function(response) {
                end_loader();
                if (response.success) {
                    alert_toast('Xóa phòng thành công!', 'success');
                    loadRooms();
                } else {
                    alert_toast('Lỗi: ' + response.message, 'error');
                }
            },
            error: function(xhr, status, error) {
                end_loader();
                console.error('Error deleting room:', error);
                alert_toast('Lỗi xóa phòng', 'error');
            }
        });
    }
    </script>

</body>

</html>