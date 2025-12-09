// Current user object
let currentUser = null;

/**
 * Load user session from storage
 */
function loadUserSession() {
  // Kiểm tra phiên đăng nhập từ PHP session
  fetch("/backend/auth.php?action=check-status", {
    method: "GET",
    headers: {
      "X-Requested-With": "XMLHttpRequest",
    },
    credentials: "include", //
  })
    .then((response) => response.json())
    .then((result) => {
      console.log("Session check result:", result);
      if (result.is_logged_in && result.user) {
        // Người dùng đã đăng nhập, lưu thông tin vào currentUser
        currentUser = {
          profile: {
            user_id: result.user.user_id,
            fullName: result.user.full_name,
            username: result.user.username,
            email: result.user.email,
            role: result.user.role,
          },
        };
        console.log("User logged in:", currentUser);
        updateUserUI();
      } else {
        // Người dùng chưa đăng nhập
        console.log("User not logged in");
        currentUser = null;
        updateUserUI();
      }
    })
    .catch((error) => {
      console.error("Error loading user session:", error);
      currentUser = null;
      updateUserUI();
    });
}

//<a href="/frontend/frontend-html/customer/profile.php?user_id=${currentUser.profile.user_id || ""}" class="dropdown-item text-secondary">Hồ sơ</a>

/**
 * Update UI based on user login status
 */
function updateUserUI() {
  const userSection = document.getElementById("userSection");

  if (!userSection) return;

  if (currentUser && currentUser.profile) {
    // User is logged in
    userSection.innerHTML = `
            <div class="nav-item dropdown pe-5">
                <a href="#" class="nav-link dropdown-toggle text-f-login my-dropdown-toggle" data-bs-toggle="dropdown">
                    ${currentUser.profile.fullName || "User"}
                </a>
                <div class="dropdown-menu dropdown-menu-chang">
                      <a href="/frontend/hoso" class="dropdown-item text-secondary">Hồ sơ</a>
                    <a href="/frontend/dondangky" class="dropdown-item text-secondary">Đơn đăng ký phòng</a>
                    <a href="/frontend/yeucausuchua" class="dropdown-item text-secondary">Yêu cầu sửa chữa</a>
                    <a href="#" class="dropdown-item text-secondary" onclick="handleLogout(); return false;">Đăng xuất</a>
                </div>
            </div>
        `;
  } else {
    // User is not logged in
    userSection.innerHTML = `
            <a href="/LoginDarkSunSet/login.php" class="btn btn-primary me-3">
                Đăng Nhập
            </a>
        `;
  }
}

/**
 * Handle user logout
 */
async function handleLogout() {
  try {
    // Gọi server để hủy session
    await fetch("/backend/auth.php?action=logout", {
      method: "GET",
      credentials: "include", // gửi cookie session
      headers: {
        "X-Requested-With": "XMLHttpRequest",
      },
    });

    // Xóa JWT local
    removeJWT();

    // Xóa user hiện tại
    currentUser = null;
    updateUserUI();

    showToast("Đã đăng xuất thành công!", "success");

    // Nếu đang ở trang cần đăng nhập → chuyển hướng
    if (
      window.location.pathname.includes("purchase") ||
      window.location.pathname.includes("admin") ||
      window.location.pathname.includes("profile") ||
      window.location.pathname.includes("about") ||
      window.location.pathname.includes("checkin") ||
      window.location.pathname.includes("repairrequest_student") ||
      window.location.pathname.includes("roomOfStudent")
    ) {
      setTimeout(() => {
        window.location.href = "/trangchu";
      }, 1000);
    }
  } catch (error) {
    console.error("Logout error:", error);
    showToast("Lỗi đăng xuất. Vui lòng thử lại!", "danger");
  }
}

// function getCurrentUser() {
//   return currentUser;
// }
