<?php
require_once __DIR__ . '/../../../backend/permission.php';
checkPermission(['manager']);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
	<meta charset="utf-8">
	<title>Thống kê Tổng quan | Admin KTX</title>
	<link href="css/app.css" rel="stylesheet">
</head>
<body>
	<div class="wrapper">
		<?php include 'include/include.php'; ?>
		<div class="main">

			<main class="content">
				<div class="container-fluid p-0">
					<h1 class="h3 mb-3">Thống kê Tổng quan</h1>

					<div class="row">
						<div class="col-md-3">
							<div class="card">
								<div class="card-body">
									<h5 class="card-title">Tổng sinh viên</h5>
									<h1 id="totalStudents">0</h1>
								</div>
							</div>
						</div>
						<div class="col-md-3">
							<div class="card">
								<div class="card-body">
									<h5 class="card-title">Tổng phòng</h5>
									<h1 id="totalRooms">0</h1>
								</div>
							</div>
						</div>
						<div class="col-md-3">
							<div class="card">
								<div class="card-body">
									<h5 class="card-title">Phòng trống</h5>
									<h1 id="emptyRooms">0</h1>
								</div>
							</div>
						</div>
						<div class="col-md-3">
							<div class="card">
								<div class="card-body">
									<h5 class="card-title">Hợp đồng hiệu lực</h5>
									<h1 id="activeContracts">0</h1>
								</div>
							</div>
						</div>
					</div>

					<div class="row">
						<div class="col-12">
							<div class="card">
								<div class="card-header">
									<h5 class="card-title">Thống kê phòng theo tòa nhà</h5>
								</div>
								<div class="card-body">
									<table class="table">
										<thead>
											<tr>
												<th>Tòa nhà</th>
												<th>Tổng phòng</th>
												<th>Tổng giường</th>
												<th>Giường trống</th>
												<th>Phòng trống</th>
												<th>Phòng đầy</th>
												<th>Bảo trì</th>
											</tr>
										</thead>
										<tbody id="buildingStats"></tbody>
									</table>
								</div>
							</div>
						</div>
					</div>

					<div class="row">
						<div class="col-md-6">
							<div class="card">
								<div class="card-header">
									<h5 class="card-title">Thống kê đăng ký phòng</h5>
								</div>
								<div class="card-body">
									<p><strong>Tổng đăng ký:</strong> <span id="totalRegistrations">0</span></p>
									<p><strong>Chờ duyệt:</strong> <span id="pendingRegistrations">0</span></p>
									<p><strong>Đã duyệt:</strong> <span id="approvedRegistrations">0</span></p>
								</div>
							</div>
						</div>
						<div class="col-md-6">
							<div class="card">
								<div class="card-header">
									<h5 class="card-title">Thống kê hợp đồng</h5>
								</div>
								<div class="card-body">
									<p><strong>Tổng hợp đồng:</strong> <span id="totalContracts">0</span></p>
									<p><strong>Đang hiệu lực:</strong> <span id="activeContractsStat">0</span></p>
									<p><strong>Hết hạn:</strong> <span id="expiredContracts">0</span></p>
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
							<p class="mb-0"><strong>Hệ thống quản lý KTX</strong> &copy; 2024</p>
						</div>
					</div>
				</div>
			</footer>
		</div>
	</div>

	<script src="js/app.js"></script>
	<script>
		document.addEventListener("DOMContentLoaded", function() {
			loadOverallStatistics();
			loadBuildingStatistics();
		});

		function loadOverallStatistics() {
			fetch('../../../backend/controller/manager/StatisticsController.php?action=get-overall-statistics')
				.then(response => response.json())
				.then(data => {
					if (data.success) {
						const s = data.statistics;
						document.getElementById('totalStudents').textContent = s.students.total_students || 0;
						document.getElementById('totalRooms').textContent = s.rooms.total_rooms || 0;
						document.getElementById('emptyRooms').textContent = s.rooms.empty_rooms || 0;
						document.getElementById('activeContracts').textContent = s.contracts.active_contracts || 0;
						document.getElementById('totalRegistrations').textContent = s.registrations.total_registrations || 0;
						document.getElementById('pendingRegistrations').textContent = s.registrations.pending_registrations || 0;
						document.getElementById('approvedRegistrations').textContent = s.registrations.approved_registrations || 0;
						document.getElementById('totalContracts').textContent = s.contracts.total_contracts || 0;
						document.getElementById('activeContractsStat').textContent = s.contracts.active_contracts || 0;
						document.getElementById('expiredContracts').textContent = s.contracts.expired_contracts || 0;
					}
				})
				.catch(error => console.error('Error:', error));
		}

		function loadBuildingStatistics() {
			fetch('../../../backend/controller/manager/StatisticsController.php?action=get-room-statistics-by-building')
				.then(response => response.json())
				.then(data => {
					if (data.success) {
						const tbody = document.getElementById('buildingStats');
						tbody.innerHTML = '';
						data.buildings.forEach(b => {
							tbody.innerHTML += `<tr>
								<td>Tòa ${b.building}</td>
								<td>${b.total_rooms}</td>
								<td>${b.total_capacity}</td>
								<td>${b.available_beds}</td>
								<td>${b.empty_rooms}</td>
								<td>${b.full_rooms}</td>
								<td>${b.maintenance_rooms}</td>
							</tr>`;
						});
					}
				})
				.catch(error => console.error('Error:', error));
		}
	</script>
</body>
</html>
