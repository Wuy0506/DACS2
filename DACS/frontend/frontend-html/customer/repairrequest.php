<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hotelier - Đơn Đăng Ký Phòng</title>
    
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
    <link href="../css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Template Stylesheet -->
    <link href="../css/style.css" rel="stylesheet">
</head>
<body>
    <?php include('include/headd.php'); ?>

    <!-- Page Header -->
    <div class="container-fluid page-header mb-5 p-0" style="background-image: url('../images/carousel-1.jpg'); background-size: cover; background-position: center;">
        <div class="container-fluid page-header-inner py-5">
            <div class="container text-center pb-5">
                <h1 class="display-3 text-white mb-3 animated slideInDown">Đơn Đặt Phòng</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb justify-content-center text-uppercase">
                        <li class="breadcrumb-item"><a href="/trangchu">Trang Chủ</a></li>
                        <li class="breadcrumb-item text-white active" aria-current="page">Đơn Đặt Phòng</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <!-- Bookings -->
    <div class="container-xxl py-5">
        <div class="container">
            <div class="text-center wow fadeInUp" data-wow-delay="0.1s">
                <h6 class="section-title text-center text-primary text-uppercase">Quản Lý</h6>
                <h1 class="mb-5">Đơn <span class="text-primary text-uppercase">Đăng Ký Phòng</span> Của Bạn</h1>
            </div>

          

            <!-- Tabs -->
            <ul class="nav nav-tabs mb-4" id="bookingTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="all-tab" data-bs-toggle="tab" data-bs-target="#all" type="button" role="tab">
                        Tất Cả
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="pending-tab" data-bs-toggle="tab" data-bs-target="#pending" type="button" role="tab">
Chờ Duyệt
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="approved-tab" data-bs-toggle="tab" data-bs-target="#approved" type="button" role="tab">
                        Đã Duyệt
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
            <div class="tab-content" id="bookingTabContent">
                <div class="tab-pane fade show active" id="all" role="tabpanel">
                    <div id="allRegistrations" class="row g-4">
                        <!-- Registrations will be loaded here -->
                    </div>
                </div>
                <div class="tab-pane fade" id="pending" role="tabpanel">
                    <div id="pendingRegistrations" class="row g-4">
                        <!-- Registrations will be loaded here -->
                    </div>
                </div>
                <div class="tab-pane fade" id="approved" role="tabpanel">
                    <div id="approvedRegistrations" class="row g-4">
                        <!-- Registrations will be loaded here -->
                    </div>
                </div>
                <div class="tab-pane fade" id="rejected" role="tabpanel">
                    <div id="rejectedRegistrations" class="row g-4">
                        <!-- Registrations will be loaded here -->
                    </div>
                </div>
            </div>

            <!-- No Registrations Message -->
            <div id="noRegistrations" class="text-center my-5" style="display: none;">
                <i class="fas fa-calendar-times fa-5x text-muted mb-4"></i>
                <h3>Bạn chưa có đơn đăng ký phòng nào</h3>
                <p class="text-muted">Hãy khám phá và đăng ký phòng ngay hôm nay!</p>
                <a href="/backend/auth.php" class="btn btn-primary mt-3">Đăng Ký Phòng Ngay</a>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <div class="container-fluid bg-dark text-light wow fadeIn index-footer-container-fluid" data-wow-delay="0.1s">
        <div class="container">
            <div class="row g-5">
                <div class="col-lg-6 ft-label">
                    <label for="text">Liên Hệ</label>
                    <h1 class="d-block">
<i class="fa-solid fa-location-dot mx-2 text-primary"></i>
                        Lô E2a-7, Đường D1, Đ. D1, Long Thạnh Mỹ, Quận 9, TP.HCM
                    </h1>
                    <h1 class="d-block">
                        <i class="fa-solid fa-envelope mx-2 text-primary"></i>
                        teamSWP@fpt.edu.vn
                    </h1>
                    <h1 class="d-block">
                        <i class="fa-solid fa-phone text-primary mx-2"></i>
                        (+84) 564 565 562 - (+84) 952 482 920
                    </h1>
                </div>
                <div class="col-sm-12 col-lg-6 text-center">
                    <div class="ps-2 pt-4">
                        <small class="fa fa-star"></small>
                        <small class="fa fa-star"></small>
                        <small class="fa fa-star"></small>
                        <small class="fa fa-star"></small>
                        <small class="fa fa-star"></small>
                    </div>
                    <label for="text" class="fs-1 text-uppercase">Hotelier</label>
                    <label for="text" class="d-block text-uppercase">Hotel & Luxury</label>
                </div>
            </div>
        </div>
    </div>

    <!-- Back to Top -->
    <a href="#" class="btn btn-lg btn-primary btn-lg-square back-to-top"><i class="bi bi-arrow-up"></i></a>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../js/main.js"></script>
    
    <!-- Custom Scripts -->
    <script src="../js/api.js"></script>
    <script src="../js/auth.js"></script>
    <script src="../js/app.js"></script>
    
    <script>
        let allRegistrationsData = [];

        // Initialize the page
        document.addEventListener('DOMContentLoaded', async function() {
            loadUserSession();
            console.log('Purchase page loaded');
            
            // Load user session first
            await loadUserSessionAsync();
            
            console.log('Current user after load:', currentUser);
            
            // Check if user is logged in
            if (!currentUser || !currentUser.profile) {
                console.log('User not logged in, redirecting...');
                alert('Vui lòng đăng nhập để xem đơn đăng ký phòng');
                window.location.href = '../../LoginDarkSunSet/login.php';
                return;
            }
            
            console.log('User logged in, loading data...');
            // Load registrations
            await loadRegistrations();
        });
        
        // load session
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
                    currentUser = {
                        profile: {
                            user_id: result.user.user_id,
                            fullName: result.user.full_name || result.user.username,
                            username: result.user.username,
                            email: result.user.email,
                            role: result.user.role
                        }
                    };
                } else {
                    currentUser = null;
                }
            } catch (error) {
                console.error('Error loading user session:', error);
                currentUser = null;
            }
        }
        
        // Load danh sách đăng ký
        async function loadRegistrations() {
            try {
                showLoading();
                
                const response = await fetch('../../../backend/controller/student/CustomerRegistrationController.php?action=get-my-registrations', {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    credentials: 'include' // Quan trọng: gửi cookie session
                });
                
                const result = await response.json();
                hideLoading();
                
                if (!result.success) {
                    // Nếu cần redirect (chưa đăng nhập)
                    if (result.redirect) {
                        alert(result.message);
                        window.location.href = '../../LoginDarkSunSet/login.php';
                        return;
                    }
                    throw new Error(result.message);
                }
                
                allRegistrationsData = result.data || [];
                
                if (allRegistrationsData.length === 0) {
                    document.getElementById('noRegistrations').style.display = 'block';
                    document.getElementById('bookingTabContent').style.display = 'none';
                    document.getElementById('bookingTabs').style.display = 'none';
                    document.getElementById('statisticsCards').style.display = 'none';
                    return;
                }
                
                renderRegistrations();
            } catch (error) {
                console.error('Error loading registrations:', error);
                hideLoading();
                // alert('Không thể tải danh sách đơn đăng ký phòng: ' + error.message);
            }
        }
// Render registrations
        function renderRegistrations() {
            const all = allRegistrationsData;
            const pending = allRegistrationsData.filter(r => r.status === 'Chờ duyệt');
            const approved = allRegistrationsData.filter(r => r.status === 'Đã duyệt');
            const rejected = allRegistrationsData.filter(r => r.status === 'Từ chối');
            
            document.getElementById('allRegistrations').innerHTML = renderRegistrationList(all);
            document.getElementById('pendingRegistrations').innerHTML = renderRegistrationList(pending);
            document.getElementById('approvedRegistrations').innerHTML = renderRegistrationList(approved);
            document.getElementById('rejectedRegistrations').innerHTML = renderRegistrationList(rejected);
        }
        
        // Render registration list
        function renderRegistrationList(registrations) {
            if (registrations.length === 0) {
                return '<div class="col-12 text-center"><p class="text-muted">Không có đơn đăng ký phòng nào</p></div>';
            }
            
            return registrations.map(reg => `
                <div class="col-md-6 col-lg-4">
                    <div class="card shadow-sm h-100">
                        <div class="card-body">
                            <h5 class="card-title">
                                <i class="fas fa-building text-primary"></i> 
                                Tòa ${reg.building} - Tầng ${reg.floor}
                            </h5>
                            <hr>
                            <p class="card-text">
                                <i class="fas fa-bed me-2"></i>
                                <strong>Giường:</strong> ${reg.bed_number || 'Chưa phân'}
                            </p>
                            <p class="card-text">
                                <i class="far fa-calendar me-2"></i>
                                <strong>Thời gian:</strong><br>
                                <small>${formatDate(reg.start_date)} - ${formatDate(reg.end_date)}</small>
                            </p>
                            <p class="card-text">
                                <i class="fas fa-money-bill-wave me-2"></i>
                                <strong>Giá:</strong> ${formatMoney(reg.price_per_month)}/tháng
                            </p>
                            <p class="card-text">
                                <i class="far fa-clock me-2"></i>
                                <strong>Ngày đăng ký:</strong><br>
                                <small>${formatDateTime(reg.request_date)}</small>
                            </p>
                            <div class="mb-3">
                                ${getStatusBadge(reg.status)}
                            </div>
                            ${reg.contract_id ? `
<div class="alert alert-info py-2 px-3 mb-3">
                                    <small><i class="fas fa-file-contract me-1"></i> Hợp đồng: #${reg.contract_id}</small>
                                </div>
                            ` : ''}
                            <div class="d-grid gap-2">
                                <button class="btn btn-outline-primary btn-sm" onclick="viewRegistrationDetail(${reg.registration_id})">
                                    <i class="fas fa-eye me-1"></i> Xem Chi Tiết
                                </button>
                                <button class="btn btn-outline-primary btn-sm" onclick="huyDangKy(${reg.registration_id})">
                                    <i class="fas me-1"></i> huỷ 
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            `).join('');
        }
        
        // Get status badge
        function getStatusBadge(status) {
            const statusMap = {
                'Chờ duyệt': { text: 'Chờ Duyệt', class: 'warning' },
                'Đã duyệt': { text: 'Đã Duyệt', class: 'success' },
                'Từ chối': { text: 'Từ Chối', class: 'danger' }
            };
            
            const statusInfo = statusMap[status] || { text: status, class: 'secondary' };
            return `<span class="badge bg-${statusInfo.class}">${statusInfo.text}</span>`;
        }
        
        // View registration detail
        async function viewRegistrationDetail(registrationId) {
            try {
                const response = await fetch(`../../../backend/controller/student/CustomerRegistrationController.php?action=get-registration-detail&registration_id=${registrationId}`, {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    credentials: 'include'
                });
                
                const result = await response.json();
                
                if (!result.success) {
                    throw new Error(result.message);
                }
                
                const reg = result.data;
                
                const modalContent = `
                    <div class="modal fade" id="registrationDetailModal" tabindex="-1">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header bg-primary text-white">
                                    <h5 class="modal-title">
                                        <i class="fas fa-info-circle me-2"></i>
                                        Chi Tiết Đơn Đăng Ký #${reg.registration_id}
                                    </h5>
<button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <h6 class="text-primary"><i class="fas fa-user me-2"></i>Thông Tin Sinh Viên</h6>
                                            <p><strong>Họ tên:</strong> ${reg.full_name}</p>
                                            <p><strong>Email:</strong> ${reg.email}</p>
                                            <p><strong>Điện thoại:</strong> ${reg.phone || 'Chưa cập nhật'}</p>
                                            <p><strong>Khoa:</strong> ${reg.faculty || 'Chưa cập nhật'}</p>
                                            <p><strong>Ngành:</strong> ${reg.major || 'Chưa cập nhật'}</p>
                                            <hr>
                                            <h6 class="text-primary"><i class="fas fa-building me-2"></i>Thông Tin Phòng</h6>
                                            <p><strong>Tòa nhà:</strong> ${reg.building}</p>
                                            <p><strong>Tầng:</strong> ${reg.floor}</p>
                                            <p><strong>Giường:</strong> ${reg.bed_number || 'Chưa phân'}</p>
                                            <p><strong>Sức chứa:</strong> ${reg.capacity} người</p>
                                            <p><strong>Giới tính:</strong> ${reg.gender_restriction}</p>
                                            <p><strong>Giá:</strong> ${formatMoney(reg.price_per_month)}/tháng</p>
                                        </div>
                                        <div class="col-md-6">
                                            <h6 class="text-primary"><i class="fas fa-calendar-alt me-2"></i>Thời Gian</h6>
                                            <p><strong>Ngày bắt đầu:</strong> ${formatDate(reg.start_date)}</p>
                                            <p><strong>Ngày kết thúc:</strong> ${formatDate(reg.end_date)}</p>
                                            <p><strong>Ngày đăng ký:</strong> ${formatDateTime(reg.request_date)}</p>
                                            <hr>
                                            <h6 class="text-primary"><i class="fas fa-clipboard-check me-2"></i>Trạng Thái</h6>
                                            <p><strong>Trạng thái:</strong> ${getStatusBadge(reg.status)}</p>
                                            ${reg.approver_name ? `<p><strong>Người duyệt:</strong> ${reg.approver_name}</p>` : ''}
                                            ${reg.contract_id ? `
                                                <hr>
<h6 class="text-primary"><i class="fas fa-file-contract me-2"></i>Hợp Đồng</h6>
                                                <p><strong>Mã hợp đồng:</strong> #${reg.contract_id}</p>
                                                <p><strong>Ngày tạo:</strong> ${formatDate(reg.contract_date)}</p>
                                                <p><strong>Ngày hết hạn:</strong> ${formatDate(reg.contract_end_date)}</p>
                                                <p><strong>Trạng thái:</strong> <span class="badge bg-info">${reg.contract_status}</span></p>
                                            ` : ''}
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                        <i class="fas fa-times me-1"></i> Đóng
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
                
                // Remove existing modal if any
                const existingModal = document.getElementById('registrationDetailModal');
                if (existingModal) {
                    existingModal.remove();
                }
                
                // Add new modal
                document.body.insertAdjacentHTML('beforeend', modalContent);
                const modal = new bootstrap.Modal(document.getElementById('registrationDetailModal'));
                modal.show();
                
            } catch (error) {
                console.error('Error loading registration detail:', error);
                alert('Không thể tải chi tiết đơn đăng ký: ' + error.message);
            }
        }

        //huỷ đăng ký nếu chưa duyệt
        async function huyDangKy(registrationId){
            try {
                if (!confirm("Bạn có chắc muốn hủy đơn đăng ký này không?")) return;
                const response = await  fetch(`../../../backend/controller/student/CustomerRegistrationController.php?action=huyDangKyPhong&registration_id=${registrationId}`, {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    credentials: 'include'
                });

                const result = await response.json();
                if(result.success){
                    alert(result.message);
                    loadRegistrations();
                }else{
                    alert("Không thể hủy: " + result.message);
                }
            } catch (error) {
                console.error(error);
                alert("Lỗi khi hủy: " + error.message);
            }
        }
// Format date
        function formatDate(dateString) {
            if (!dateString) return 'N/A';
            const date = new Date(dateString);
            return date.toLocaleDateString('vi-VN');
        }
        
        // Format datetime
        function formatDateTime(dateString) {
            if (!dateString) return 'N/A';
            const date = new Date(dateString);
            return date.toLocaleString('vi-VN');
        }
        
        // Format money
        function formatMoney(amount) {
            if (!amount) return '0';
            return new Intl.NumberFormat('vi-VN', {
                style: 'currency',
                currency: 'VND'
            }).format(amount);
        }
        
        // Show loading
        function showLoading() {
            document.getElementById('loadingSpinner').style.display = 'block';
        }
        
        // Hide loading
        function hideLoading() {
            document.getElementById('loadingSpinner').style.display = 'none';
        }
    </script>
</body>
</html>
