<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hotelier - Checkout</title>
    
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
    <link href="/frontend/frontend-html/css/checkin.css" rel="stylesheet">
</head>
<body>
   <?php include('include/headd.php'); ?>
    <!-- Checkout Content -->
    <div class="container-xxl py-5 bill-information">
        <div class="container">
            <div class="text-center wow fadeInUp" data-wow-delay="0.1s">
                <h6 class="section-title text-center text-primary text-uppercase">Đăng Ký Phòng</h6>
                <h1 class="mb-5">Đăng Ký <span class="text-primary text-uppercase" id="roomName">Ký Túc Xá</span></h1>
            </div>
            <div class="row g-5">
                <!-- Booking Form -->
                <div class="col-lg-7">
                    <form id="checkoutForm" class="overall-payment-form">
                        <input type="hidden" id="roomId">
                        
                        <h5 class="mb-3"><i class="bi bi-person"></i> Thông Tin Sinh Viên</h5>
                        <div class="row g-3 payment-form mb-4">
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="fullName">
                                    <label>Họ Tên</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="email" class="form-control" id="email" required>
                                    <label>Email <span class="text-danger">*</span></label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="tel" class="form-control" id="phone" required>
                                    <label>Số Điện Thoại <span class="text-danger">*</span></label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="faculty">
                                    <label>Khoa</label>
                                </div>
                            </div>
                        </div>
                        
                        <h5 class="mb-3 mt-4 pt-3 border-top"><i class="bi bi-calendar"></i> Thời Gian Ở</h5>
                        <div class="row g-3 payment-form">
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="date" class="form-control" id="startDate" required>
                                    <label>Ngày Bắt Đầu <span class="text-danger">*</span></label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="date" class="form-control" id="endDate" required>
                                    <label>Ngày Kết Thúc <span class="text-danger">*</span></label>
                                </div>
                            </div>
                        </div>
                        
                        <h5 class="mb-3 mt-4 pt-3 border-top"><i class="bi bi-bed"></i> Chọn Giường <span class="text-danger">*</span></h5>
                        <div class="row g-3 payment-form mb-4">
                            <div class="col-12">
                                <div id="bedSelection">
                                    <p class="text-muted">Vui lòng đợi, đang tải danh sách giường...</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row g-3 payment-form">
                            <div class="col-12">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="agreeTerms" required>
                                    <label class="form-check-label" for="agreeTerms">
                                        Tôi đồng ý với <a href="#" class="text-primary">Điều khoản và Quy định</a> của ký túc xá <span class="text-danger">*</span>
                                    </label>
                                </div>
                            </div>
                            <div class="col-12">
                                <button class="btn btn-primary w-100 py-3" type="submit">Gửi Đăng Ký</button>
                            </div>
                        </div>
                    </form>
                </div>
                
                <!-- Room Summary -->
                <div class="col-lg-5">
                    <div class="card border-0 shadow">
                        <div class="card-body">
                            <h5 class="card-title mb-4"><i class="bi bi-info-circle"></i> Thông Tin Phòng</h5>
                            <div id="roomSummary">
                                <div class="text-center text-muted">
                                    <div class="spinner-border" role="status"></div>
                                    <p class="mt-2">Đang tải...</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

   <?php include('include/footer.php'); ?>
   
    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="/frontend/frontend-html/js/main.js"></script>
    <script src="/frontend/frontend-html/js/auth.js"></script>
    <script src="/frontend/frontend-html/js/app.js"></script>
    
    <script>
         let roomData = null;
         let availableBeds = [];
         let studentInfo = null;
         
         // Lấy room_id và user_id từ URL
        //  const urlParams = new URLSearchParams(window.location.search);
        //  const roomId = urlParams.get('room');
        //  const userId = urlParams.get('user_id'); // Có thể null nếu không có
        const roomId = '<?php echo isset($_GET["room"]) ? $_GET["room"] : ""; ?>';
        const userId = '<?php echo isset($_GET["user_id"]) ? $_GET["user_id"] : ""; ?>';
        
        document.addEventListener('DOMContentLoaded', function() {
            loadUserSession();
            if (!roomId) {
                alert('Không tìm thấy thông tin phòng!');
                window.history.back();
                return;
            }
            
            document.getElementById('roomId').value = roomId;
            
            // Load dữ liệu
            loadStudentInfo();
            loadRoomDetails();
            
            // Set ngày tối thiểu
            const today = new Date().toISOString().split('T')[0];
            document.getElementById('startDate').setAttribute('min', today);
            document.getElementById('startDate').value = today;
            
            const nextMonth = new Date();
            nextMonth.setMonth(nextMonth.getMonth() + 6);
            document.getElementById('endDate').setAttribute('min', nextMonth.toISOString().split('T')[0]);
            document.getElementById('endDate').value = nextMonth.toISOString().split('T')[0];
        });
        
         // Load thông tin sinh viên
         function loadStudentInfo() {
             // Nếu không có user_id, để trống form
             if (!userId) {
                 console.log('📝 Không có user_id, vui lòng điền thông tin thủ công');
                return;
             }
             
             console.log('🔄 Đang load thông tin sinh viên với user_id:', userId);
             
             fetch(`../../../backend/booking.php?action=get-student-info&user_id=${userId}`, {
                 headers: {'X-Requested-With': 'XMLHttpRequest'}
             })
             .then(res => {
                 console.log('📡 Response status:', res.status);
                 return res.json();
             })
             .then(result => {
                 console.log('📦 Response data:', result);
                 
                 if (result.success && result.data) {
                    studentInfo = result.data;
                    console.log('✅ Thông tin sinh viên:', studentInfo);
                    
                    // Tự động điền thông tin nếu có và chỉ khóa các trường CÓ dữ liệu
                    if (studentInfo.full_name && studentInfo.full_name.trim() !== '') {
                        const fullNameInput = document.getElementById('fullName');
                        fullNameInput.value = studentInfo.full_name;
                        fullNameInput.readOnly = true;
                        fullNameInput.classList.add('bg-light');
                        console.log('✏️ Đã điền và khóa Họ Tên');
                    } else {
                        console.log('📝 Họ Tên trống - cho phép nhập');
                    }
                    
                    if (studentInfo.email && studentInfo.email.trim() !== '') {
                        const emailInput = document.getElementById('email');
                        emailInput.value = studentInfo.email;
                        emailInput.readOnly = true;
                        emailInput.classList.add('bg-light');
                        console.log('✏️ Đã điền và khóa Email');
                    } else {
                        console.log('📝 Email trống - cho phép nhập');
                    }
                    
                    if (studentInfo.phone && studentInfo.phone.trim() !== '') {
                        const phoneInput = document.getElementById('phone');
                        phoneInput.value = studentInfo.phone;
                        phoneInput.readOnly = true;
                        phoneInput.classList.add('bg-light');
                        console.log('✏️ Đã điền và khóa SĐT');
                    } else {
                        console.log('📝 SĐT trống - cho phép nhập');
                    }
                    
                    if (studentInfo.faculty && studentInfo.faculty.trim() !== '') {
                        const facultyInput = document.getElementById('faculty');
                        facultyInput.value = studentInfo.faculty;
                        facultyInput.readOnly = true;
                        facultyInput.classList.add('bg-light');
                        console.log('✏️ Đã điền và khóa Khoa');
                    } else {
                        console.log('📝 Khoa trống - cho phép nhập');
                    }
                } else {
                     // Nếu không có thông tin, để trống cho người dùng điền
                     console.warn('⚠️ Không lấy được thông tin:', result.message);
                     console.log('📝 Vui lòng điền thông tin thủ công');
                 }
             })
             .catch(err => {
                 console.error('❌ Error loading student info:', err);
                 console.log('📝 Vui lòng điền thông tin thủ công');
             });
         }
        
        // Load thông tin phòng và giường
        function loadRoomDetails() {
            fetch(`../../../backend/booking.php?action=get-room-details&room_id=${roomId}`, {
                headers: {'X-Requested-With': 'XMLHttpRequest'}
            })
            .then(res => res.json())
            .then(result => {
                if (result.success) {
                    roomData = result.data.room;
                    availableBeds = result.data.beds;
                    displayRoomInfo();
                    displayBeds();
                } else {
                    alert(result.message);
                    window.history.back();
                }
            })
            .catch(err => {
                console.error('Error:', err);
                alert('Không thể tải thông tin phòng');
            });
        }
        
        // Hiển thị thông tin phòng
        function displayRoomInfo() {
            document.getElementById('roomSummary').innerHTML = `
                <div class="mb-3 pb-3 border-bottom">
                    <div class="d-flex justify-content-between">
                        <span><i class="bi bi-building"></i> Khu nhà:</span>
                        <strong>${roomData.building}</strong>
                    </div>
                </div>
                <div class="mb-3 pb-3 border-bottom">
                    <div class="d-flex justify-content-between">
                        <span><i class="bi bi-layers"></i> Tầng:</span>
                        <strong>${roomData.floor}</strong>
                    </div>
                </div>
                <div class="mb-3 pb-3 border-bottom">
                    <div class="d-flex justify-content-between">
                        <span><i class="bi bi-people"></i> Sức chứa:</span>
                        <strong>${roomData.capacity} người</strong>
                    </div>
                </div>
                <div class="mb-3 pb-3 border-bottom">
                    <div class="d-flex justify-content-between">
                        <span><i class="bi bi-door-open"></i> Còn trống:</span>
                        <strong class="text-success">${roomData.available_beds} giường</strong>
                    </div>
                    </div>
                <div class="mb-3 pb-3 border-bottom">
                    <div class="d-flex justify-content-between">
                        <span><i class="bi bi-gender-ambiguous"></i> Giới hạn:</span>
                        <strong>${getGenderBadge(roomData.gender_restriction)}</strong>
                    </div>
                </div>
                <div class="mb-3">
                    <div class="d-flex justify-content-between">
                        <span><i class="bi bi-cash"></i> Giá thuê:</span>
                        <h5 class="text-primary mb-0">${formatMoney(roomData.price_per_month)} VNĐ/tháng</h5>
                    </div>
                </div>
            `;
        }
        
        // Hiển thị danh sách giường trống để người dùng chọn
        let selectedBedId = null;
        
        function displayBeds() {
            if (!roomId) {
                document.getElementById('bedSelection').innerHTML = 
                    '<p class="text-muted">Vui lòng chọn phòng trước</p>';
                return;
            }
            
            // Gọi API lấy danh sách giường trống
            fetch(`../../../backend/booking.php?action=get-available-beds&room_id=${roomId}`)
                .then(res => res.json())
                .then(data => {
                    const bedContainer = document.getElementById('bedSelection');
                    
                    if (!data || data.length === 0) {
                        bedContainer.innerHTML = 
                            '<div class="alert alert-warning">Phòng này hiện không còn giường trống</div>';
                        return;
                    }
                    
                    // Tạo danh sách giường dưới dạng radio buttons
                    let bedsHTML = '<div class="row g-2">';
                    data.forEach(bed => {
                        bedsHTML += `
                            <div class="col-6 col-md-4">
                                <input type="radio" class="btn-check" name="bedOption" id="bed${bed.bed_id}" value="${bed.bed_id}">
                                <label class="btn btn-outline-primary w-100" for="bed${bed.bed_id}">
                                    <i class="bi bi-bed"></i> Giường ${bed.bed_number}
                                </label>
                            </div>
                        `;
                    });
                    bedsHTML += '</div>';
                    bedsHTML += '<p class="text-muted mt-2 small"><i class="bi bi-info-circle"></i> Vui lòng chọn một giường trống</p>';
                    
                    bedContainer.innerHTML = bedsHTML;
                    
                    // Lắng nghe sự kiện chọn giường
                    document.querySelectorAll('input[name="bedOption"]').forEach(radio => {
                        radio.addEventListener('change', function() {
                        selectedBedId = parseInt(this.value);
                            console.log('Đã chọn giường:', selectedBedId);
                        });
                    });
                })
                .catch(error => {
                    console.error('Lỗi khi tải giường:', error);
                    document.getElementById('bedSelection').innerHTML = 
                        '<div class="alert alert-danger">Không thể tải danh sách giường</div>';
                });
        }
        
        // Submit form
        document.getElementById('checkoutForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Validate thông tin sinh viên (chỉ bắt buộc email và phone)
            const fullName = document.getElementById('fullName').value.trim();
            const email = document.getElementById('email').value.trim();
            const phone = document.getElementById('phone').value.trim();
            const faculty = document.getElementById('faculty').value.trim();
            
            if (!email || !phone) {
                alert('Vui lòng điền Email và Số điện thoại!');
                return;
            }
            
            // Validate ngày
            const startDate = document.getElementById('startDate').value;
            const endDate = document.getElementById('endDate').value;
            
            if (!startDate || !endDate) {
                alert('Vui lòng chọn ngày bắt đầu và kết thúc!');
                return;
            }
            
            // Validate giường đã chọn
            if (!selectedBedId) {
                alert('Vui lòng chọn một giường trống!');
                return;
            }
            
            // Validate checkbox đồng ý
            const agreeTerms = document.getElementById('agreeTerms').checked;
            if (!agreeTerms) {
                alert('Vui lòng đồng ý với điều khoản và quy định!');
                return;
            }
            
            const formData = {
                room_id: roomId,
                bed_id: selectedBedId,
                start_date: startDate,
                end_date: endDate,
                user_id: userId ? parseInt(userId) : null, // Gửi user_id nếu có, null nếu không
                // Gửi thông tin sinh viên (bắt buộc)
                full_name: fullName,
                email: email,
                phone: phone,
                faculty: faculty
            };
            
            console.log('📤 Đang gửi đăng ký:', formData);
            
            // Hiển thị loading
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Đang xử lý...';
            fetch('../../../backend/booking.php?action=create-registration', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify(formData)
            })
            .then(res => res.json())
            .then(result => {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
                
                if (result.success) {
                    alert('✅ ' + result.message);
                    // Redirect về trang profile hoặc danh sách đăng ký
                    window.location.href = '/backend/auth.php?'; // Hoặc trang khác theo yêu cầu
                } else {
                    alert('❌ ' + result.message);
                }
            })
            .catch(err => {
                console.error('Error:', err);
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
                alert('❌ Có lỗi xảy ra khi gửi đăng ký. Vui lòng thử lại!');
            });
        });
        
        // Helper functions
        function getGenderBadge(gender) {
            if (!gender || gender === null || gender === '') {
                return '<span class="badge bg-secondary">Không giới hạn</span>';
            }
            const badges = {
                'Nam': '<span class="badge bg-info">Nam</span>',
                'Nữ': '<span class="badge" style="background-color: #e91e63;">Nữ</span>'
            };
            return badges[gender] || '<span class="badge bg-secondary">Không giới hạn</span>';
        }
        
        function formatMoney(amount) {
            return new Intl.NumberFormat('vi-VN').format(amount);
        }
    </script>
 
</html>
