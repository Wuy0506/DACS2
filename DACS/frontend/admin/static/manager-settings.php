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
	<meta name="description" content="Cấu hình hệ thống Quản lý KTX">

	<link rel="preconnect" href="https://fonts.gstatic.com">
	<link rel="shortcut icon" href="img/icons/icon-48x48.png" />

	<title>Admin KTX</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
	<link href="css/app.css" rel="stylesheet">

	
	
</head>

<body>
	<div class="wrapper">
		<?php include 'include/include.php'; ?>	
		<div class="main">

			<main class="content">
				<div class="container-fluid p-0">

					<div class="mb-3">
						<h1 class="h3 d-inline align-middle">Cấu hình Hệ thống</h1>
						<button class="btn btn-primary float-end" data-bs-toggle="modal" data-bs-target="#addSettingModal">
							<i data-feather="plus"></i> Thêm cấu hình
						</button>
					</div>

					<div class="row">
						<div class="col-12">
							<div class="card">
								<div class="card-header">
									<h5 class="card-title mb-0">Danh sách cấu hình</h5>
									<p class="text-muted mb-0"><small>Các cấu hình sẽ được áp dụng cho toàn bộ hệ thống</small></p>
								</div>
								<div class="card-body">
									<table class="table table-hover">
										<thead>
											<tr>
												<th>ID</th>
												<th>Tên cấu hình</th>
												<th>Giá trị</th>
												<th>Cập nhật lần cuối</th>
												<th>Người cập nhật</th>
												<th>Thao tác</th>
											</tr>
										</thead>
										<tbody id="settingsTableBody">
											<tr>
												<td colspan="6" class="text-center">Đang tải dữ liệu...</td>
											</tr>
										</tbody>
									</table>
								</div>
							</div>
						</div>
					</div>

					<div class="row mt-3">
						<div class="col-12">
							<div class="card">
								<div class="card-header">
									<h5 class="card-title mb-0">Hướng dẫn sử dụng</h5>
								</div>
								<div class="card-body">
									<ul>
										<li><strong>Giá điện (price_per_kwh):</strong> Giá tiền mỗi kWh điện, ví dụ: 3500</li>
										<li><strong>Giá nước (price_per_m3):</strong> Giá tiền mỗi m³ nước, ví dụ: 20000</li>
										<li><strong>Thời hạn thanh toán (payment_deadline_days):</strong> Số ngày hạn thanh toán, ví dụ: 15</li>
										<li><strong>Email hệ thống (system_email):</strong> Email liên hệ của hệ thống</li>
									</ul>
									<p class="text-warning"><i data-feather="alert-triangle"></i> Lưu ý: Các thay đổi sẽ được áp dụng ngay lập tức!</p>
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

	<!-- Add Setting Modal -->
	<div class="modal fade" id="addSettingModal" tabindex="-1">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title">Thêm cấu hình mới</h5>
					<button type="button" class="btn-close" data-bs-dismiss="modal"></button>
				</div>
				<div class="modal-body">
					<form id="addSettingForm">
						<div class="mb-3">
							<label class="form-label">Tên cấu hình <span class="text-danger">*</span></label>
							<input type="text" class="form-control" name="setting_name" placeholder="vd: price_per_kwh" required>
						</div>
						<div class="mb-3">
							<label class="form-label">Giá trị <span class="text-danger">*</span></label>
							<input type="text" class="form-control" name="setting_value" placeholder="vd: 3500" required>
						</div>
					</form>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
					<button type="button" class="btn btn-primary" onclick="addSetting()">Thêm</button>
				</div>
			</div>
		</div>
	</div>

	<!-- Edit Setting Modal -->
	<div class="modal fade" id="editSettingModal" tabindex="-1">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title">Chỉnh sửa cấu hình</h5>
					<button type="button" class="btn-close" data-bs-dismiss="modal"></button>
				</div>
				<div class="modal-body">
					<form id="editSettingForm">
						<input type="hidden" name="setting_id" id="editSettingId">
						<div class="mb-3">
							<label class="form-label">Tên cấu hình</label>
							<input type="text" class="form-control" id="editSettingName" readonly>
						</div>
						<div class="mb-3">
							<label class="form-label">Giá trị <span class="text-danger">*</span></label>
							<input type="text" class="form-control" name="setting_value" id="editSettingValue" required>
						</div>
					</form>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
					<button type="button" class="btn btn-primary" onclick="updateSetting()">Cập nhật</button>
				</div>
			</div>
		</div>
	</div>

	<script src="js/app.js"></script>
	<script>
		document.addEventListener("DOMContentLoaded", function() {
			loadSettings();
		});

		function loadSettings() {
			fetch('../../../backend/controller/manager/SystemSettingsController.php?action=get-all-settings')
				.then(response => response.json())
				.then(data => {
					if (data.success) {
						renderSettingsTable(data.settings);
					}
				})
				.catch(error => console.error('Error:', error));
		}

		function renderSettingsTable(settings) {
			const tbody = document.getElementById('settingsTableBody');
			tbody.innerHTML = '';

			if (settings.length === 0) {
				tbody.innerHTML = '<tr><td colspan="6" class="text-center">Chưa có cấu hình nào</td></tr>';
				return;
			}

			settings.forEach(setting => {
				const row = `<tr>
					<td>${setting.setting_id}</td>
					<td><strong>${setting.setting_name}</strong></td>
					<td>${setting.setting_value}</td>
					<td>${setting.last_updated || '-'}</td>
					<td>${setting.updated_by_name || '-'}</td>
					<td>
						<button class="btn btn-sm btn-primary" onclick="editSetting(${setting.setting_id})">
							<i data-feather="edit"></i> Sửa
						</button>
						<button class="btn btn-sm btn-danger" onclick="deleteSetting(${setting.setting_id})">
							<i data-feather="trash-2"></i> Xóa
						</button>
					</td>
				</tr>`;
				tbody.innerHTML += row;
			});

			feather.replace();
		}

		function addSetting() {
			const form = document.getElementById('addSettingForm');
			const formData = new FormData(form);
			const data = Object.fromEntries(formData);

			fetch('../../../backend/controller/manager/SystemSettingsController.php?action=add-setting', {
				method: 'POST',
				headers: {'Content-Type': 'application/json'},
				body: JSON.stringify(data)
			})
			.then(response => response.json())
			.then(result => {
				if (result.success) {
					alert('Thêm cấu hình thành công!');
					bootstrap.Modal.getInstance(document.getElementById('addSettingModal')).hide();
					form.reset();
					loadSettings();
				} else {
					alert('Lỗi: ' + result.message);
				}
			})
			.catch(error => {
				console.error('Error:', error);
				alert('Có lỗi xảy ra!');
			});
		}

		function editSetting(settingId) {
			fetch(`../../../backend/controller/manager/SystemSettingsController.php?action=get-setting&setting_id=${settingId}`)
				.then(response => response.json())
				.then(data => {
					if (data.success) {
						const setting = data.setting;
						document.getElementById('editSettingId').value = setting.setting_id;
						document.getElementById('editSettingName').value = setting.setting_name;
						document.getElementById('editSettingValue').value = setting.setting_value;
						
						new bootstrap.Modal(document.getElementById('editSettingModal')).show();
					}
				})
				.catch(error => console.error('Error:', error));
		}

		function updateSetting() {
			const form = document.getElementById('editSettingForm');
			const formData = new FormData(form);
			const data = Object.fromEntries(formData);

			fetch('../../../backend/controller/manager/SystemSettingsController.php?action=update-setting', {
				method: 'POST',
				headers: {'Content-Type': 'application/json'},
				body: JSON.stringify(data)
			})
			.then(response => response.json())
			.then(result => {
				if (result.success) {
					alert('Cập nhật thành công!');
					bootstrap.Modal.getInstance(document.getElementById('editSettingModal')).hide();
					loadSettings();
				} else {
					alert('Lỗi: ' + result.message);
				}
			})
			.catch(error => {
				console.error('Error:', error);
				alert('Có lỗi xảy ra!');
			});
		}

		function deleteSetting(settingId) {
			if (!confirm('Bạn có chắc chắn muốn xóa cấu hình này?')) return;

			fetch('../../../backend/controller/manager/SystemSettingsController.php?action=delete-setting', {
				method: 'POST',
				headers: {'Content-Type': 'application/json'},
				body: JSON.stringify({setting_id: settingId})
			})
			.then(response => response.json())
			.then(result => {
				if (result.success) {
					alert('Xóa thành công!');
					loadSettings();
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
