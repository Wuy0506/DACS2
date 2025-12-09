
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Hệ thống quản lý khách sạn - Đặt phòng trực tuyến">
    <title>Hotelier - Trang Chủ</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="https://luxcity.com/LuxCity/img/shared//favicon.png">

    <!-- Icon Font Stylesheet -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">


    <!-- Bootstrap CSS -->
    <link href="/frontend/frontend-html/css/bootstrap.min.css" rel="stylesheet">

    <!-- Template Stylesheet -->
    <link href="/frontend/frontend-html/css/style.css" rel="stylesheet">

    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">

    <link rel="stylesheet" href="../568/assets/css/templatemo-digimedia-v3.css">

    <style>
    #searchResultsSection {
        display: none;
        animation: slideDown 0.5s ease-out;
    }

    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translateY(-20px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .results-card {
        background: #fff;
        border-radius: 15px;
        box-shadow: 0 0 30px rgba(0, 0, 0, 0.1);
        padding: 30px;
        margin-bottom: 30px;
    }

    .badge-active {
        background-color: #28a745;
    }

    .badge-full {
        background-color: #dc3545;
    }

    table.dataTable thead th {
        background-color: #4da6e7;
        color: white;
        font-weight: 600;
    }

    .btn-action {
        padding: 5px 15px;
        font-size: 13px;
        margin-right: 5px;
    }

    .room-type-badge {
        display: inline-block;
        padding: 5px 12px;
        border-radius: 15px;
        font-size: 12px;
        font-weight: 500;
    }

    .room-type-standard {
        background-color: #e3f2fd;
        color: #1976d2;
    }

    .room-type-deluxe {
        background-color: #f3e5f5;
        color: #7b1fa2;
    }

    .room-type-suite {
        background-color: #fff3e0;
        color: #e65100;
    }

    /* toà nhà */
    .building-card {
        transition: 0.3s;
        cursor: pointer;
        border-radius: 18px;
    }

    .building-card:hover {
        transform: translateY(-6px);
    box-shadow: 0 8px 20px rgba(0,0,0,0.15);
    }

    .building-icon {
        font-size: 60px;
        color: #fd950dff  ;
    }

    .building-title {
        font-size: 28px;
        font-weight: bold;
        color: #fd950dff;
    }

    </style>

</head>

<body>
    <?php include '../frontend/frontend-html/customer/include/headd.php'; ?>
    <!-- Carousel Start -->
    <div class="container-fluid p-0 mb-5">
        <div id="header-carousel" class="carousel slide" data-bs-ride="carousel">
            <div class="carousel-inner">
                <div class="carousel-item active">
                    <img class="w-100" src="/frontend/frontend-html/images/anhKTX1.png" alt="Carousel 1" style="width:600px; height:420px; object-fit:cover;">
                    <div class="carousel-caption d-flex flex-column align-items-center justify-content-center">
                        <div class="p-3" style="max-width: 700px;">
                            <h6 class="section-title text-white text-uppercase mb-3 animated slideInDown">Dormitory System</h6>
                            <h1 class="display-3 text-white mb-4 animated slideInDown">Không Gian Sinh Viên</h1>
                        </div>
                    </div>
                </div>
                <div class="carousel-item">
                    <img class="w-100" src="../frontend/frontend-html/images/anhKTX2.png" alt="Carousel 2" style="width:600px; height:420px; object-fit:cover;">
                    <div class="carousel-caption d-flex flex-column align-items-center justify-content-center">
                        <div class="p-3" style="max-width: 700px;">
                            <h6 class="section-title text-white text-uppercase mb-3 animated slideInDown">Dormitory System</h6>
                            <h1 class="display-3 text-white mb-4 animated slideInDown">Khám Phá Ký Túc Xá</h1>
                        </div>
                    </div>
                </div>
            </div>
            <button class="carousel-control-prev" type="button" data-bs-target="#header-carousel" data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Previous</span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#header-carousel" data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Next</span>
            </button>
        </div>
    </div>
    <!-- Carousel End -->

    <!-- Booking Form Start -->
    <div class="container-fluid booking pb-5 wow fadeIn">
        <div class="container">
            <div class="bg-white shadow" style="padding: 35px; border-radius: 9px;">
                <form id="bookingForm">
                    <div class="row g-2">
                        <div class="col-md-3">
                                <label class="form-label fw-bold">
                                <i class="bi bi-people"></i> Số người
                            </label>
                            <input type="number" id="peopleCount" class="form-control" min="1" value="1">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold">
                                <i class="bi bi-gender-ambiguous"></i> Giới tính
                            </label>
                            <select id="genderFilter" class="form-select">
                                <option value="">Tất cả</option>
                                <option value="Nam">Nam</option>
                                <option value="Nữ">Nữ</option>
                                <option value="Không giới hạn">Không giới hạn</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold">
                                <i class="bi bi-door-open"></i> Tình trạng
                            </label>
                            <select id="statusFilter" class="form-select">
                                <option value="">Tất cả</option>
                                <option value="Trống" selected>Trống</option>
                                <option value="Đầy">Đầy</option>
                                <option value="Bảo trì">Bảo trì</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold" style="opacity: 0;">Button</label>
                            <button type="submit" class="btn btn-primary w-100 d-block">
                                <i class="bi bi-search"></i> TÌM KIẾM PHÒNG
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Search Results Section -->
    <div id="searchResultsSection" class="container-fluid pb-5">
        <div class="container">
            <div class="results-card">
                <h4 class="mb-4">
                    <i class="bi bi-list-check"></i> Danh Sách Phòng Khả Dụng
                    <span class="badge bg-primary" id="roomCount">0</span>
                </h4>

                <div class="table-responsive">
                    <table class="table table-hover table-striped table-bordered" id="roomsTable">
                        <thead>
                            <tr>
                                <th style="width: 5%;">#</th>
                                <th style="width: 10%;">Khu nhà</th>
                                <th style="width: 8%;">Phòng/Tầng</th>
                                <th style="width: 8%;">Sức chứa</th>
                                <th style="width: 10%;">Còn trống</th>
                                <th style="width: 12%;">Giới hạn</th>
                                <th style="width: 12%;">Giá/tháng</th>
                                <th style="width: 10%;">Trạng thái</th>
                                <th style="width: 25%;">Hành động</th>
                            </tr>
                        </thead>
                        <tbody id="roomsTableBody">
                            <!-- Data will be loaded here -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- About Start -->
    <div class="container-xxl py-5">
        <div class="container">
            <div class="row g-5 align-items-center">
                <div class="col-lg-6">
                    <h6 class="section-title text-start text-primary text-uppercase">Về Chúng Tôi</h6>
                    <h1 class="mb-4">Chào Mừng Đến <span class="text-primary text-uppercase">Ký Túc Xá</span></h1>
                    <p class="mb-4">Ký túc xá không chỉ là nơi ở, mà còn là ngôi nhà thứ hai của sinh viên. 
                        Tại đây, bạn được sống trong môi trường thân thiện, an toàn, đầy đủ tiện nghi và là nơi gắn kết bạn bè, 
                        chia sẻ kỷ niệm trong quãng đời sinh viên.</p>
                    <div class="row g-3 pb-4">
                        <div class="col-sm-4 wow fadeIn" data-wow-delay="0.1s">
                            <div class="border rounded p-1">
                                <div class="border rounded text-center p-4">
                                    <i class="fa fa-hotel fa-2x text-primary mb-2"></i>
                                    <h2 class="mb-1" data-toggle="counter-up">100+</h2>
                                    <p class="mb-0">Phòng</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-4 wow fadeIn" data-wow-delay="0.3s">
                            <div class="border rounded p-1">
                                <div class="border rounded text-center p-4">
                                    <i class="fa fa-users-cog fa-2x text-primary mb-2"></i>
                                    <h2 class="mb-1" data-toggle="counter-up">10+</h2>
                                    <p class="mb-0">Nhân Viên</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-4 wow fadeIn" data-wow-delay="0.5s">
                            <div class="border rounded p-1">
                                <div class="border rounded text-center p-4">
                                    <i class="fa fa-users fa-2x text-primary mb-2"></i>
                                    <h2 class="mb-1" data-toggle="counter-up">1000+</h2>
                                    <p class="mb-0">Sinh Viên</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <a class="btn btn-primary py-3 px-5 mt-2" href="/frontend/about">Khám Phá Thêm</a>
                </div>
                <div class="col-lg-6">
                    <div class="row g-3">
                        <div class="col-6 text-end">
                            <img class="img-fluid rounded w-75 wow zoomIn" data-wow-delay="0.1s"
                                src="../frontend/frontend-html/images/anhktx5.png" style="margin-top: 30%;">
                        </div>
                        <div class="col-6 text-start">
                            <img class="img-fluid rounded w-100 wow zoomIn" data-wow-delay="0.3s"
                                src="../frontend/frontend-html/images/anhktx4.png">
                        </div>
                        <div class="col-6 text-end">
                            <img class="img-fluid rounded w-50 wow zoomIn" data-wow-delay="0.5s"
                                src="../frontend/frontend-html/images/anhktx3.png">
                        </div>
                        <div class="col-6 text-start">
                            <img class="img-fluid rounded w-75 wow zoomIn" data-wow-delay="0.7s"
                                src="../frontend/frontend-html/images/anhktt6.png">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- About End -->

    <!-- Rooms Start -->
    <div class="container-xxl py-5 bg-white">
        <div class="container">
            <div class="text-center wow fadeInUp" data-wow-delay="0.1s">
                <h6 class="section-title text-center text-primary text-uppercase">Phòng Của Chúng Tôi</h6>
                <h1 class="mb-2">Khám Phá <span class="text-primary text-uppercase">Các Toà</span></h1>
            </div>

            <div class="row g-4 text-center">
                <div class="col-md-4">
                    <a href="/frontend/toaA" class="text-decoration-none text-dark">
                        <div class="p-4 shadow building-card bg-white">
                            <i class="bi bi-building building-icon"></i>
                            <h3 class="building-title mt-3">TÒA A</h3>
                            <p class="text-muted">Nhấn để xem các phòng tại tòa A</p>
                        </div>
                    </a>
                </div>

                <div class="col-md-4">
                    <a href="/frontend/toaB" class="text-decoration-none text-dark">
                        <div class="p-4 shadow building-card bg-white">
                            <i class="bi bi-building building-icon"></i>
                            <h3 class="building-title mt-3">TÒA B</h3>
                            <p class="text-muted">Nhấn để xem các phòng tại tòa B</p>
                        </div>
                    </a>
                </div>

                <div class="col-md-4">
                    <a href="/frontend/toaC" class="text-decoration-none text-dark">
                        <div class="p-4 shadow building-card bg-white">
                            <i class="bi bi-building building-icon"></i>
                            <h3 class="building-title mt-3">TÒA C</h3>
                            <p class="text-muted">Nhấn để xem các phòng tại tòa C</p>
                        </div>
                    </a>
                </div>
            </div>

            
        </div>
    </div>
    <!-- Rooms End -->
    <?php include '../frontend/frontend-html/customer/include/footer.php'; ?>
    <!-- Footer End -->

    <!-- Back to Top -->
    <a href="#" class="btn btn-lg btn-primary btn-lg-square back-to-top"><i class="bi bi-arrow-up"></i></a>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script src="js/main.js"></script>

    <!-- Custom Scripts -->
    <script src="../frontend/frontend-html/js/api.js"></script>
    <script src="../frontend/frontend-html/js/auth.js"></script>
    <script src="../frontend/frontend-html/js/app.js"></script>

    <script>
    let dataTable;

    // Initialize the page
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize DataTable
        initializeDataTable();

        // Load user session
        loadUserSession();

        // Load rooms
        loadRooms();
    });

    function initializeDataTable() {
        dataTable = $('#roomsTable').DataTable({
            "paging": true,
            "lengthChange": true,
            "searching": true,
            "ordering": true,
            "info": true,
            "autoWidth": false,
            "responsive": true,
            "language": {
                "lengthMenu": "Hiển thị _MENU_ phòng mỗi trang",
                "zeroRecords": "Không tìm thấy phòng phù hợp",
                "info": "Hiển thị trang _PAGE_ / _PAGES_",
                "infoEmpty": "Không có dữ liệu",
                "infoFiltered": "(lọc từ _MAX_ phòng)",
                "search": "Tìm kiếm:",
                "paginate": {
                    "first": "Đầu",
                    "last": "Cuối",
                    "next": "Sau",
                    "previous": "Trước"
                }
            },
            "pageLength": 10
        });
    }

    // Handle booking form submission
    document.getElementById('bookingForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const people = document.getElementById('peopleCount').value;
        const gender = document.getElementById('genderFilter').value;
        const status = document.getElementById('statusFilter').value;

        // Save to session storage
        sessionStorage.setItem('bookingSearch', JSON.stringify({
            people: people,
            gender: gender,
            status: status
        }));

        // Load and display search results
        loadSearchResults(people, gender, status);

        // Scroll to results
        setTimeout(() => {
            document.getElementById('searchResultsSection').scrollIntoView({
                behavior: 'smooth'
            });
        }, 500);
    });

    function loadSearchResults(people, gender, status) {
        // Show results section
        document.getElementById('searchResultsSection').style.display = 'block';

        // Build URL với các filter
        let url = `../../backend/search.php?action=search&people=${people}`;
        if (gender) url += `&gender=${gender}`;
        if (status) url += `&status=${status}`;

        // Call API mới theo MVC structure
        fetch(url, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    displaySearchResults(result.data);
                } else {
                    alert(result.message || 'Không thể tải danh sách phòng');
                    displaySearchResults([]);
                }
            })
            .catch(error => {
                console.error('Error loading search results:', error);
                alert('Không thể tải danh sách phòng. Vui lòng thử lại sau.');
                displaySearchResults([]);
            });
    }

    function displaySearchResults(rooms) {
        // Clear existing data
        dataTable.clear();

        if (!rooms || rooms.length === 0) {
            dataTable.draw();
            document.getElementById('roomCount').textContent = '0';
            return;
        }

        // Add rows to DataTable với cấu trúc mới
        rooms.forEach((room, index) => {
            const rowData = [
                index + 1,
                room.building || 'N/A',
                room.name +'/'+ room.floor || 'N/A',
                room.capacity || 0,
                room.available || 0,
                getGenderBadge(room.gender_restriction || 'Không giới hạn'),
                formatMoney(room.price) + ' VNĐ/tháng',
                getStatusBadge(room.status),
                getActionButtons(room)
            ];
            dataTable.row.add(rowData);
        });

        dataTable.draw();
        document.getElementById('roomCount').textContent = rooms.length;
    }

    function getGenderBadge(gender) {
        const badges = {
            'Nam': '<span class="badge bg-info">Nam</span>',
            'Nữ': '<span class="badge" style="background-color: #e91e63;">Nữ</span>',
            'Không giới hạn': '<span class="badge bg-secondary">Không giới hạn</span>'
        };
        return badges[gender] || badges['Không giới hạn'];
    }

    function getStatusBadge(status) {
        const badges = {
            'Trống': '<span class="badge badge-active">Trống</span>',
            'Đầy': '<span class="badge badge-full">Đầy</span>',
            'Bảo trì': '<span class="badge bg-warning">Bảo trì</span>'
        };
        return badges[status] || '<span class="badge bg-secondary">N/A</span>';
    }

    function getActionButtons(room) {
        return `
            <button class="btn btn-sm btn-success btn-action" onclick="bookRoom(${room.id})">
                <i class="bi bi-calendar-check"></i> Đặt phòng
            </button>
        `;
    }

    window.bookRoom = function(roomId) {
        // Check if user is logged in và lấy user_id
        fetch('../../backend/auth.php?action=check-status', {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(result => {
                if (result.is_logged_in && result.user && result.user.user_id) {
                    // Kiểm tra profile đã hoàn chỉnh chưa
                    checkProfileBeforeBooking(roomId, result.user.user_id);
                } else if (result.is_logged_in) {
                    // Đã login nhưng không có user_id, vẫn cho vào (để trống form)
                    window.location.href = `../frontend/frontend-html/customer/checkin.php?room=${roomId}`;
                } else {
                    alert('Vui lòng đăng nhập để đặt phòng');
                    window.location.href = '../../LoginDarkSunSet/login.php';
                }
            })
            .catch(error => {
                console.error('Error checking login status:', error);
                alert('Có lỗi xảy ra. Vui lòng thử lại.');
            });
    };

    // Hàm kiểm tra profile hoàn chỉnh trước khi đặt phòng
    function checkProfileBeforeBooking(roomId, userId) {
        fetch(`../../backend/profile.php?action=check-profile-complete&user_id=${userId}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    if (result.is_complete) {
                    // Profile đã hoàn chỉnh, cho phép đặt phòng
                        window.location.href =
                            `/frontend/datphong/${roomId}/${userId}`;
                    } else {
                        // Profile chưa hoàn chỉnh, hiển thị thông báo và chuyển đến trang profile
                        const missingFields = result.missing_fields.join(', ');
                        alert(`⚠️ Vui lòng hoàn thiện thông tin cá nhân trước khi đăng ký phòng!\n\nCác thông tin còn thiếu:\n${missingFields}`);
                        
                        // Hỏi người dùng có muốn đến trang profile không
                        if (confirm('Bạn có muốn đến trang cập nhật thông tin cá nhân không?')) {
                            window.location.href = `../frontend/frontend-html/customer/profile.php?user_id=${userId}`;
                        }
                    }
                } else {
                    alert('Không thể kiểm tra thông tin: ' + (result.message || 'Lỗi không xác định'));
                }
            })
            .catch(error => {
                console.error('Error checking profile:', error);
                alert('Có lỗi xảy ra khi kiểm tra thông tin. Vui lòng thử lại.');
            });
    }

    </script>
</body>

</html>