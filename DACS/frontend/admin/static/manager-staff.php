<?php
require_once __DIR__ . '/../../../backend/permission.php';
checkPermission(['manager']);
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Quản lý nhân viên - Hệ thống quản lý ký túc xá">

    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link rel="shortcut icon" href="img/icons/icon-48x48.png" />

    <title>Quản lý Nhân viên | Admin KTX</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/app.css" rel="stylesheet">
    <style>
    .badge-role-staff {
        background-color: #3b7ddd;
        color: white;
    }

    .badge-role-manager {
        background-color: #dc3545;
        color: white;
    }

    /* Fix footer white space */
    .wrapper {
        min-height: 100vh;
        display: flex;
    }

    .main {
        min-height: auto !important;
        flex: 1;
    }

    /* Fix modal z-index */
    .modal {
        z-index: 1060 !important;
    }

    .modal-backdrop {
        z-index: 1055 !important;
    }
    </style>
</head>

<body>
    <div class="wrapper">
        <?php include 'include/include.php'; ?>
        <main class="content">
            <div class="container-fluid p-0">

                <div class="mb-3">
                    <h1 class="h3 d-inline align-middle">Quản lý Nhân viên</h1>
                    <button class="btn btn-primary float-end" data-bs-toggle="modal" data-bs-target="#addStaffModal">
                        <i data-feather="plus"></i> Thêm nhân viên
                    </button>
                    <i data-feather="plus"></i> Thêm nhân viên
                    </button>
                </div>

                <!-- Statistics Cards -->
                <div class="row">
                    <div class="col-xl-4 col-xxl-4">
                        <div class="card">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col mt-0">
                                        <h5 class="card-title">Tổng nhân viên</h5>
                                    </div>
                                    <div class="col-auto">
                                        <div class="stat text-primary">
                                            <i class="align-middle" data-feather="users"></i>
                                        </div>
                                    </div>
                                </div>
                                <h1 class="mt-1 mb-3" id="totalStaff">0</h1>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-4 col-xxl-4">
                        <div class="card">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col mt-0">
                                        <h5 class="card-title">Nhân viên thường</h5>
                                    </div>
                                    <div class="col-auto">
                                        <div class="stat text-primary">
                                            <i class="align-middle" data-feather="user"></i>
                                        </div>
                                    </div>
                                </div>
                                <h1 class="mt-1 mb-3" id="totalRegularStaff">0</h1>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-4 col-xxl-4">
                        <div class="card">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col mt-0">
                                        <h5 class="card-title">Quản lý</h5>
                                    </div>
                                    <div class="col-auto">
                                        <div class="stat text-primary">
                                            <i class="align-middle" data-feather="shield"></i>
                                        </div>
                                    </div>
                                </div>
                                <h1 class="mt-1 mb-3" id="totalManagers">0</h1>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Filter -->
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Bộ lọc</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4">
                                        <label class="form-label">Vai trò</label>
                                        <select class="form-select" id="filterRole" onchange="loadStaffList()">
                                            <option value="all">Tất cả</option>
                                            <option value="staff">Nhân viên</option>
                                            <option value="manager">Quản lý</option>
                                        </select>
                                    </div>
                                    <div class="col-md-8">
                                        <label class="form-label">Tìm kiếm</label>
                                        <input type="text" class="form-control" id="searchInput"
                                            placeholder="Tìm theo tên, email, số điện thoại..." onkeyup="filterTable()">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Staff List Table -->
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Danh sách nhân viên</h5>
                            </div>
                            <div class="card-body">
                                <table class="table table-hover" id="staffTable">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Họ tên</th>
                                            <th>Username</th>
                                            <th>Email</th>
                                            <th>Số điện thoại</th>
                                            <th>Vai trò</th>
                                            <th>Chức vụ</th>
                                            <th>Ngày vào làm</th>
                                            <th>Thao tác</th>
                                        </tr>
                                    </thead>
                                    <tbody id="staffTableBody">
                                        <tr>
                                            <td colspan="9" class="text-center">Đang tải dữ liệu...</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </main>

        <footer class="footer">
            <div class="container-fluid">
                <div class="row text-muted">
                    <div class="col-6 text-start">
                        <p class="mb-0">
                            <strong>Hệ thống quản lý KTX</strong> &copy; 2024
                        </p>
                    </div>
                </div>
            </div>
        </footer>
    </div>
    </div>

    <!-- Modals placed outside wrapper for proper display -->
    <!-- Add Staff Modal -->
    <div class="modal fade" id="addStaffModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Thêm nhân viên mới</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="addStaffForm">
                        <div class="mb-3">
                            <label class="form-label">Username <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="username" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Mật khẩu <span class="text-danger">*</span></label>
                            <input type="password" class="form-control" name="password" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Họ tên <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="full_name" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Số điện thoại <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="phone" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Vai trò <span class="text-danger">*</span></label>
                            <select class="form-select" name="role" id="addRole" onchange="toggleRoleFields('add')"
                                required>
                                <option value="staff">Nhân viên</option>
                                <option value="manager">Quản lý</option>
                            </select>
                        </div>
                        <div class="mb-3" id="addPositionField">
                            <label class="form-label">Chức vụ <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="position">
                        </div>
                        <div class="mb-3" id="addDepartmentField" style="display: none;">
                            <label class="form-label">Phòng ban</label>
                            <input type="text" class="form-control" name="department">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Ngày vào làm <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" name="hire_date" required>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="button" class="btn btn-primary" onclick="addStaff()">Thêm</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Staff Modal -->
    <div class="modal fade" id="editStaffModal" tabindex="-1" aria-labelledby="editStaffModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editStaffModalLabel">Chỉnh sửa nhân viên</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editStaffForm">
                        <input type="hidden" name="user_id" id="editUserId">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Họ tên <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="full_name" id="editFullName" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Email <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control" name="email" id="editEmail" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Số điện thoại <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="phone" id="editPhone" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3" id="editPositionField">
                                    <label class="form-label">Chức vụ</label>
                                    <input type="text" class="form-control" name="position" id="editPosition">
                                </div>
                                <div class="mb-3" id="editDepartmentField" style="display: none;">
                                    <label class="form-label">Phòng ban</label>
                                    <input type="text" class="form-control" name="department" id="editDepartment">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Mật khẩu mới (để trống nếu không đổi)</label>
                                    <input type="password" class="form-control" name="password" id="editPassword">
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="button" class="btn btn-primary" onclick="updateStaff()">Cập nhật</button>
                </div>
            </div>
        </div>
    </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/app.js"></script>
    <script>
    let staffData = [];

    document.addEventListener("DOMContentLoaded", function() {
        loadStaffStatistics();
        loadStaffList();
    });

    function loadStaffStatistics() {
        fetch('../../../backend/controller/manager/StaffManagementController.php?action=get-statistics')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('totalStaff').textContent = data.stats.total_staff || 0;
                    document.getElementById('totalRegularStaff').textContent = data.stats.total_regular_staff || 0;
                    document.getElementById('totalManagers').textContent = data.stats.total_managers || 0;
                }
            })
            .catch(error => console.error('Error:', error));
    }

    function loadStaffList() {
        const role = document.getElementById('filterRole').value;
        fetch(`../../../backend/controller/manager/StaffManagementController.php?action=get-all-staff&role=${role}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    staffData = data.staff;
                    renderStaffTable(staffData);
                }
            })
            .catch(error => console.error('Error:', error));
    }

    function renderStaffTable(data) {
        const tbody = document.getElementById('staffTableBody');
        tbody.innerHTML = '';

        if (data.length === 0) {
            tbody.innerHTML = '<tr><td colspan="9" class="text-center">Không có dữ liệu</td></tr>';
            return;
        }

        data.forEach(staff => {
            const roleBadge = staff.role === 'staff' ?
                '<span class="badge badge-role-staff">Nhân viên</span>' :
                '<span class="badge badge-role-manager">Quản lý</span>';

            const row = `<tr>
					<td>${staff.user_id}</td>
					<td>${staff.full_name}</td>
					<td>${staff.username}</td>
					<td>${staff.email}</td>
					<td>${staff.phone}</td>
					<td>${roleBadge}</td>
					<td>${staff.position || '-'}</td>
					<td>${staff.hire_date || '-'}</td>
					<td>
						<button class="btn btn-sm btn-primary" onclick="editStaff(${staff.user_id})">
							<i data-feather="edit"></i> Sửa
						</button>
						<button class="btn btn-sm btn-danger" onclick="deleteStaff(${staff.user_id})">
							<i data-feather="trash-2"></i> Xóa
						</button>
					</td>
				</tr>`;
            tbody.innerHTML += row;
        });

        feather.replace();
    }

    function filterTable() {
        const searchText = document.getElementById('searchInput').value.toLowerCase();
        const filtered = staffData.filter(staff => {
            return staff.full_name.toLowerCase().includes(searchText) ||
                staff.username.toLowerCase().includes(searchText) ||
                staff.email.toLowerCase().includes(searchText) ||
                staff.phone.toLowerCase().includes(searchText);
        });
        renderStaffTable(filtered);
    }

    function toggleRoleFields(mode) {
        const role = document.getElementById(mode + 'Role').value;
        const positionField = document.getElementById(mode + 'PositionField');
        const departmentField = document.getElementById(mode + 'DepartmentField');

        if (role === 'staff') {
            positionField.style.display = 'block';
            departmentField.style.display = 'none';
        } else {
            positionField.style.display = 'none';
            departmentField.style.display = 'block';
        }
    }

    function addStaff() {
        const form = document.getElementById('addStaffForm');
        const formData = new FormData(form);
        const data = Object.fromEntries(formData);

        fetch('../../../backend/controller/manager/StaffManagementController.php?action=add-staff', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    alert('Thêm nhân viên thành công!');
                    bootstrap.Modal.getInstance(document.getElementById('addStaffModal')).hide();
                    form.reset();
                    loadStaffList();
                    loadStaffStatistics();
                } else {
                    alert('Lỗi: ' + result.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Có lỗi xảy ra!');
            });
    }

    function editStaff(userId) {
        fetch(
                `../../../backend/controller/manager/StaffManagementController.php?action=get-staff-details&user_id=${userId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const staff = data.staff;
                    document.getElementById('editUserId').value = staff.user_id;
                    document.getElementById('editFullName').value = staff.full_name;
                    document.getElementById('editEmail').value = staff.email;
                    document.getElementById('editPhone').value = staff.phone;
                    document.getElementById('editPosition').value = staff.position || '';
                    document.getElementById('editDepartment').value = staff.department || '';

                    if (staff.role === 'manager') {
                        document.getElementById('editPositionField').style.display = 'none';
                        document.getElementById('editDepartmentField').style.display = 'block';
                    } else {
                        document.getElementById('editPositionField').style.display = 'block';
                        document.getElementById('editDepartmentField').style.display = 'none';
                    }

                    // Show modal with proper Bootstrap 5 method
                    const modalElement = document.getElementById('editStaffModal');
                    const modal = new bootstrap.Modal(modalElement, {
                        backdrop: 'static',
                        keyboard: false
                    });
                    modal.show();
                } else {
                    alert('Không thể tải thông tin nhân viên: ' + (data.message || 'Lỗi không xác định'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Có lỗi xảy ra khi tải thông tin nhân viên!');
            });
    }

    function updateStaff() {
        const form = document.getElementById('editStaffForm');
        const formData = new FormData(form);
        const data = Object.fromEntries(formData);

        fetch('../../../backend/controller/manager/StaffManagementController.php?action=update-staff', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    alert('Cập nhật thành công!');
                    bootstrap.Modal.getInstance(document.getElementById('editStaffModal')).hide();
                    loadStaffList();
                } else {
                    alert('Lỗi: ' + result.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Có lỗi xảy ra!');
            });
    }

    function deleteStaff(userId) {
        if (!confirm('Bạn có chắc chắn muốn xóa nhân viên này?')) return;

        fetch('../../../backend/controller/manager/StaffManagementController.php?action=delete-staff', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    user_id: userId
                })
            })
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    alert('Xóa thành công!');
                    loadStaffList();
                    loadStaffStatistics();
                } else {
                    alert('Lỗi: ' + result.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Có lỗi xảy ra!');
            });
    }
    </script>

</body>

</html>