<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hotelier - Yêu Cầu Sửa Chữa</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="https://luxcity.com/LuxCity/img/shared//favicon.png">
    
    <!-- Google Web Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Heebo:wght@400;500;600;700&family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Icon Font Stylesheet -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">
    
    <!-- Bootstrap CSS -->
    <link href="/frontend/frontend-html/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Template Stylesheet -->
    <link href="/frontend/frontend-html/css/style.css" rel="stylesheet">
    
    <style>
        .repair-card {
            border: 1px solid #ddd;
            border-radius: 8px;
            transition: all 0.3s;
        }
        .repair-card:hover {
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            transform: translateY(-2px);
        }
        .status-badge {
            padding: 0.35rem 0.75rem;
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
        }
        .image-preview {
            max-width: 100%;
            max-height: 200px;
            margin-top: 10px;
            border-radius: 8px;
        }
        .table td {
            vertical-align: middle;
        }
        .img-thumbnail {
            border: 1px solid #dee2e6;
            border-radius: 4px;
            padding: 2px;
            transition: transform 0.2s;
        }
        .img-thumbnail:hover {
            transform: scale(1.1);
            box-shadow: 0 2px 8px rgba(0,0,0,0.2);
        }
    </style>
</head>
<body>
    <?php include('include/headd.php'); ?>

    <!-- Page Header -->
    <div class="container-fluid page-header mb-5 p-0" style="background-image: url('/frontend/frontend-html/images/anhKTX1.png'); background-size: cover; background-position: center;">
        <div class="container-fluid page-header-inner py-5">
            <div class="container text-center pb-5">
                <h1 class="display-3 text-white mb-3 animated slideInDown">Yêu Cầu Sửa Chữa</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb justify-content-center text-uppercase">
                        <li class="breadcrumb-item"><a href="/frontend/trangchu">Trang Chủ</a></li>
                        <li class="breadcrumb-item text-white active" aria-current="page">Yêu Cầu Sửa Chữa</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <!-- Create Repair Request Button -->
    <div class="container mb-4">
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createRepairModal">
            <i class="fas fa-plus me-2"></i>Tạo Yêu Cầu Sửa Chữa
        </button>
    </div>

    <!-- Repair Requests -->
    <div class="container-xxl py-5">
        <div class="container">
            <div class="text-center wow fadeInUp" data-wow-delay="0.1s">
                <h6 class="section-title text-center text-primary text-uppercase">Quản Lý</h6>
                <h1 class="mb-5">Yêu Cầu <span class="text-primary text-uppercase">Sửa Chữa</span> Của Bạn</h1>
            </div>

            <!-- Tabs -->
            <ul class="nav nav-tabs mb-4" id="repairTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="all-tab" data-bs-toggle="tab" data-bs-target="#all" type="button" role="tab">
                        Tất Cả
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="pending-tab" data-bs-toggle="tab" data-bs-target="#pending" type="button" role="tab">
                        Chờ Xử Lý
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="in-progress-tab" data-bs-toggle="tab" data-bs-target="#in-progress" type="button" role="tab">
                        Đang Sửa
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="completed-tab" data-bs-toggle="tab" data-bs-target="#completed" type="button" role="tab">
                        Hoàn Thành
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="rejected-tab" data-bs-toggle="tab" data-bs-target="#rejected" type="button" role="tab">
                        Từ Chối
                    </button>
                </li>
            </ul>

            <!-- Loading -->
            <div id="loadingSpinner" class="text-center my-5">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Đang tải...</span>
                </div>
            </div>

            <!-- Tab Content -->
            <div class="tab-content" id="repairTabContent">
                <div class="tab-pane fade show active" id="all" role="tabpanel">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped table-bordered" id="allRequestsTable">
                            <thead class="table-primary">
                                <tr>
                                    <th>#</th>
                                    <th>Phòng</th>
                                    <th>Mô Tả</th>
                                    <th>Hình Ảnh</th>
                                    <th>Mức Độ</th>
                                    <th>Trạng Thái</th>
                                    <th>Ngày Báo Cáo</th>
                                    <th>Hành Động</th>
                                </tr>
                            </thead>
                            <tbody id="allRequests"></tbody>
                        </table>
                    </div>
                </div>
                <div class="tab-pane fade" id="pending" role="tabpanel">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped table-bordered">
                            <thead class="table-warning">
                                <tr>
                                    <th>#</th>
                                    <th>Phòng</th>
                                    <th>Mô Tả</th>
                                    <th>Hình Ảnh</th>
                                    <th>Mức Độ</th>
                                    <th>Ngày Báo Cáo</th>
                                    <th>Hành Động</th>
                                </tr>
                            </thead>
                            <tbody id="pendingRequests"></tbody>
                        </table>
                    </div>
                </div>
                <div class="tab-pane fade" id="in-progress" role="tabpanel">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped table-bordered">
                            <thead class="table-info">
                                <tr>
                                    <th>#</th>
                                    <th>Phòng</th>
                                    <th>Mô Tả</th>
                                    <th>Hình Ảnh</th>
                                    <th>Người Xử Lý</th>
                                    <th>Ngày Tiếp Nhận</th>
                                    <th>Hành Động</th>
                                </tr>
                            </thead>
                            <tbody id="inProgressRequests"></tbody>
                        </table>
                    </div>
                </div>
                <div class="tab-pane fade" id="completed" role="tabpanel">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped table-bordered">
                            <thead class="table-success">
                                <tr>
                                    <th>#</th>
                                    <th>Phòng</th>
                                    <th>Mô Tả</th>
                                    <th>Hình Ảnh</th>
                                    <th>Người Xử Lý</th>
                                    <th>Chi Phí</th>
                                    <th>Ngày Hoàn Thành</th>
                                    <th>Hành Động</th>
                                </tr>
                            </thead>
                            <tbody id="completedRequests"></tbody>
                        </table>
                    </div>
                </div>
                <div class="tab-pane fade" id="rejected" role="tabpanel">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped table-bordered">
                            <thead class="table-danger">
                                <tr>
                                    <th>#</th>
                                    <th>Phòng</th>
                                    <th>Mô Tả</th>
                                    <th>Hình Ảnh</th>
                                    <th>Ngày Báo Cáo</th>
                                    <th>Hành Động</th>
                                </tr>
                            </thead>
                            <tbody id="rejectedRequests"></tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- No Requests Message -->
            <div id="noRequests" class="text-center my-5" style="display: none;">
                <i class="fas fa-tools fa-5x text-muted mb-4"></i>
                <h3>Bạn chưa có yêu cầu sửa chữa nào</h3>
                <p class="text-muted">Hãy báo cáo sự cố trong phòng của bạn để được hỗ trợ kịp thời!</p>
                <button class="btn btn-primary mt-3" data-bs-toggle="modal" data-bs-target="#createRepairModal">
                    Tạo Yêu Cầu Ngay
                </button>
            </div>
        </div>
    </div>

    <!-- Create Repair Request Modal -->
    <div class="modal fade" id="createRepairModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tạo Yêu Cầu Sửa Chữa</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="createRepairForm">
                        <div class="mb-3">
                            <label for="roomInfo" class="form-label">Phòng Của Bạn</label>
                            <input type="text" class="form-control" id="roomInfo" readonly>
                            <input type="hidden" id="roomId">
                            <small class="text-muted">Thông tin phòng được lấy tự động từ hệ thống</small>
                        </div>
                        
                        <div class="mb-3">
                            <label for="priority" class="form-label">Mức Độ Ưu Tiên <span class="text-danger">*</span></label>
                            <select class="form-select" id="priority" required>
                                <option value="Thường">Thường</option>
                                <option value="Khẩn cấp">Khẩn Cấp</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="description" class="form-label">Mô Tả Sự Cố <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="description" rows="4" required 
                                placeholder="Vui lòng mô tả chi tiết sự cố (ví dụ: mất điện, rò rỉ nước, hư khóa cửa, hỏng thiết bị...)"></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label for="imageUpload" class="form-label">Hình Ảnh (Tùy chọn)</label>
                            <input type="file" class="form-control" id="imageUpload" accept="image/*">
                            <small class="text-muted">Đính kèm hình ảnh sự cố để xử lý nhanh hơn (tối đa 5MB)</small>
                            <div id="imagePreview"></div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="button" class="btn btn-primary" id="submitRepairBtn">
                        <i class="fas fa-paper-plane me-2"></i>Gửi Yêu Cầu
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- View Detail Modal -->
    <div class="modal fade" id="detailModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Chi Tiết Yêu Cầu Sửa Chữa</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="detailContent">
                    <!-- Detail will be loaded here -->
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <?php include('include/footer.php');  ?>

    <!-- Back to Top -->
    <a href="#" class="btn btn-lg btn-primary btn-lg-square back-to-top"><i class="bi bi-arrow-up"></i></a>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../lib/wow/wow.min.js"></script>
    <script src="/frontend/frontend-html/js/main.js"></script>
    <script src="/frontend/frontend-html/js/auth.js"></script>
    
    <script>
        // Khai báo biến local cho trang này
        let repairCurrentUser = null;
        let allRepairRequests = [];
        let uploadedImageUrl = '';

        // Initialize the page
        document.addEventListener('DOMContentLoaded', async function() {
            loadUserSession();
            try {
                // Load user session
                await loadUserSessionAsync();
                console.log('Repair request page loaded', repairCurrentUser);
                
                if (!repairCurrentUser || !repairCurrentUser.profile || !repairCurrentUser.profile.user_id) {
                    showToast('Vui lòng đăng nhập để sử dụng chức năng này', 'warning');
                    document.getElementById('loadingSpinner').style.display = 'none';
                    setTimeout(() => {
                        window.location.href = '../../LoginDarkSunSet/login.php';
                    }, 2000);
                    return;
                }
                
                await loadStudentRooms();
                await loadRepairRequests();
                
                // Handle image upload
                document.getElementById('imageUpload').addEventListener('change', handleImageUpload);
                
                // Handle form submission
                document.getElementById('submitRepairBtn').addEventListener('click', handleSubmitRepair);
            } catch (error) {
                console.error('Initialization error:', error);
                document.getElementById('loadingSpinner').style.display = 'none';
                showToast('Lỗi khi tải trang: ' + error.message, 'danger');
            }
        });
        
        // Load user session async
        async function loadUserSessionAsync() {
            try {
                const response = await fetch('../../../backend/auth.php?action=check-status', {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    credentials: 'include'
                });
                
                const result = await response.json();
                console.log('Session check result:', result);
                
                if (result.is_logged_in && result.user) {
                    repairCurrentUser = {
                        profile: {
                            user_id: result.user.user_id,
                            fullName: result.user.full_name || result.user.username,
                            username: result.user.username,
                            email: result.user.email,
                            role: result.user.role
                        }
                    };
                } else {
                    repairCurrentUser = null;
                }
            } catch (error) {
                console.error('Error loading user session:', error);
                repairCurrentUser = null;
                throw error;
            }
        }

        // Load student's rooms (Tự động lấy phòng)
        async function loadStudentRooms() {
            try {
                const studentId = repairCurrentUser.profile.user_id;
                const response = await fetch(`../../../backend/repair.php?action=get-student-rooms&student_id=${studentId}`);
                const data = await response.json();
                
                if (data.success && data.rooms.length > 0) {
                    // Tự động chọn phòng đầu tiên
                    const room = data.rooms[0];
                    document.getElementById('roomInfo').value = room.room_name;
                    document.getElementById('roomId').value = room.room_id;
                } else {
                    document.getElementById('roomInfo').value = 'Bạn chưa được phân phòng';
                    document.getElementById('submitRepairBtn').disabled = true;
                }
            } catch (error) {
                console.error('Error loading rooms:', error);
                showToast('Lỗi khi tải thông tin phòng', 'danger');
            }
        }

        // Load repair requests
        async function loadRepairRequests() {
            try {
                document.getElementById('loadingSpinner').style.display = 'block';
                
                const studentId = repairCurrentUser.profile.user_id;
                const response = await fetch(`../../../backend/repair.php?action=get-student-requests&student_id=${studentId}`);
                const data = await response.json();
                
                if (data.success) {
                    allRepairRequests = data.requests;
                    displayRepairRequests();
                } else {
                    showToast(data.message || 'Lỗi khi tải danh sách yêu cầu', 'danger');
                }
            } catch (error) {
                console.error('Error loading repair requests:', error);
                showToast('Lỗi khi tải danh sách yêu cầu', 'danger');
            } finally {
                document.getElementById('loadingSpinner').style.display = 'none';
            }
        }

        // Display repair requests (Table format)
        function displayRepairRequests() {
            if (allRepairRequests.length === 0) {
                document.getElementById('noRequests').style.display = 'block';
                document.getElementById('repairTabContent').style.display = 'none';
                return;
            }
            
            document.getElementById('noRequests').style.display = 'none';
            document.getElementById('repairTabContent').style.display = 'block';
            
            // Render all requests
            renderTable('allRequests', allRepairRequests, 'all');
            
            // Render by status
            renderTable('pendingRequests', allRepairRequests.filter(r => r.status === 'Chờ xử lý'), 'pending');
            renderTable('inProgressRequests', allRepairRequests.filter(r => r.status === 'Đang sửa'), 'in-progress');
            renderTable('completedRequests', allRepairRequests.filter(r => r.status === 'Hoàn thành'), 'completed');
            renderTable('rejectedRequests', allRepairRequests.filter(r => r.status === 'Từ chối'), 'rejected');
        }

        // Render table
        function renderTable(containerId, requests, type) {
            const tbody = document.getElementById(containerId);
            tbody.innerHTML = '';
            
            const columnCounts = {
                'all': 8,
                'pending': 7,
                'in-progress': 7,
                'completed': 8,
                'rejected': 6
            };
            const colspan = columnCounts[type] || 7;
            
            if (requests.length === 0) {
                tbody.innerHTML = `<tr><td colspan="${colspan}" class="text-center text-muted">Không có yêu cầu nào</td></tr>`;
                return;
            }
            
            requests.forEach((request, index) => {
                const row = document.createElement('tr');
                row.style.cursor = 'pointer';
                row.onclick = () => viewRepairDetail(request.repair_id);
                
                const statusClass = {
                    'Chờ xử lý': 'status-pending',
                    'Đang sửa': 'status-in-progress',
                    'Hoàn thành': 'status-completed',
                    'Từ chối': 'status-rejected'
                }[request.status] || 'status-pending';
                
                const priorityClass = request.priority === 'Khẩn cấp' ? 'priority-urgent' : 'priority-normal';
                
                const imageContent = request.image_url
                    ? `<img src="../../../${request.image_url}" class="img-thumbnail" style="max-height:60px; max-width:80px; object-fit:cover; cursor:pointer;" alt="Hình ảnh" onerror="this.onerror=null; this.src='data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'80\' height=\'60\'%3E%3Crect fill=\'%23ddd\' width=\'80\' height=\'60\'/%3E%3Ctext x=\'50%25\' y=\'50%25\' dominant-baseline=\'middle\' text-anchor=\'middle\' fill=\'%23999\' font-size=\'10\'%3ENo Image%3C/text%3E%3C/svg%3E';" onclick="event.stopPropagation(); window.open('../../../${request.image_url}', '_blank')" title="Click để xem ảnh lớn">`
                    : '<span class="text-muted">Không có</span>';
                
                if (type === 'all') {
                    row.innerHTML = `
                        <td class="text-center">${index + 1}</td>
                        <td>${request.room_name}</td>
                        <td>${truncateText(request.description, 50)}</td>
                        <td>${imageContent}</td>
                        <td><span class="priority-badge ${priorityClass}">${request.priority}</span></td>
                        <td><span class="status-badge ${statusClass}">${request.status}</span></td>
                        <td>${formatDate(request.report_date)}</td>
                        <td class="text-center">
                            <button class="btn btn-sm btn-info" onclick="event.stopPropagation(); viewRepairDetail(${request.repair_id})">
                                <i class="fas fa-eye"></i> Xem
                            </button>
                        </td>
                    `;
                } else if (type === 'pending') {
                    row.innerHTML = `
                        <td class="text-center">${index + 1}</td>
                        <td>${request.room_name}</td>
                        <td>${truncateText(request.description, 50)}</td>
                        <td>${imageContent}</td>
                        <td><span class="priority-badge ${priorityClass}">${request.priority}</span></td>
                        <td>${formatDate(request.report_date)}</td>
                        <td class="text-center">
                            <button class="btn btn-sm btn-info" onclick="event.stopPropagation(); viewRepairDetail(${request.repair_id})">
                                <i class="fas fa-eye"></i> Xem
                            </button>
                        </td>
                    `;
                } else if (type === 'in-progress') {
                    row.innerHTML = `
                        <td class="text-center">${index + 1}</td>
                        <td>${request.room_name}</td>
                        <td>${truncateText(request.description, 50)}</td>
                        <td>${imageContent}</td>
                        <td>${request.staff_name || 'Chưa có'}</td>
                        <td>${request.received_date ? formatDate(request.received_date) : 'N/A'}</td>
                        <td class="text-center">
                            <button class="btn btn-sm btn-info" onclick="event.stopPropagation(); viewRepairDetail(${request.repair_id})">
                                <i class="fas fa-eye"></i> Xem
                            </button>
                        </td>
                    `;
                } else if (type === 'completed') {
                    row.innerHTML = `
                        <td class="text-center">${index + 1}</td>
                        <td>${request.room_name}</td>
                        <td>${truncateText(request.description, 50)}</td>
                        <td>${imageContent}</td>
                        <td>${request.staff_name || 'N/A'}</td>
                        <td>${request.actual_cost ? formatMoney(request.actual_cost) + ' VNĐ' : 'Chưa có'}</td>
                        <td>${formatDate(request.report_date)}</td>
                        <td class="text-center">
                            <button class="btn btn-sm btn-info" onclick="event.stopPropagation(); viewRepairDetail(${request.repair_id})">
                                <i class="fas fa-eye"></i> Xem
                            </button>
                        </td>
                    `;
                } else if (type === 'rejected') {
                    row.innerHTML = `
                        <td class="text-center">${index + 1}</td>
                        <td>${request.room_name}</td>
                        <td>${truncateText(request.description, 50)}</td>
                        <td>${imageContent}</td>
                        <td>${formatDate(request.report_date)}</td>
                        <td class="text-center">
                            <button class="btn btn-sm btn-info" onclick="event.stopPropagation(); viewRepairDetail(${request.repair_id})">
                                <i class="fas fa-eye"></i> Xem
                            </button>
                        </td>
                    `;
                }
                
                tbody.appendChild(row);
            });
        }

        // View repair detail
        async function viewRepairDetail(repairId) {
            try {
                const response = await fetch(`../../../backend/repair.php?action=get-request-details&repair_id=${repairId}`);
                const data = await response.json();
                
                if (data.success) {
                    const request = data.request;
                    const statusClass = {
                        'Chờ xử lý': 'status-pending',
                        'Đang sửa': 'status-in-progress',
                        'Hoàn thành': 'status-completed',
                        'Từ chối': 'status-rejected'
                    }[request.status] || 'status-pending';
                    
                    document.getElementById('detailContent').innerHTML = `
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="fw-bold">Phòng:</label>
                                <p>${request.room_name}</p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="fw-bold">Trạng thái:</label>
                                <p><span class="status-badge ${statusClass}">${request.status}</span></p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="fw-bold">Mức độ:</label>
                                <p><span class="priority-badge ${request.priority === 'Khẩn cấp' ? 'priority-urgent' : 'priority-normal'}">${request.priority}</span></p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="fw-bold">Ngày báo cáo:</label>
                                <p>${formatDateTime(request.report_date)}</p>
                            </div>
                            <div class="col-12 mb-3">
                                <label class="fw-bold">Mô tả sự cố:</label>
                                <p>${request.description}</p>
                            </div>
                            ${request.image_url ? `
                            <div class="col-12 mb-3">
                                <label class="fw-bold">Hình ảnh:</label>
                                <br>
                                <img src="../../../backend${request.image_url}" class="img-fluid" alt="Hình ảnh sự cố" style="max-height: 400px;">
                            </div>
                            ` : ''}
                            ${request.staff_name ? `
                            <div class="col-md-6 mb-3">
                                <label class="fw-bold">Người xử lý:</label>
                                <p>${request.staff_name}</p>
                            </div>
                            ` : ''}
                            ${request.estimated_cost ? `
                            <div class="col-md-6 mb-3">
                                <label class="fw-bold">Chi phí ước tính:</label>
                                <p>${formatMoney(request.estimated_cost)} VNĐ</p>
                            </div>
                            ` : ''}
                            ${request.actual_cost ? `
                            <div class="col-md-6 mb-3">
                                <label class="fw-bold">Chi phí thực tế:</label>
                                <p>${formatMoney(request.actual_cost)} VNĐ</p>
                            </div>
                            ` : ''}
                        </div>
                    `;
                    
                    new bootstrap.Modal(document.getElementById('detailModal')).show();
                } else {
                    showToast(data.message || 'Lỗi khi tải chi tiết', 'danger');
                }
            } catch (error) {
                console.error('Error loading detail:', error);
                showToast('Lỗi khi tải chi tiết yêu cầu', 'danger');
            }
        }

        // Handle image upload
        async function handleImageUpload(event) {
            const file = event.target.files[0];
            if (!file) return;
            
            // Validate file size
            if (file.size > 5 * 1024 * 1024) {
                showToast('Kích thước file không được vượt quá 5MB', 'warning');
                event.target.value = '';
                return;
            }
            
            // Show preview
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('imagePreview').innerHTML = `
                    <img src="${e.target.result}" class="image-preview" alt="Preview">
                `;
            };
            reader.readAsDataURL(file);
            
            // Upload to server
            const formData = new FormData();
            formData.append('image', file);
            
            try {
                const response = await fetch('../../../backend/repair.php?action=upload-image', {
                    method: 'POST',
                    body: formData
                });
                
                const data = await response.json();
                
                if (data.success) {
                    uploadedImageUrl = data.image_url;
                    showToast('Tải ảnh lên thành công', 'success');
                } else {
                    showToast(data.message || 'Lỗi khi tải ảnh lên', 'danger');
                    uploadedImageUrl = '';
                }
            } catch (error) {
                console.error('Error uploading image:', error);
                showToast('Lỗi khi tải ảnh lên', 'danger');
                uploadedImageUrl = '';
            }
        }

        // Handle submit repair
        async function handleSubmitRepair() {
            const roomId = document.getElementById('roomId').value;
            const priority = document.getElementById('priority').value;
            const description = document.getElementById('description').value.trim();
            
            if (!roomId || !description) {
                showToast('Vui lòng điền đầy đủ thông tin', 'warning');
                return;
            }
            
            const submitBtn = document.getElementById('submitRepairBtn');
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Đang gửi...';
            
            try {
                const response = await fetch('../../../backend/repair.php?action=create-repair-request', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        student_id: repairCurrentUser.profile.user_id,
                        room_id: parseInt(roomId),
                        description: description,
                        image_url: uploadedImageUrl,
                        priority: priority
                    })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    showToast('Đã gửi yêu cầu sửa chữa thành công!', 'success');
                    bootstrap.Modal.getInstance(document.getElementById('createRepairModal')).hide();
                    
                    // Reset form
                    document.getElementById('createRepairForm').reset();
                    document.getElementById('imagePreview').innerHTML = '';
                    uploadedImageUrl = '';
                    
                    // Reload requests
                    await loadRepairRequests();
                } else {
                    showToast(data.message || 'Lỗi khi gửi yêu cầu', 'danger');
                }
            } catch (error) {
                console.error('Error submitting repair:', error);
                showToast('Lỗi khi gửi yêu cầu sửa chữa', 'danger');
            } finally {
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="fas fa-paper-plane me-2"></i>Gửi Yêu Cầu';
            }
        }

        // Utility functions
        function formatDate(dateString) {
            const date = new Date(dateString);
            return date.toLocaleDateString('vi-VN');
        }

        function formatDateTime(dateString) {
            const date = new Date(dateString);
            return date.toLocaleDateString('vi-VN') + ' ' + date.toLocaleTimeString('vi-VN');
        }

        function formatMoney(amount) {
            return new Intl.NumberFormat('vi-VN').format(amount);
        }

        function truncateText(text, maxLength) {
            if (text.length <= maxLength) return text;
            return text.substring(0, maxLength) + '...';
        }

        function showToast(message, type = 'info') {
            const toast = document.createElement('div');
            toast.className = `alert alert-${type} position-fixed top-0 end-0 m-3`;
            toast.style.zIndex = '9999';
            toast.textContent = message;
            
            document.body.appendChild(toast);
            
            setTimeout(() => {
                toast.remove();
            }, 3000);
        }
    </script>
</body>
</html>
