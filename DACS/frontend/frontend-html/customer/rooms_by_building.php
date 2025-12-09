<?php 
// Lấy tên tòa nhà từ URL, nếu không có thì mặc định là A
$building = isset($_GET['building']) ? $_GET['building'] : 'A';
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Danh Sách Phòng Tòa <?php echo $building; ?></title>

    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="/frontend/frontend-html/css/bootstrap.min.css" rel="stylesheet">
    <link href="/frontend/frontend-html/css/style.css" rel="stylesheet">
    
    <style>
        /* CSS cho thẻ phòng */
        .room-item { transition: .5s; }
        .room-item:hover { box-shadow: 0 0 30px #CCCCCC; }
        .unvailable-room { filter: grayscale(100%); }
    </style>
</head>

<body>
    <?php include('include/headd.php');  ?>

    <div class="container-fluid page-header mb-5 p-0" style="background-image: url(/frontend/frontend-html/images/anhktx3.png);">
        <div class="container-fluid page-header-inner py-5">
            <div class="container text-center pb-5">
                <h1 class="display-3 text-white mb-3 animated slideInDown">Tòa <?php echo $building; ?></h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb justify-content-center text-uppercase">
                        <li class="breadcrumb-item"><a href="/frontend/trangchu">Trang Chủ</a></li>
                        <li class="breadcrumb-item text-white active" aria-current="page">Danh Sách Phòng</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
    <div class="container-xxl py-5">
        <div class="container">
            <div class="text-center wow fadeInUp" data-wow-delay="0.1s">
                <h6 class="section-title text-center text-primary text-uppercase">Ký Túc Xá</h6>
                <h1 class="mb-5">Danh Sách Phòng <span class="text-primary text-uppercase">Tòa <?php echo $building; ?></span></h1>
            </div>

            <div id="loadingSpinner" class="text-center my-5">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Đang tải...</span>
                </div>
            </div>

            <div id="roomListContainer" class="row g-4">
                </div>
        </div>
    </div>
    <?php include('include/footer.php');  ?>

    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../js/main.js"></script>

    <script src="/frontend/frontend-html/js/api.js"></script>
    <script src="/frontend/frontend-html/js/auth.js"></script>
    <script src="/frontend/frontend-html/js/app.js"></script>

    <script>
        // Lấy biến building từ PHP sang JS
        const currentBuilding = "<?php echo $building; ?>";

        document.addEventListener('DOMContentLoaded', function() {
            loadUserSession();
            loadRoomsForBuilding(currentBuilding);
        });

        function loadRoomsForBuilding(building) {
            const container = document.getElementById('roomListContainer');
            const spinner = document.getElementById('loadingSpinner');

            // Gọi API Backend (Sử dụng đường dẫn backend của bạn)
            fetch(`../../../backend/search.php?action=get-rooms-by-building&building=${building}`)
                .then(response => response.json())
                .then(result => {
                    spinner.style.display = 'none';

                    if (result.success && result.data.length > 0) {
                        renderRoomsByFloor(result.data);
                    } else {
                        container.innerHTML = `
                            <div class="col-12 text-center">
                                <div class="alert alert-warning">
                                    Không có phòng nào trống tại tòa ${building} vào lúc này.
                                </div>
                                <a href="/frontend/trangchu" class="btn btn-primary">Quay lại trang chủ</a>
                            </div>`;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    spinner.style.display = 'none';
                    container.innerHTML = '<p class="text-center text-danger">Lỗi kết nối server.</p>';
                });
        }

        function renderRoomsByFloor(rooms) {
            const container = document.getElementById('roomListContainer');
            
            // 1. Gom nhóm phòng theo tầng
            const roomsByFloor = {};
            rooms.forEach(room => {
                const floor = room.floor || 'Khác';
                if (!roomsByFloor[floor]) roomsByFloor[floor] = [];
                roomsByFloor[floor].push(room);
            });

            let htmlContent = '';

            // 2. Duyệt qua từng tầng và render
            Object.keys(roomsByFloor).sort().forEach(floor => {
                const floorRooms = roomsByFloor[floor];

                // Tiêu đề tầng
                htmlContent += `
                    <div class="col-12 mt-4 mb-2 wow fadeInUp" data-wow-delay="0.1s">
                        <div class="d-flex align-items-center">
                            <h4 class="text-primary mb-0 me-3 border-start border-5 border-primary ps-3">
                                <i class="bi bi-layers-fill"></i> Tầng ${floor}
                            </h4>
                            <span class="badge bg-secondary">${floorRooms.length} phòng</span>
                            <hr class="flex-grow-1 ms-3 text-muted">
                        </div>
                    </div>
                `;

                // Danh sách phòng trong tầng
                floorRooms.forEach(room => {
                    htmlContent += `
                        <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.2s">
                            <div class="room-item shadow rounded overflow-hidden">
                                <div class="position-relative">
                                    <img class="img-fluid w-100" src="${room.image}" alt="${room.room_name}" style="height: 250px; object-fit: cover;">
                                    <small class="position-absolute start-0 top-100 translate-middle-y bg-primary text-white rounded py-1 px-3 ms-4">
                                        ${new Intl.NumberFormat('vi-VN').format(room.price_per_month)} VNĐ
                                    </small>
                                </div>
                                <div class="p-4 mt-2">
                                    <div class="d-flex justify-content-between mb-3">
                                        <h5 class="mb-0">Phòng ${room.room_name}</h5>
                                        <div class="ps-2">
                                            <span class="badge ${getGenderBadgeClass(room.gender_restriction)}">
                                                ${room.gender_restriction}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="d-flex mb-3">
                                        <small class="border-end me-3 pe-3"><i class="fa fa-bed text-primary me-2"></i>${room.capacity} Giường</small>
                                        <small class="border-end me-3 pe-3"><i class="fa fa-check-circle text-primary me-2"></i>Trống ${room.available_beds}</small>
                                    </div>
                                    <p class="text-body mb-3 small">${room.description || 'Phòng đầy đủ tiện nghi...'}</p>
                                    <div class="d-flex justify-content-between">
                                        <button class="btn btn-sm btn-dark rounded py-2 px-4" onclick="bookRoom(${room.room_id})">Đặt Ngay</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                });
            });

            container.innerHTML = htmlContent;
        }

        // Hàm helper chọn màu badge cho giới tính
        function getGenderBadgeClass(gender) {
            if (gender === 'Nam') return 'bg-info';
            if (gender === 'Nữ') return 'bg-danger';
            return 'bg-success';
        }

        // Hàm xử lý đặt phòng (Copy lại từ file cũ của bạn để hoạt động được ở trang mới)
        function bookRoom(roomId) {
            // Check if user is logged in và lấy user_id
            fetch('/backend/auth.php?action=check-status', {
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
                        window.location.href = `/frontend/frontend-html/customer/checkin.php?room=${roomId}`;
                    } else {
                        alert('Vui lòng đăng nhập để đặt phòng');
                        window.location.href = '/LoginDarkSunSet/login.php';
                    }
                })
                .catch(error => {
                    console.error('Error checking login status:', error);
                    alert('Có lỗi xảy ra. Vui lòng thử lại.');
                });
        }

        // Hàm kiểm tra profile hoàn chỉnh trước khi đặt phòng
    function checkProfileBeforeBooking(roomId, userId) {
        fetch(`/backend/profile.php?action=check-profile-complete&user_id=${userId}`, {
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
                            window.location.href = `/frontend/frontend-html/customer/profile.php?user_id=${userId}`;
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