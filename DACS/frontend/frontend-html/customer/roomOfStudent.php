<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hotelier - Phòng Của Bạn</title>

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

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
    :root {
        --primary: #FEA116;
        --light: #F1F8FF;
        --dark: #0F172B;
    }

    .page-header { background-position: center center; background-repeat: no-repeat; background-size: cover; }
    .page-header-bg { background-image: url('/frontend/frontend-html/images/anhKTX1.png'); background-color: rgba(15, 39, 43, 0.7); background-blend-mode: multiply; }
    .page-header h3.display-3 { font-size: 2.5rem !important; line-height: 1.2 !important; font-weight: 700 !important; }

    .room-info-item { display: flex; align-items: center; padding: 15px; background: #fff; border-radius: 8px; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05); transition: .3s; height: 100%; }
    .room-info-item:hover { transform: translateY(-5px); box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1); }
    .room-info-icon { width: 50px; height: 50px; display: flex; align-items: center; justify-content: center; background: var(--light); color: var(--primary); border-radius: 50px; margin-right: 15px; font-size: 20px; }

    .bed-card { background: #fff; border-radius: 10px; overflow: hidden; box-shadow: 0 0 20px rgba(0, 0, 0, 0.08); transition: all 0.3s ease; position: relative; border-top: 5px solid transparent; height: 100%; }
    .bed-card:hover { transform: translateY(-5px); box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15); }
    .bed-card-body { padding: 25px; text-align: center; }
    .bed-icon-wrapper { width: 80px; height: 80px; margin: 0 auto 15px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 30px; transition: 0.3s; }

    .my-bed { border-top-color: var(--primary); }
    .my-bed .bed-icon-wrapper { background-color: #FEA116; color: white; box-shadow: 0 0 0 5px rgba(254, 161, 22, 0.2); }
    .my-bed .status-badge { background-color: var(--primary); }

    .roommate-bed { border-top-color: #0F172B; cursor: pointer; }
    .roommate-bed .bed-icon-wrapper { background-color: #0F172B; color: white; }
    .roommate-bed:hover .bed-icon-wrapper { transform: scale(1.1); }
    .roommate-bed .status-badge { background-color: #0F172B; }

    .empty-bed { border-top-color: #dee2e6; opacity: 0.8; }
    .empty-bed .bed-icon-wrapper { background-color: #f8f9fa; color: #adb5bd; border: 2px dashed #dee2e6; }
    .empty-bed .status-badge { background-color: #6c757d; }

    .bed-number-display { position: absolute; top: 10px; right: 15px; font-family: 'Heebo', sans-serif; font-weight: 800; font-size: 3rem; opacity: 0.05; }
    .status-badge { display: inline-block; padding: 5px 12px; border-radius: 20px; color: white; font-size: 12px; font-weight: 600; margin-bottom: 15px; text-transform: uppercase; }
    .user-name { font-family: 'Montserrat', sans-serif; font-weight: 700; font-size: 18px; color: #0F172B; margin-bottom: 5px; }
    .user-role { font-size: 14px; color: #666; }

    .modal-header { background-color: var(--primary); color: white; }
    .modal-item { margin-bottom: 12px; padding-bottom: 12px; border-bottom: 1px dashed #eee; }
    .modal-item:last-child { border-bottom: none; }
    .modal-item i { width: 25px; color: var(--primary); }

    /* Payment Section Styles */
    .payment-section { margin-top: 50px; }
    .payment-card { background: #fff; border-radius: 15px; box-shadow: 0 5px 25px rgba(0, 0, 0, 0.08); overflow: hidden; height: 100%; transition: all 0.3s ease; }
    .payment-card:hover { transform: translateY(-5px); box-shadow: 0 10px 35px rgba(0, 0, 0, 0.12); }
    .payment-card-header { background: linear-gradient(135deg, var(--primary) 0%, #e8920e 100%); color: white; padding: 20px; text-align: center; }
    .payment-card-header.success { background: linear-gradient(135deg, #28a745 0%, #20c997 100%); }
    .payment-card-header.info { background: linear-gradient(135deg, #17a2b8 0%, #6610f2 100%); }
    .payment-card-header i { font-size: 2.5rem; margin-bottom: 10px; }
    .payment-card-body { padding: 25px; }

    .fee-item { display: flex; justify-content: space-between; align-items: center; padding: 15px; background: #f8f9fa; border-radius: 10px; margin-bottom: 12px; border-left: 4px solid var(--primary); transition: all 0.3s ease; }
    .fee-item:hover { background: #fff3e0; }
    .fee-item.paid { border-left-color: #28a745; background: #e8f5e9; }
    .fee-info h6 { margin: 0; font-weight: 600; color: var(--dark); }
    .fee-info small { color: #666; }
    .fee-amount { font-weight: 700; font-size: 1.1rem; color: var(--primary); }
    .fee-amount.paid { color: #28a745; }

    .btn-pay { background: linear-gradient(135deg, var(--primary) 0%, #e8920e 100%); border: none; color: white; padding: 8px 20px; border-radius: 25px; font-weight: 600; transition: all 0.3s ease; }
    .btn-pay:hover { transform: scale(1.05); box-shadow: 0 5px 15px rgba(254, 161, 22, 0.4); color: white; }

    .history-item { display: flex; align-items: center; padding: 15px; border-bottom: 1px solid #eee; transition: background 0.3s; cursor: pointer; }
    .history-item:hover { background: #f8f9fa; }
    .history-item:last-child { border-bottom: none; }
    .history-icon { width: 45px; height: 45px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-right: 15px; font-size: 18px; }
    .history-icon.room { background: rgba(254, 161, 22, 0.15); color: var(--primary); }
    .history-icon.electric { background: rgba(255, 193, 7, 0.15); color: #ffc107; }
    .history-icon.water { background: rgba(23, 162, 184, 0.15); color: #17a2b8; }
    .history-icon.other { background: rgba(108, 117, 125, 0.15); color: #6c757d; }
    .history-info { flex: 1; }
    .history-info h6 { margin: 0; font-weight: 600; }
    .history-info small { color: #888; }
    .history-amount { font-weight: 700; color: #28a745; }

    .stat-box { text-align: center; padding: 20px; background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); border-radius: 15px; margin-bottom: 15px; }
    .stat-box h3 { color: var(--primary); font-weight: 700; margin-bottom: 5px; }
    .stat-box p { margin: 0; color: #666; font-size: 14px; }
    .empty-state { text-align: center; padding: 40px 20px; color: #888; }
    .empty-state i { font-size: 3rem; margin-bottom: 15px; color: #ddd; }

    .payment-modal .modal-header { background: linear-gradient(135deg, var(--primary) 0%, #e8920e 100%); }
    .payment-summary { background: #f8f9fa; border-radius: 10px; padding: 20px; margin-bottom: 20px; }
    .payment-summary-item { display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px dashed #ddd; }
    .payment-summary-item:last-child { border-bottom: none; font-weight: 700; font-size: 1.2rem; color: var(--primary); }
    .qr-code-container { text-align: center; padding: 20px; background: white; border-radius: 10px; border: 2px dashed #ddd; }
    .qr-code-container img { max-width: 200px; margin-bottom: 15px; }
    .bank-info { background: #e3f2fd; border-radius: 10px; padding: 15px; margin-top: 15px; }
    .bank-info p { margin: 5px 0; font-size: 14px; }
    .bank-info strong { color: var(--dark); }

    /* Payment History Table */
    .payment-table { width: 100%; border-collapse: collapse; background-color: transparent; }
    .payment-table thead th { background: linear-gradient(135deg, var(--primary) 0%, #e8920e 100%); color: white; padding: 12px 15px; text-align: left; font-weight: 600; border: none; }
    .payment-table thead th:first-child { border-radius: 10px 0 0 0; }
    .payment-table thead th:last-child { border-radius: 0 10px 0 0; }
    .payment-table tbody tr { background-color: #ffffff; }
    .payment-table tbody tr:nth-child(even) { background-color: #f8f9fa; }
    .payment-table tbody td { padding: 12px 15px; border-bottom: 1px solid #eee; vertical-align: middle; }

    .payment-table .type-badge { display: inline-flex; align-items: center; padding: 5px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; }
    .payment-table .type-badge.room { background: rgba(254, 161, 22, 0.15); color: var(--primary); }
    .payment-table .type-badge.electric { background: rgba(255, 193, 7, 0.15); color: #d39e00; }
    .payment-table .type-badge.water { background: rgba(23, 162, 184, 0.15); color: #17a2b8; }
    .payment-table .type-badge.other { background: rgba(108, 117, 125, 0.15); color: #6c757d; }
    .payment-table .amount-cell { font-weight: 700; color: #28a745; font-size: 1rem; }
    .payment-table .date-cell { color: #666; font-size: 13px; }
    .payment-table .action-btn { padding: 5px 12px; border-radius: 20px; font-size: 12px; }
    </style>
</head>

<body>

    <?php include('include/headd.php'); ?>

    <div class="container-fluid page-header mb-5 p-0 page-header-bg">
        <div class="container-fluid page-header-inner py-2">
            <div class="container text-center pb-5">
                <h3 class="display-3 text-white mb-3 animated slideInDown">Phòng Của Bạn
                    <span id="room-name-display">...</span>
                </h3>
            </div>
        </div>
    </div>

    <div class="container-xxl py-5">
        <div class="container">
            <div class="text-center wow fadeInUp" data-wow-delay="0.1s">
                <h6 class="section-title text-center text-primary text-uppercase">Thông tin chi tiết</h6>
                <h1 class="mb-5">Thông Tin <span class="text-primary text-uppercase">Phòng</span></h1>
            </div>

            <div id="loading-spinner" class="text-center my-5">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Đang tải...</span>
                </div>
            </div>

            <div id="room-content" style="display: none;">
                <div class="row g-4 mb-5" id="room-info"></div>

                <div class="row g-4">
                    <div class="col-12">
                        <div class="text-center wow fadeInUp" data-wow-delay="0.1s">
                            <h6 class="section-title text-center text-primary text-uppercase">Thành viên</h6>
                            <h1 class="mb-4">Danh Sách <span class="text-primary text-uppercase">Giường</span></h1>
                        </div>
                    </div>
                </div>

                <div class="row g-4" id="beds-container"></div>
            </div>
        </div>
    </div>

    <!-- Payment Section -->
    <div class="container-xxl py-5 payment-section" id="payment-section" style="display: none;">
        <div class="container">
            <div class="text-center wow fadeInUp" data-wow-delay="0.1s">
                <h6 class="section-title text-center text-primary text-uppercase">Quản lý tài chính</h6>
                <h1 class="mb-5">Thanh Toán <span class="text-primary text-uppercase">Phí KTX</span></h1>
            </div>

            <div class="row g-4">
                <!-- Thống kê thanh toán -->
                <div class="col-lg-4 wow fadeInUp" data-wow-delay="0.1s">
                    <div class="payment-card">
                        <div class="payment-card-header info">
                            <i class="fa fa-chart-pie"></i>
                            <h5 class="mb-0">Thống Kê Thanh Toán</h5>
                        </div>
                        <div class="payment-card-body" id="payment-statistics">
                            <div class="text-center">
                                <div class="spinner-border text-primary" role="status"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Các khoản phí cần đóng -->
                <div class="col-lg-8 wow fadeInUp" data-wow-delay="0.2s">
                    <div class="payment-card">
                        <div class="payment-card-header">
                            <i class="fa fa-file-invoice-dollar"></i>
                            <h5 class="mb-0">Các Khoản Phí Cần Đóng</h5>
                        </div>
                        <div class="payment-card-body" id="pending-fees">
                            <div class="text-center">
                                <div class="spinner-border text-primary" role="status"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Lịch sử thanh toán -->
            <div class="row g-4 mt-4">
                <div class="col-12 wow fadeInUp" data-wow-delay="0.3s">
                   
                    <div id="payment-history">
                        <div class="text-center">
                            <div class="spinner-border text-primary" role="status"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Payment Modal -->
    <div class="modal fade payment-modal" id="paymentModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">
                        <i class="fa fa-credit-card me-2"></i>Xác Nhận Thanh Toán
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="mb-3"><i class="fa fa-info-circle text-primary me-2"></i>Chi Tiết Thanh Toán</h6>
                            <div class="payment-summary" id="payment-summary">
                                <!-- Filled by JS -->
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h6 class="mb-3"><i class="fa fa-qrcode text-primary me-2"></i>Quét Mã QR Để Thanh Toán</h6>
                            <div class="qr-code-container">
                                <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=BANK_TRANSFER_KTX" alt="QR Code" id="payment-qr">
                                <p class="text-muted mb-0">Quét mã để chuyển khoản</p>
                            </div>
                            <div class="bank-info">
                                <p><strong>Ngân hàng:</strong> Vietcombank</p>
                                <p><strong>Số tài khoản:</strong> 1234567890</p>
                                <p><strong>Chủ tài khoản:</strong> KÝ TÚC XÁ SINH VIÊN</p>
                                <p><strong>Nội dung:</strong> <span id="payment-content">KTX_MSSV_THANG</span></p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                    <button type="button" class="btn btn-pay" id="confirm-payment-btn">
                        <i class="fa fa-check me-2"></i>Xác Nhận Đã Thanh Toán
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Payment Detail Modal -->
    <div class="modal fade" id="paymentDetailModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title fw-bold">
                        <i class="fa fa-receipt me-2"></i>Chi Tiết Giao Dịch
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4" id="payment-detail-body">
                    <!-- Filled by JS -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="roommateModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">
                        <i class="fa fa-user-circle me-2"></i>Thông Tin Bạn Cùng Phòng
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body p-4" id="modal-body-roommate">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>

                </div>
            </div>
        </div>
    </div>

    <div class="row mt-5 mb-5">
        <div class="col-12 text-center">
            <div class="card border-danger shadow-sm">
                <div class="card-body">
                    <h5 class="card-title text-danger"><i class="fa fa-sign-out-alt me-2"></i>Trả Phòng KTX</h5>
                    <p class="card-text text-muted">Bạn muốn chấm dứt hợp đồng và rời khỏi KTX? Vui lòng đảm bảo đã thanh toán tất cả các khoản phí.</p>
                    <button id="btn-checkout" class="btn btn-outline-danger px-4 py-2 rounded-pill fw-bold">
                        <i class="fa fa-paper-plane me-2"></i>Gửi Yêu Cầu Trả Phòng
                    </button>
                </div>
            </div>
        </div>
    </div>

    <?php include('include/footer.php'); ?>

    <a href="#" class="btn btn-lg btn-primary btn-lg-square back-to-top"><i class="bi bi-arrow-up"></i></a>


    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="/frontend/frontend-html/js/main.js"></script>

    <!-- Custom Scripts -->
    <script src="/frontend/frontend-html/js/auth.js"></script>
    <script src="/frontend/frontend-html/js/app.js"></script>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        loadUserSession();
        // Khởi tạo WOW.js cho animation nếu template hỗ trợ
        if (typeof WOW === 'function') new WOW().init();

        const endpoint = "../../../backend/controller/student/getRoomOfStudent.php?action=layThongTin";

        fetch(endpoint)
            .then(res => res.json())
            .then(data => {
                document.getElementById('loading-spinner').style.display = 'none';
                document.getElementById('room-content').style.display = 'block';

                if (!data.success || !data.data) {
                    document.getElementById('room-content').innerHTML = `
                            <div class="alert alert-warning text-center">
                                <i class="fa fa-exclamation-triangle fa-2x mb-3"></i><br>
                                Bạn chưa được xếp vào phòng nào hoặc không tìm thấy dữ liệu.
                            </div>`;
                    return;
                }

                const me = data.data.me;
                const room = data.data.room;
                const roommates = data.data.roommates;

                document.getElementById("room-name-display").textContent = room.room_name ?? "Không rõ";
                
                // Save room_id for utility invoices
                currentRoomId = room.room_id;

                renderRoomInfo(room);
                renderBeds(room, me, roommates);
            })
            .catch(err => {
                console.error(err);
                document.getElementById('loading-spinner').style.display = 'none';
                alert("Có lỗi xảy ra khi tải dữ liệu.");
            });
    });

    // --- Render Room Info (Style mới) ---
    function renderRoomInfo(room) {
        const container = document.getElementById("room-info");

        // Format giá tiền
        const price = new Intl.NumberFormat('vi-VN', {
            style: 'currency',
            currency: 'VND'
        }).format(room.price_per_month);

        const items = [{
                icon: 'fa-building',
                label: 'Tòa nhà',
                value: room.building
            },
            {
                icon: 'fa-layer-group',
                label: 'Tầng',
                value: room.floor
            },
            {
                icon: 'fa-users',
                label: 'Sức chứa',
                value: `${room.capacity} Người`
            },
            {
                icon: 'fa-venus-mars',
                label: 'Giới tính',
                value: room.gender_restriction
            },
            {
                icon: 'fa-money-bill-wave',
                label: 'Giá thuê',
                value: `${price}/tháng`
            },

        ];

        let html = '';
        items.forEach((item, index) => {
            html += `
                    <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="${0.1 * (index + 1)}s">
                        <div class="room-info-item">
                            <div class="room-info-icon">
                                <i class="fa ${item.icon}"></i>
                            </div>
                            <div>
                                <small class="text-muted text-uppercase">${item.label}</small>
                                <h6 class="mb-0 fw-bold">${item.value}</h6>
                            </div>
                        </div>
                    </div>
                `;
        });

        container.innerHTML = html;
    }

    // --- Render Beds (Style mới dạng Card) ---
    function renderBeds(room, me, roommates) {
        const bedsDiv = document.getElementById("beds-container");
        bedsDiv.innerHTML = "";
        const bedCount = room.capacity;
        let roommateByBed = {};

        roommates.forEach(r => roommateByBed[r.bed_number] = r);

        for (let i = 1; i <= bedCount; i++) {
            let bedTypeClass = "empty-bed";
            let badgeText = "Trống";
            let name = "Chưa có người";
            let role = "Sẵn sàng đón sinh viên";
            let icon = "fa-bed";
            let clickAttr = "";
            let delay = 0.1 * i;

            if (i == me.bed_number) {
                bedTypeClass = "my-bed";
                badgeText = "Giường của bạn";
                name = me.full_name;
                role = "Sinh viên (Bạn)";
                icon = "fa-user-check";
            } else if (roommateByBed[i]) {
                const rm = roommateByBed[i];
                bedTypeClass = "roommate-bed";
                badgeText = "Bạn cùng phòng";
                name = rm.full_name;
                role = `MSSV: ${rm.student_id}`;
                icon = "fa-user";
                // Escape single quotes safely for JSON
                const rmData = JSON.stringify(rm).replace(/'/g, "&#39;");
                clickAttr = `onclick='showRoommateDetail(${rmData})'`;
            }

            bedsDiv.innerHTML += `
                    <div class="col-lg-3 col-md-6 wow fadeInUp" data-wow-delay="${delay}s">
                        <div class="bed-card ${bedTypeClass}" ${clickAttr}>
                            <div class="bed-number-display">${i}</div>
                            <div class="bed-card-body">
                                <span class="status-badge">${badgeText}</span>
                                <div class="bed-icon-wrapper">
                                    <i class="fa ${icon}"></i>
                                </div>
                                <h5 class="user-name text-truncate">${name}</h5>
                                <p class="user-role mb-0">${role}</p>
                            </div>
                        </div>
                    </div>
                `;
        }
    }

    // --- Modal hiển thị thông tin bạn cùng phòng ---
    function showRoommateDetail(rm) {
        // Nếu truyền vào là string (do onclick HTML), parse lại
        if (typeof rm === 'string') {
            rm = JSON.parse(rm);
        }

        // Format ngày sinh cho đẹp
        const dob = new Date(rm.date_of_birth).toLocaleDateString('vi-VN');

        const html = `
                <div class="text-center mb-4">
                    <div class="mx-auto bg-light rounded-circle d-flex align-items-center justify-content-center mb-3" style="width: 100px; height: 100px;">
                        <i class="fa fa-user fa-3x text-primary"></i>
                    </div>
                    <h4 class="mb-1">${rm.full_name}</h4>
                    <p class="text-muted mb-0">${rm.student_id}</p>
                </div>
                
                <div class="modal-item"><i class="fa fa-envelope me-2"></i> <strong>Email:</strong> ${rm.email}</div>
                <div class="modal-item"><i class="fa fa-phone me-2"></i> <strong>SĐT:</strong> ${rm.phone}</div>
                <div class="modal-item"><i class="fa fa-university me-2"></i> <strong>Khoa:</strong> ${rm.faculty}</div>
                <div class="modal-item"><i class="fa fa-graduation-cap me-2"></i> <strong>Ngành:</strong> ${rm.major}</div>
                <div class="modal-item"><i class="fa fa-venus-mars me-2"></i> <strong>Giới tính:</strong> ${rm.gender}</div>
                <div class="modal-item"><i class="fa fa-birthday-cake me-2"></i> <strong>Ngày sinh:</strong> ${dob}</div>
                <div class="modal-item border-0"><i class="fa fa-bed me-2"></i> <strong>Giường số:</strong> ${rm.bed_number}</div>
            `;

        document.getElementById("modal-body-roommate").innerHTML = html;
        var myModal = new bootstrap.Modal(document.getElementById("roommateModal"));
        myModal.show();
    }

    // ==================== PAYMENT FUNCTIONS ====================
    let currentPaymentData = null;
    let currentUtilityInvoice = null;
    let studentMSSV = '';
    let currentRoomId = null;

    // Load payment data when page loads
    function loadPaymentData() {
        const paymentEndpoint = "../../../backend/controller/student/getStudentPayment.php?action=getAll";
        
        fetch(paymentEndpoint)
            .then(res => res.json())
            .then(data => {
                document.getElementById('payment-section').style.display = 'block';
                
                if (data.success && data.data) {
                    renderPaymentStatistics(data.data.statistics);
                    renderPendingFees(data.data.pending_fees, data.data.contract);
                    renderPaymentHistory(data.data.history);
                    
                    // Load utility invoices
                    if (currentRoomId) {
                        loadUtilityInvoices(currentRoomId);
                    }
                } else {
                    document.getElementById('pending-fees').innerHTML = `
                        <div class="empty-state">
                            <i class="fa fa-info-circle"></i>
                            <p>Chưa có thông tin thanh toán</p>
                        </div>`;
                    document.getElementById('payment-history').innerHTML = `
                        <div class="empty-state">
                            <i class="fa fa-history"></i>
                            <p>Chưa có lịch sử thanh toán</p>
                        </div>`;
                    document.getElementById('payment-statistics').innerHTML = `
                        <div class="empty-state">
                            <i class="fa fa-chart-pie"></i>
                            <p>Chưa có dữ liệu thống kê</p>
                        </div>`;
                }
            })
            .catch(err => {
                console.error('Error loading payment data:', err);
                document.getElementById('payment-section').style.display = 'block';
                document.getElementById('pending-fees').innerHTML = `
                    <div class="alert alert-danger">Lỗi tải dữ liệu thanh toán</div>`;
            });
    }

    // Render payment statistics
    function renderPaymentStatistics(stats) {
        const container = document.getElementById('payment-statistics');
        if (!stats) {
            container.innerHTML = `<div class="empty-state"><i class="fa fa-chart-pie"></i><p>Chưa có dữ liệu</p></div>`;
            return;
        }

        const formatMoney = (amount) => new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(amount);
        
        let html = `
            <div class="stat-box">
                <h3>${formatMoney(stats.total_amount)}</h3>
                <p>Tổng đã thanh toán</p>
            </div>
            <div class="stat-box">
                <h3>${stats.total_count || 0}</h3>
                <p>Số lần thanh toán</p>
            </div>
        `;
        
        if (stats.by_type) {
            const types = { 'Phòng': 'fa-home', 'Điện': 'fa-bolt', 'Nước': 'fa-tint', 'Khác': 'fa-tag' };
            for (const [type, data] of Object.entries(stats.by_type)) {
                html += `
                    <div class="d-flex justify-content-between align-items-center p-2 border-bottom">
                        <span><i class="fa ${types[type] || 'fa-tag'} text-primary me-2"></i>${type}</span>
                        <span class="fw-bold">${formatMoney(data.amount)}</span>
                    </div>
                `;
            }
        }

        container.innerHTML = html;
    }

    // Render pending fees (bao gồm cả hóa đơn điện/nước)
    function renderPendingFees(fees, contract) {
        const container = document.getElementById('pending-fees');
        const formatMoney = (amount) => new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(amount);
        
        let html = '';
        
        // Hiển thị các khoản phí phòng
        if (fees && fees.length > 0) {
            fees.forEach((fee, index) => {
                html += `
                    <div class="fee-item" data-index="${index}">
                        <div class="fee-info">
                            <h6><i class="fa fa-file-invoice text-primary me-2"></i>${fee.description}</h6>
                            <small><i class="fa fa-calendar-alt me-1"></i>${fee.month_display}</small>
                        </div>
                        <div class="d-flex align-items-center">
                            <span class="fee-amount me-3">${formatMoney(fee.amount)}</span>
                            <button class="btn btn-pay btn-sm" onclick='openPaymentModal(${JSON.stringify(fee).replace(/'/g, "\\'")})'>
                                <i class="fa fa-credit-card me-1"></i>Thanh toán
                            </button>
                        </div>
                    </div>
                `;
            });
        }
        
        // Placeholder cho hóa đơn điện/nước (sẽ được thêm vào sau)
        html += '<div id="utility-invoices-inline"></div>';
        
        if (html === '<div id="utility-invoices-inline"></div>') {
            container.innerHTML = `
                <div class="empty-state">
                    <i class="fa fa-check-circle text-success"></i>
                    <p>Bạn đã thanh toán đầy đủ các khoản phí!</p>
                </div>`;
        } else {
            container.innerHTML = html;
        }
    }

    // Render payment history
    function renderPaymentHistory(history) {
        const container = document.getElementById('payment-history');
        
        if (!history || history.length === 0) {
            container.innerHTML = `
                <div class="empty-state">
                    <i class="fa fa-history"></i>
                    <p>Chưa có lịch sử thanh toán</p>
                </div>`;
            return;
        }

        const formatMoney = (amount) => new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(amount);
        const formatDate = (dateStr) => new Date(dateStr).toLocaleDateString('vi-VN', { day: '2-digit', month: '2-digit', year: 'numeric' });
        const formatTime = (dateStr) => new Date(dateStr).toLocaleTimeString('vi-VN', { hour: '2-digit', minute: '2-digit' });
        
        const typeMap = { 'Phòng': 'room', 'Điện': 'electric', 'Nước': 'water', 'Khác': 'other' };
        const iconClass = { 'Phòng': 'fa-home', 'Điện': 'fa-bolt', 'Nước': 'fa-tint', 'Khác': 'fa-tag' };
        
        let html = `
            <div class="table-responsive">
                <table class="payment-table">
                    <thead>
                        <tr>
                            <th style="width: 50px;">#</th>
                            <th>Loại phí</th>
                            <th>Mô tả</th>
                            <th>Ngày thanh toán</th>
                            <th>Phương thức</th>
                            <th style="text-align: right;">Số tiền</th>
                            <th style="width: 100px; text-align: center;">Chi tiết</th>
                        </tr>
                    </thead>
                    <tbody>
        `;
        
        history.forEach((item, index) => {
            html += `
                <tr>
                    <td class="text-center">${index + 1}</td>
                    <td>
                        <span class="type-badge ${typeMap[item.payment_type] || 'other'}">
                            <i class="fa ${iconClass[item.payment_type] || 'fa-tag'} me-1"></i>
                            ${item.payment_type}
                        </span>
                    </td>
                    <td>${item.description || '-'}</td>
                    <td class="date-cell">
                        <i class="fa fa-calendar-alt me-1"></i>${formatDate(item.payment_date)}<br>
                        <small><i class="fa fa-clock me-1"></i>${formatTime(item.payment_date)}</small>
                    </td>
                    <td>
                        <span class="badge bg-info">${item.payment_method || 'Chuyển khoản'}</span>
                    </td>
                    <td class="amount-cell text-end">${formatMoney(item.amount)}</td>
                    <td class="text-center">
                        <button class="btn btn-sm btn-outline-primary action-btn" onclick="event.stopPropagation(); showPaymentDetail(${item.payment_id})">
                            <i class="fa fa-eye"></i>
                        </button>
                    </td>
                </tr>
            `;
        });
        
        html += `
                    </tbody>
                </table>
            </div>
        `;

        container.innerHTML = html;
    }

    // Open payment modal
    // function openPaymentModal(fee) {
    //     if (typeof fee === 'string') fee = JSON.parse(fee);
    //     currentPaymentData = fee;
        
    //     const formatMoney = (amount) => new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(amount);
        
    //     const summaryHtml = `
    //         <div class="payment-summary-item">
    //             <span>Loại phí:</span>
    //             <span>${fee.fee_type}</span>
    //         </div>
    //         <div class="payment-summary-item">
    //             <span>Kỳ thanh toán:</span>
    //             <span>${fee.month_display}</span>
    //         </div>
    //         <div class="payment-summary-item">
    //             <span>Mô tả:</span>
    //             <span>${fee.description}</span>
    //         </div>
    //         <div class="payment-summary-item">
    //             <span>Số tiền:</span>
    //             <span>${formatMoney(fee.amount)}</span>
    //         </div>
    //     `;
        
    //     document.getElementById('payment-summary').innerHTML = summaryHtml;
        
    //     // Generate payment content
    //     const paymentContent = `KTX_${studentMSSV}_${fee.month.replace('-', '')}`;
    //     document.getElementById('payment-content').textContent = paymentContent;
        
    //     // Generate QR code with bank info
    //     const qrData = encodeURIComponent(`Thanh toan ${fee.description} - ${paymentContent}`);
    //     document.getElementById('payment-qr').src = `https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=${qrData}`;
        
    //     const modal = new bootstrap.Modal(document.getElementById('paymentModal'));
    //     modal.show();
    // }

    // --- Thay thế hàm openPaymentModal cũ ---
    function openPaymentModal(fee) {
        if (typeof fee === 'string') fee = JSON.parse(fee);
        
        // Hỏi xác nhận trước khi chuyển hướng
        if(!confirm(`Bạn muốn thanh toán hóa đơn: ${fee.description} qua VNPAY?`)){
            return;
        }

        const btn = event.target; // Nút vừa bấm
        const originalText = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Đang chuyển hướng...';

        // Chuẩn bị dữ liệu gửi đi
        const paymentPayload = {
            payment_type: fee.fee_type,
            amount: fee.amount,
            description: fee.description,
            month: fee.month_display,
            contract_id: fee.contract_id
        };

        // Nếu là hóa đơn có sẵn (Điện/Nước đã được staff tạo)
        if (fee.payment_id) {
            paymentPayload.payment_id = fee.payment_id;
        }

        // Gọi API tạo URL VNPAY
        fetch('/backend/controller/student/StudentPaymentController.php?action=createVnpayUrl', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(paymentPayload)
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                // Chuyển hướng người dùng sang VNPAY
                window.location.href = data.payment_url;
            } else {
                alert('Lỗi: ' + (data.message || 'Không thể tạo link thanh toán'));
                btn.disabled = false;
                btn.innerHTML = originalText;
            }
        })
        .catch(err => {
            console.error(err);
            alert('Lỗi kết nối đến server.');
            btn.disabled = false;
            btn.innerHTML = originalText;
        });
    }

    // --- Xử lý khi quay lại từ VNPAY (Kiểm tra URL param) ---
    document.addEventListener('DOMContentLoaded', function() {
        // ... (các hàm init cũ của bạn) ...
        loadUserSession();
        // Check URL params for payment status
        const urlParams = new URLSearchParams(window.location.search);
        const paymentStatus = urlParams.get('payment_status');

        if (paymentStatus === 'success') {
            Swal.fire({
                icon: 'success',
                title: 'Thanh toán thành công!',
                text: 'Hệ thống đã ghi nhận thanh toán của bạn.',
                confirmButtonColor: '#FEA116'
            });
            // Xóa param khỏi URL để không hiện lại khi reload
            window.history.replaceState({}, document.title, window.location.pathname);
        } else if (paymentStatus === 'failed') {
            Swal.fire({
                icon: 'error',
                title: 'Thanh toán thất bại',
                text: 'Giao dịch đã bị hủy hoặc có lỗi xảy ra.',
                confirmButtonColor: '#d33'
            });
            window.history.replaceState({}, document.title, window.location.pathname);
        }
        
        loadPaymentData(); // Load lại dữ liệu để thấy trạng thái mới
    });

    // Confirm payment
    document.getElementById('confirm-payment-btn').addEventListener('click', function() {
        e.preventDefault();
        if (!currentPaymentData) return;
        
        const btn = this;
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Đang xử lý...';
        
        // Chuẩn bị dữ liệu thanh toán
        const paymentPayload = {
            payment_type: currentPaymentData.fee_type,
            amount: currentPaymentData.amount,
            description: currentPaymentData.description,
            month: currentPaymentData.month,
            contract_id: currentPaymentData.contract_id
        };
        
        // Nếu có payment_id (hóa đơn điện/nước do staff tạo), gửi kèm để cập nhật thay vì tạo mới
        if (currentPaymentData.payment_id) {
            paymentPayload.payment_id = currentPaymentData.payment_id;
        }
        
        fetch('../../../backend/controller/student/getStudentPayment.php?action=createPayment', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(paymentPayload)
        })
        .then(res => res.json())
        .then(data => {
            btn.disabled = false;
            btn.innerHTML = '<i class="fa fa-check me-2"></i>Xác Nhận Đã Thanh Toán';
            
            if (data.success) {
                bootstrap.Modal.getInstance(document.getElementById('paymentModal')).hide();
                alert('Thanh toán thành công! Cảm ơn bạn.');
                loadPaymentData(); // Reload data
            } else {
                alert('Lỗi: ' + (data.message || 'Không thể xử lý thanh toán'));
            }
        })
        .catch(err => {
            btn.disabled = false;
            btn.innerHTML = '<i class="fa fa-check me-2"></i>Xác Nhận Đã Thanh Toán';
            alert('Lỗi kết nối. Vui lòng thử lại.');
            console.error(err);
        });
    });

    // Show payment detail
    function showPaymentDetail(paymentId) {
        fetch(`../../../backend/controller/student/getStudentPayment.php?action=getPaymentDetail&payment_id=${paymentId}`)
            .then(res => res.json())
            .then(data => {
                if (data.success && data.data) {
                    const p = data.data;
                    const formatMoney = (amount) => new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(amount);
                    const formatDate = (dateStr) => new Date(dateStr).toLocaleDateString('vi-VN', { day: '2-digit', month: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit' });
                    
                    const html = `
                        <div class="text-center mb-4">
                            <div class="mx-auto bg-success text-white rounded-circle d-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                                <i class="fa fa-check fa-2x"></i>
                            </div>
                            <h4 class="text-success mb-1">Thanh Toán Thành Công</h4>
                            <p class="text-muted mb-0">Mã GD: #${p.payment_id}</p>
                        </div>
                        <div class="modal-item"><i class="fa fa-tag me-2"></i><strong>Loại:</strong> ${p.payment_type}</div>
                        <div class="modal-item"><i class="fa fa-money-bill me-2"></i><strong>Số tiền:</strong> ${formatMoney(p.amount)}</div>
                        <div class="modal-item"><i class="fa fa-calendar me-2"></i><strong>Ngày TT:</strong> ${formatDate(p.payment_date)}</div>
                        <div class="modal-item"><i class="fa fa-credit-card me-2"></i><strong>Phương thức:</strong> ${p.payment_method}</div>
                        <div class="modal-item border-0"><i class="fa fa-file-alt me-2"></i><strong>Mô tả:</strong> ${p.description || 'Không có'}</div>
                    `;
                    
                    document.getElementById('payment-detail-body').innerHTML = html;
                    const modal = new bootstrap.Modal(document.getElementById('paymentDetailModal'));
                    modal.show();
                }
            })
            .catch(err => console.error(err));
    }

    // ==================== UTILITY INVOICE FUNCTIONS ====================
    
    // Load utility invoices for current room
    function loadUtilityInvoices(roomId) {
        currentRoomId = roomId;
        const endpoint = `../../../backend/controller/UtilityInvoiceController.php?action=get-pending-by-room&room_id=${roomId}`;
        
        fetch(endpoint)
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    renderUtilityInvoices(data.data);
                } else {
                    document.getElementById('utility-invoices').innerHTML = `
                        <div class="empty-state">
                            <i class="fa fa-check-circle text-success"></i>
                            <p>Không có hóa đơn điện/nước chờ thanh toán</p>
                        </div>`;
                }
            })
            .catch(err => {
                console.error('Error loading utility invoices:', err);
                document.getElementById('utility-invoices').innerHTML = `
                    <div class="alert alert-danger">Lỗi tải dữ liệu hóa đơn</div>`;
            });
    }
    
    // Render utility invoices inline với các khoản phí khác
    function renderUtilityInvoices(invoices) {
        const container = document.getElementById('utility-invoices-inline');
        
        if (!container) return; // Nếu chưa có container thì bỏ qua
        
        if (!invoices || invoices.length === 0) {
            return; // Không hiển thị gì nếu không có hóa đơn
        }
        
        const formatMoney = (amount) => new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(amount);
        
        let html = '';
        invoices.forEach((invoice, index) => {
            html += `
                <div class="fee-item" data-invoice-id="${invoice.invoice_id}">
                    <div class="fee-info">
                        <h6><i class="fa fa-bolt text-warning me-2"></i>Điện/Nước tháng ${invoice.invoice_month}/${invoice.invoice_year}</h6>
                        <small><i class="fa fa-calendar-alt me-1"></i>Điện: ${invoice.electric_usage} kWh | Nước: ${invoice.water_usage} m³</small>
                    </div>
                    <div class="d-flex align-items-center">
                        <span class="fee-amount me-3">${formatMoney(invoice.total_amount)}</span>
                        <button class="btn btn-pay btn-sm" onclick="payUtilityInvoice(${invoice.invoice_id})">
                            <i class="fa fa-credit-card me-1"></i>Thanh toán
                        </button>
                    </div>
                </div>
            `;
        });
        
        container.innerHTML = html;
    }
    
    // Pay utility invoice
    function payUtilityInvoice(invoiceId) {
        if (!confirm('Xác nhận thanh toán hóa đơn điện/nước này?\n\nLưu ý: Sau khi thanh toán, hóa đơn sẽ được đánh dấu là đã thanh toán cho cả phòng.')) {
            return;
        }
        
        // Get student ID from localStorage or session
        const studentId = localStorage.getItem('userId') || sessionStorage.getItem('userId');
        
        if (!studentId) {
            alert('Không tìm thấy thông tin sinh viên. Vui lòng đăng nhập lại.');
            return;
        }
        
        const btn = event.target;
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Đang xử lý...';
        
        fetch('../../../backend/controller/UtilityInvoiceController.php?action=pay-invoice', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                invoice_id: invoiceId,
                student_id: studentId
            })
        })
        .then(res => res.json())
        .then(data => {
            btn.disabled = false;
            btn.innerHTML = '<i class="fa fa-credit-card me-1"></i>Thanh toán';
            
            if (data.status === 'success') {
                alert('Thanh toán thành công!\n\nHóa đơn đã được thanh toán cho cả phòng.');
                // Reload data
                if (currentRoomId) {
                    loadUtilityInvoices(currentRoomId);
                }
                loadPaymentData();
            } else {
                alert('Lỗi: ' + (data.message || 'Không thể xử lý thanh toán'));
            }
        })
        .catch(err => {
            btn.disabled = false;
            btn.innerHTML = '<i class="fa fa-credit-card me-1"></i>Thanh toán';
            alert('Lỗi kết nối. Vui lòng thử lại.');
            console.error(err);
        });
    }

    // Biến lưu trạng thái hiện tại
    let isPendingCheckout = false;

    // Nút Checkout
    const checkoutBtn = document.getElementById('btn-checkout');

    // 1. Kiểm tra trạng thái hợp đồng khi load trang
    function checkContractStatus() {
        fetch('../../../backend/controller/student/StudentPaymentController.php?action=getContract')
        .then(res => res.json())
        .then(data => {
            if(data.success && data.data) {
                const status = data.data.status;
                
                if(status === 'Chờ duyệt trả phòng') {
                    isPendingCheckout = true;
                    // Đổi giao diện nút thành nút Hủy yêu cầu
                    checkoutBtn.innerHTML = '<i class="fa fa-undo me-2"></i>Hủy yêu cầu trả phòng';
                    checkoutBtn.classList.remove('btn-outline-danger');
                    checkoutBtn.classList.add('btn-warning');
                    
                    // Cập nhật text hướng dẫn
                    checkoutBtn.previousElementSibling.textContent = "Bạn đã gửi yêu cầu trả phòng. Nhấn nút dưới đây nếu muốn hủy yêu cầu và tiếp tục ở.";
                } else {
                    isPendingCheckout = false;
                    // Reset về nút trả phòng
                    checkoutBtn.innerHTML = '<i class="fa fa-paper-plane me-2"></i>Gửi Yêu Cầu Trả Phòng';
                    checkoutBtn.classList.remove('btn-warning');
                    checkoutBtn.classList.add('btn-outline-danger');
                }
            }
        });
    }

    // 2. Xử lý sự kiện Click
    checkoutBtn.addEventListener('click', function() {
        if (this.disabled) return;

        if (isPendingCheckout) {
            // LOGIC HỦY YÊU CẦU
            Swal.fire({
                title: 'Hủy yêu cầu trả phòng?',
                text: "Hợp đồng sẽ quay lại trạng thái Hiệu lực.",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#f0ad4e',
                confirmButtonText: 'Đồng ý hủy',
                cancelButtonText: 'Không'
            }).then((result) => {
                if (result.isConfirmed) {
                    performAction('cancelCheckout');
                }
            });
        } else {
            // LOGIC GỬI YÊU CẦU (Kiểm tra nợ)
            Swal.fire({
                title: 'Xác nhận trả phòng?',
                text: "Hệ thống sẽ kiểm tra công nợ. Nếu đủ điều kiện, yêu cầu sẽ được gửi đi.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                confirmButtonText: 'Gửi yêu cầu',
                cancelButtonText: 'Thoát'
            }).then((result) => {
                if (result.isConfirmed) {
                    performAction('requestCheckout');
                }
            });
        }
    });

    // Hàm gọi API chung
    function performAction(actionName) {
        const originalText = checkoutBtn.innerHTML;
        checkoutBtn.disabled = true;
        checkoutBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Đang xử lý...';

        fetch(`../../../backend/controller/student/StudentPaymentController.php?action=${actionName}`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' }
        })
        .then(res => res.json())
        .then(data => {
            checkoutBtn.disabled = false;
            
            if (data.success) {
                Swal.fire('Thành công!', data.message, 'success').then(() => {
                    // Reload lại trạng thái để cập nhật nút và hiển thị đúng
                    location.reload(); 
                });
            } else {
                checkoutBtn.innerHTML = originalText;
                Swal.fire('Thất bại', data.message, 'error');
            }
        })
        .catch(err => {
            console.error(err);
            checkoutBtn.disabled = false;
            checkoutBtn.innerHTML = originalText;
            Swal.fire('Lỗi', 'Lỗi kết nối server', 'error');
        });
    }

    // Khởi chạy khi load trang
    checkContractStatus();

    // Load payment data after room data loaded
    setTimeout(loadPaymentData, 500);
    </script>
</body>

</html>