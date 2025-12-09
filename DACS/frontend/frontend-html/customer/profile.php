<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hotelier - My Profile</title>
    
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
    <link href="/frontend/frontend-html/css/profile.css" rel="stylesheet">
</head>
<body>
    
  <?php include('include/headd.php'); ?>

    <!-- Profile Content -->
    <div class="container-xxl py-5">
        <div class="container">
            <div class="text-center wow fadeInUp" data-wow-delay="0.1s">
                <h6 class="section-title text-center text-primary text-uppercase">Account</h6>
                <h1 class="mb-5">My <span class="text-primary text-uppercase">Profile</span></h1>
            </div>
            
            <div class="row g-5">
                <!-- Profile Info -->
                <div class="col-lg-4">
                    <div class="card border-0 shadow">
                        <div class="card-body text-center">
                            <img id="userAvatar" src="https://ui-avatars.com/api/?name=User&background=667eea&color=fff" 
                                 class="rounded-circle mb-3" style="width: 150px; height: 150px;" alt="Profile">
                            <h4 id="userName">User Name</h4>
                            <p id="userEmail" class="text-muted">user@example.com</p>
                            <hr>
                            <div class="d-grid gap-2">
                                <a href="/frontend/dondangky" class="btn btn-primary">
                                    <i class="fas fa-calendar me-2"></i>My Bookings
                                </a>
                                <!-- <button class="btn btn-outline-danger" onclick="handleLogout()">
                                    <i class="fas fa-sign-out-alt me-2"></i>Logout
                                </button> -->
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Profile Form -->
                <div class="col-lg-8">
                    <div class="card border-0 shadow">
<div class="card-body">
                            <h5 class="card-title mb-3">Profile Information</h5>
                            <div class="alert alert-info mb-4" role="alert">
                                <i class="fas fa-info-circle me-2"></i>
                                <strong>Lưu ý:</strong> Vui lòng điền đầy đủ các thông tin có dấu <span class="text-danger">*</span> để có thể đăng ký phòng.
                            </div>
                            <form id="profileForm">
                                <input type="hidden" id="userId">
                                <div class="row g-3">
                                    <!-- Thông tin User -->
                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <input type="text" class="form-control" id="fullName" required>
                                            <label>Họ Tên <span class="text-danger">*</span></label>
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
                                    
                                    <!-- Mã Sinh Viên - thẳng hàng với Số Điện Thoại -->
                                    <div class="col-md-6" id="studentIdField" style="display: none;">
                                        <div class="form-floating">
                                            <input type="text" class="form-control" id="studentId" required>
                                            <label>Mã Sinh Viên <span class="text-danger">*</span></label>
                                        </div>
                                    </div>
                                    
                                    <!-- Thông tin Student (chỉ hiển thị nếu role = student) -->
                                    <div id="studentFields" style="display: none;">
                                        <div class="col-md-6">
                                            <div class="form-floating">
<input type="text" class="form-control" id="faculty" required>
                                                <label>Khoa <span class="text-danger">*</span></label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-floating">
                                                <input type="text" class="form-control" id="major" required>
                                                <label>Ngành <span class="text-danger">*</span></label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-floating">
                                                <select class="form-select" id="gender" required>
                                                    <option value="">Chọn giới tính</option>
                                                    <option value="Nam">Nam</option>
                                                    <option value="Nữ">Nữ</option>
                                                    <option value="Khác">Khác</option>
                                                </select>
                                                <label>Giới Tính <span class="text-danger">*</span></label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-floating">
                                                <input type="date" class="form-control" id="dateOfBirth" required>
                                                <label>Ngày Sinh <span class="text-danger">*</span></label>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="form-floating">
                                                <textarea class="form-control" id="address" style="height: 100px" required></textarea>
                                                <label>Địa Chỉ <span class="text-danger">*</span></label>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-12">
                                        <button class="btn btn-primary w-100 py-3" type="submit">
                                            <i class="fas fa-save me-2"></i>Lưu Thay Đổi
                                        </button>
                                    </div>
                                </div>
</form>
                            
                            <hr class="my-4">
                            
                            <!-- Change Password Form -->
                            <h5 class="card-title mb-4">Đổi Mật Khẩu</h5>
                            <form id="changePasswordForm">
                                <div class="row g-3">
                                    <div class="col-12">
                                        <div class="form-floating">
                                            <input type="password" class="form-control" id="oldPassword" required>
                                            <label>Mật Khẩu Cũ <span class="text-danger">*</span></label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <input type="password" class="form-control" id="newPassword" required>
                                            <label>Mật Khẩu Mới <span class="text-danger">*</span></label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <input type="password" class="form-control" id="confirmPassword" required>
                                            <label>Xác Nhận Mật Khẩu <span class="text-danger">*</span></label>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <button class="btn btn-warning w-100 py-3" type="submit">
                                            <i class="fas fa-key me-2"></i>Đổi Mật Khẩu
                                        </button>
                                    </div>
                                </div>
                            </form>
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
        let userId = null;
        
        document.addEventListener('DOMContentLoaded', async function() {
            loadUserSession();
    
            // 2. Gọi hàm và ĐỢI nó chạy xong bằng 'await' (Thay thế cho setTimeout)
            await loadUserSessionAsync();

            // 3. Lúc này code đã CHẮC CHẮN có dữ liệu (hoặc null), không lo bị lỗi
            if (currentUser && currentUser.profile && currentUser.profile.user_id) {
                userId = currentUser.profile.user_id; // Lấy ID từ kết quả vừa load
                console.log('✅ Đã xác thực người dùng:', userId);
                loadProfile(); // Tiến hành load form thông tin
            } else {
                // Trường hợp chưa đăng nhập
                console.warn('⛔ Chưa đăng nhập hoặc không lấy được session');
                alert('Vui lòng đăng nhập để xem thông tin cá nhân');
                window.location.href = '/LoginDarkSunSet/login.php';
            }

        });
        // Hàm load session người dùng (phiên bản async/await)
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
        
        // Load profile from backend
        function loadProfile() {
            fetch(`/backend/profile.php?action=get-profile&user_id=${userId}`, {
                headers: {'X-Requested-With': 'XMLHttpRequest'}
            })
            .then(res => res.json())
            .then(result => {
                if (result.success && result.data) {
                    const data = result.data;
                    
                    // Display user info
                    document.getElementById('userName').textContent = data.full_name || data.username;
                    document.getElementById('userEmail').textContent = data.email || '';
                    
                    // Update avatar
                    const avatarName = encodeURIComponent(data.full_name || data.username);
document.getElementById('userAvatar').src = `https://ui-avatars.com/api/?name=${avatarName}&background=667eea&color=fff`;
                    
                    // Pre-fill form
                    document.getElementById('userId').value = data.user_id;
                    document.getElementById('fullName').value = data.full_name || '';
                    document.getElementById('email').value = data.email || '';
                    document.getElementById('phone').value = data.phone || '';
                    
                    // Show student fields if role is student
                    if (data.role === 'student') {
                        document.getElementById('studentIdField').style.display = 'block';
                        document.getElementById('studentFields').style.display = 'contents';
                        document.getElementById('studentId').value = data.student_id || '';
                        document.getElementById('faculty').value = data.faculty || '';
                        document.getElementById('major').value = data.major || '';
                        document.getElementById('gender').value = data.gender || '';
                        document.getElementById('dateOfBirth').value = data.date_of_birth || '';
                        document.getElementById('address').value = data.address || '';
                    }
                } else {
                    alert(result.message || 'Không thể tải thông tin');
                }
            })
            .catch(err => {
                console.error('Error loading profile:', err);
                alert('Không thể tải thông tin. Vui lòng thử lại.');
            });
        }
        
        // Handle profile update
        document.getElementById('profileForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = {
                user_id: userId,
                full_name: document.getElementById('fullName').value,
                email: document.getElementById('email').value,
                phone: document.getElementById('phone').value,
                student_id: document.getElementById('studentId').value,
                faculty: document.getElementById('faculty').value,
                major: document.getElementById('major').value,
                gender: document.getElementById('gender').value,
                date_of_birth: document.getElementById('dateOfBirth').value,
                address: document.getElementById('address').value
            };
            
            fetch('/backend/profile.php?action=update-profile', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify(formData)
            })
            .then(res => res.json())
            .then(result => {
if (result.success) {
                    alert('✅ ' + result.message);
                    loadProfile(); // Reload profile
                } else {
                    alert('❌ ' + result.message);
                }
            })
            .catch(err => {
                console.error('Error:', err);
                alert('❌ Có lỗi xảy ra. Vui lòng thử lại.');
            });
        });
        
        // Handle password change
        document.getElementById('changePasswordForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const oldPassword = document.getElementById('oldPassword').value;
            const newPassword = document.getElementById('newPassword').value;
            const confirmPassword = document.getElementById('confirmPassword').value;
            
            if (newPassword !== confirmPassword) {
                alert('❌ Mật khẩu xác nhận không khớp!');
                return;
            }
            
            if (newPassword.length < 6) {
                alert('❌ Mật khẩu mới phải có ít nhất 6 ký tự!');
                return;
            }
            
            const formData = {
                user_id: userId,
                old_password: oldPassword,
                new_password: newPassword
            };
            
            fetch('/backend/profile.php?action=change-password', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify(formData)
            })
            .then(res => res.json())
            .then(result => {
                if (result.success) {
                    alert('✅ ' + result.message);
                    document.getElementById('changePasswordForm').reset();
                } else {
                    alert('❌ ' + result.message);
                }
            })
            .catch(err => {
                console.error('Error:', err);
                alert('❌ Có lỗi xảy ra. Vui lòng thử lại.');
            });
        });
    </script>
</body>
</html>
