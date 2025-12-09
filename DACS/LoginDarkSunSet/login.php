<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Login & Register - Wizard Magazine</title>
   <link rel="stylesheet" href="../LoginDarkSunSet/style.css">
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
   <link rel="stylesheet" href="https://pro.fontawesome.com/releases/v5.10.0/css/all.css" integrity="sha384-AYmEC3Yw5cVb3ZcuHtOA93w35dYTsvhLPVnYs9eStHfGJvOvKxVfELGroGkvsg+p" crossorigin="anonymous"/>
   <link rel="preconnect" href="https://fonts.googleapis.com">
   <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
   <link href="https://fonts.googleapis.com/css2?family=Spartan:wght@300;400;500;600&display=swap" rel="stylesheet">
   <link href="https://fonts.googleapis.com/css2?family=Kaushan+Script&display=swap" rel="stylesheet">
   <style>
      .error-message {
         background-color: #f8d7da;
         color: #721c24;
         padding: 12px;
         border-radius: 4px;
         margin-bottom: 15px;
         border: 1px solid #f5c6cb;
         display: none;
      }
      .success-message {
         background-color: #d4edda;
         color: #155724;
         padding: 12px;
         border-radius: 4px;
         margin-bottom: 15px;
         border: 1px solid #c3e6cb;
         display: none;
      }
      .form-submit {
         cursor: pointer;
      }
      .form-submit:disabled {
         opacity: 0.6;
         cursor: not-allowed;
      }
   </style>
</head>
<body>
   <div class="overlay"></div>
   <div class="login-container">
      <div>
         <div class="logo">
            <i class="fas fa-hat-wizard"></i>
            <span>Wizard Magazine</span>
         </div>
         <div class="register">
            <div id="sidebar-title">Don't have an account?</div>
            <p id="sidebar-description">Register to access all the features of our services. Manage your business in one place. It's free</p>
            <div class="social">
               <a data-toggle="tooltip" title="Facebook" href=""><i class="fab fa-facebook-f"></i></a>
               <a data-toggle="tooltip" title="Google" href=""><i class="fab fa-google"></i></a>
               <a data-toggle="tooltip" title="Pinterest" href=""><i class="fab fa-pinterest-p"></i></a>
               <a data-toggle="tooltip" title="Github" href=""><i class="fab fa-github"></i></a>
            </div>
         </div>
      </div>

      <div class="form-login">
         <!-- Form đăng nhập -->
         <form class="form" id="form-login">
            <h3 class="heading">Sign in</h3>
            
            <div id="login-message"></div>
            
            <div class="spacer"></div>
      
            <div class="form-group">
              <label for="login-email" class="form-label">Email</label>
              <input id="login-email" name="email" type="text" placeholder="VD: email@domain.com" class="form-control">
              <span class="form-message"></span>
            </div>
      
            <div class="form-group">
              <label for="login-password" class="form-label">Password</label>
              <input id="login-password" name="password" type="password" placeholder="Enter password" class="form-control">
              <span class="form-message"></span>
            </div>
      
            <div class="sign-up" style="display: flex; align-items: center;">
               <div>
                  <button type="submit" class="form-submit">Sign in</button>
                  <i class="fas fa-chevron-right"></i>
               </div>
               <a href="javascript:void(0)" onclick="switchToRegister()" style="margin-left: 35px;">Don't have an account?</a>
            </div>
         </form>

         <!-- Form đăng ký (ẩn mặc định) -->
         <form class="form" id="form-register" style="display: none;">
            <h3 class="heading">Sign up</h3>
            
            <div id="register-message"></div>
            
            <div class="spacer"></div>
            
            <div class="form-group">
              <label for="reg-username" class="form-label">Username</label>
              <input id="reg-username" name="username" type="text" placeholder="Enter username" class="form-control">
              <span class="form-message"></span>
            </div>
            
            <div class="form-group">
              <label for="reg-email" class="form-label">Email</label>
              <input id="reg-email" name="email" type="text" placeholder="Enter email" class="form-control">
              <span class="form-message"></span>
            </div>
            
            <div class="form-group">
              <label for="reg-phone" class="form-label">Phone</label>
              <input id="reg-phone" name="phone" type="text" placeholder="Enter phone number" class="form-control">
              <span class="form-message"></span>
            </div>
      
            <div class="form-group">
              <label for="reg-password" class="form-label">Password</label>
              <input id="reg-password" name="password" type="password" placeholder="Enter password" class="form-control">
              <span class="form-message"></span>
            </div>
            
            <div class="form-group">
              <label for="password-confirmation" class="form-label">Confirm Password</label>
              <input id="password-confirmation" name="password_confirmation" type="password" placeholder="Confirm password" class="form-control">
              <span class="form-message"></span>
            </div>

            <div class="form-group f-term">
               <input id="agree" name="agree" type="checkbox" class="form-control">
               <label for="agree" class="form-label">I agree to the all statements in <a href="">Terms of service</a></label>               
               <span class="form-message"></span>
            </div>
      
            <div class="sign-up">
               <div>
                  <button type="submit" class="form-submit">Sign up</button>
                  <i class="fas fa-chevron-right"></i>
               </div>
               <a href="javascript:void(0)" onclick="switchToLogin()">Have an account?</a>
            </div>
         </form>
      </div>
   </div>

   <script src="../LoginDarkSunSet/validate.js"></script>
   <script>
      // Biến lưu trữ validator instances
      let loginValidator = null;
      let registerValidator = null;

      // Khởi tạo validators
      function initValidators() {
         // Validator cho form đăng nhập
         loginValidator = Validator({
            form: '#form-login',
            formGroupSelector: '.form-group',
            errorSelector: '.form-message',
            rules: [
               Validator.isRequired('#login-email', 'Vui lòng nhập email'),
               Validator.isEmail('#login-email', 'Email không hợp lệ'),
               Validator.isRequired('#login-password', 'Vui lòng nhập mật khẩu'),
               Validator.minLength('#login-password', 6, 'Mật khẩu phải có ít nhất 6 ký tự')
            ],
            onSubmit: handleLogin
         });

         // Validator cho form đăng ký
         registerValidator = Validator({
            form: '#form-register',
            formGroupSelector: '.form-group',
            errorSelector: '.form-message',
            rules: [
               Validator.isRequired('#reg-username', 'Vui lòng nhập username'),
               Validator.minLength('#reg-username', 5, 'Username phải có ít nhất 5 ký tự'),
               Validator.isRequired('#reg-email', 'Vui lòng nhập email'),
               Validator.isEmail('#reg-email', 'Email không hợp lệ'),
               Validator.isRequired('#reg-phone', 'Vui lòng nhập số điện thoại'),
               Validator.isRequired('#reg-password', 'Vui lòng nhập mật khẩu'),
               Validator.minLength('#reg-password', 6, 'Mật khẩu phải có ít nhất 6 ký tự'),
               Validator.isRequired('#password-confirmation', 'Vui lòng xác nhận mật khẩu'),
               Validator.isConfirmed('#password-confirmation', function() {
                  return document.querySelector('#reg-password').value;
               }, 'Mật khẩu xác nhận không khớp'),
               Validator.isRequired('#agree', 'Vui lòng đồng ý với điều khoản dịch vụ')
            ],
            onSubmit: handleRegister
         });
      }

      // Hàm hiển thị thông báo
      function showMessage(elementId, message, type) {
         const messageDiv = document.getElementById(elementId);
         messageDiv.className = type === 'error' ? 'error-message' : 'success-message';
         messageDiv.textContent = message;
         messageDiv.style.display = 'block';
      }

      // Hàm ẩn thông báo
      function hideMessage(elementId) {
         const messageDiv = document.getElementById(elementId);
         messageDiv.style.display = 'none';
      }

      // Hàm xử lý đăng nhập
      function handleLogin(data) {
         const submitBtn = document.querySelector('#form-login .form-submit');
         submitBtn.disabled = true;
         submitBtn.textContent = 'Đang xử lý...';
         
         hideMessage('login-message');

         // Gọi API đăng nhập qua auth.php
         fetch('../backend/auth.php?action=login', {
            method: 'POST',
            headers: {
               'Content-Type': 'application/json',
               'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify(data)
         })
         .then(response => response.json())
         .then(result => {
            if (result.success) {
               showMessage('login-message', result.message, 'success');
               // Kiểm tra role và chuyển hướng sau 1 giây
               setTimeout(() => {
                  if (result.user) {
                     if (result.user.role === 'manager') {
                        window.location.href = '../frontend/admin/static/index.php';
                     } else if (result.user.role === 'staff') {
                        window.location.href = '../frontend/staff_manager/dashboard.php';
                     } else {
                        window.location.href = '../backend/auth.php';
                     }
                  } else {
                     window.location.href = '../backend/auth.php';
                  }
               }, 1000);
            } else {
               showMessage('login-message', result.message, 'error');
               submitBtn.disabled = false;
               submitBtn.textContent = 'Sign in';
            }
         })
         .catch(error => {
            console.error('Error:', error);
            showMessage('login-message', 'Có lỗi xảy ra. Vui lòng thử lại.', 'error');
            submitBtn.disabled = false;
            submitBtn.textContent = 'Sign in';
         });
      }

      // Hàm xử lý đăng ký
      function handleRegister(data) {
         const submitBtn = document.querySelector('#form-register .form-submit');
         submitBtn.disabled = true;
         submitBtn.textContent = 'Đang xử lý...';
         
         hideMessage('register-message');

         // Chuẩn bị dữ liệu
         const registerData = {
            username: data.username,
            email: data.email,
            password: data.password,
            full_name: data.username, // Sử dụng username làm họ tên
            phone: data.phone || ''
         };

         // Gọi API đăng ký qua auth.php
         fetch('../backend/auth.php?action=register', {
            method: 'POST',
            headers: {
               'Content-Type': 'application/json',
               'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify(registerData)
         })
         .then(response => response.json())
         .then(result => {
            if (result.success) {
               showMessage('register-message', result.message, 'success');
               // Chuyển về form đăng nhập sau 2 giây
               setTimeout(() => {
                  switchToLogin();
                  showMessage('login-message', 'Đăng ký thành công! Vui lòng đăng nhập.', 'success');
                  // Reset form
                  document.getElementById('form-register').reset();
               }, 2000);
            } else {
               showMessage('register-message', result.message, 'error');
               submitBtn.disabled = false;
               submitBtn.textContent = 'Sign up';
            }
         })
         .catch(error => {
            console.error('Error:', error);
            showMessage('register-message', 'Có lỗi xảy ra. Vui lòng thử lại.', 'error');
            submitBtn.disabled = false;
            submitBtn.textContent = 'Sign up';
         });
      }

      // Chuyển sang form đăng ký
      function switchToRegister() {
         document.getElementById('form-login').style.display = 'none';
         document.getElementById('form-register').style.display = 'block';
         document.getElementById('sidebar-title').textContent = 'Have an account?';
         document.getElementById('sidebar-description').textContent = 'Sign in to continue accessing our services.';
         hideMessage('login-message');
         hideMessage('register-message');
      }

      // Chuyển sang form đăng nhập
      function switchToLogin() {
         document.getElementById('form-register').style.display = 'none';
         document.getElementById('form-login').style.display = 'block';
         document.getElementById('sidebar-title').textContent = "Don't have an account?";
         document.getElementById('sidebar-description').textContent = 'Register to access all the features of our services. Manage your business in one place. It\'s free';
         hideMessage('login-message');
         hideMessage('register-message');
      }

      // Khởi tạo khi trang load
      document.addEventListener('DOMContentLoaded', function() {
         initValidators();
         
         // Không tự động redirect nữa - cho phép người dùng đăng nhập lại
      });
   </script>
</body>
</html>
