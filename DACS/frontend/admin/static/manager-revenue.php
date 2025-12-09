<?php
require_once __DIR__ . '/../../../backend/permission.php';
checkPermission(['manager']);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
	<meta charset="utf-8">
	<title>Doanh thu & Chi phí | Admin KTX</title>
	<link href="css/app.css" rel="stylesheet">
</head>
<body>
	<div class="wrapper">
		<?php include 'include/include.php'; ?>
		
		<div class="main">
			

			<main class="content">
				<div class="container-fluid p-0">
					<h1 class="h3 mb-3">Doanh thu & Chi phí</h1>

					<div class="row">
						<div class="col-12">
							<div class="card">
								<div class="card-header">
									<h5 class="card-title mb-0">Bộ lọc</h5>
								</div>
								<div class="card-body">
									<div class="row">
										<div class="col-md-4">
											<label>Từ ngày</label>
											<input type="date" class="form-control" id="startDate">
										</div>
										<div class="col-md-4">
											<label>Đến ngày</label>
											<input type="date" class="form-control" id="endDate">
										</div>
										<div class="col-md-4">
											<label>&nbsp;</label>
											<button class="btn btn-primary d-block" onclick="loadRevenue()">Tìm kiếm</button>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>

					<div class="row">
						<div class="col-md-6">
							<div class="card">
								<div class="card-header">
									<h5 class="card-title">Tổng doanh thu</h5>
								</div>
								<div class="card-body">
									<h1 id="totalRevenue">0 VNĐ</h1>
									<div id="revenueDetail"></div>
								</div>
							</div>
						</div>
						<div class="col-md-6">
							<div class="card">
								<div class="card-header">
									<h5 class="card-title">Tổng chi phí sửa chữa</h5>
								</div>
								<div class="card-body">
									<h1 id="totalRepairCost">0 VNĐ</h1>
									<p><strong>Chi phí dự kiến:</strong> <span id="estimatedCost">0 VNĐ</span></p>
									<p><strong>Chi phí thực tế:</strong> <span id="actualCost">0 VNĐ</span></p>
								</div>
							</div>
						</div>
					</div>

					<div class="row">
						<div class="col-12">
							<div class="card">
								<div class="card-header">
									<h5 class="card-title">Doanh thu theo loại</h5>
								</div>
								<div class="card-body">
									<table class="table">
										<thead>
											<tr>
												<th>Loại</th>
												<th>Số lượng thanh toán</th>
												<th>Tổng tiền</th>
											</tr>
										</thead>
										<tbody id="revenueTable"></tbody>
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
			loadRevenue();
			loadRepairCost();
		});

		function loadRevenue() {
			const start = document.getElementById('startDate').value;
			const end = document.getElementById('endDate').value;
			let url = '../../../backend/controller/manager/StatisticsController.php?action=get-revenue-statistics';
			if (start) url += `&start_date=${start}`;
			if (end) url += `&end_date=${end}`;

			fetch(url)
				.then(response => response.json())
				.then(data => {
					if (data.success) {
						document.getElementById('totalRevenue').textContent = 
							new Intl.NumberFormat('vi-VN').format(data.total_revenue) + ' VNĐ';
						
						const tbody = document.getElementById('revenueTable');
						tbody.innerHTML = '';
						data.revenue.forEach(r => {
							tbody.innerHTML += `<tr>
								<td>${r.payment_type}</td>
								<td>${r.total_payments}</td>
								<td>${new Intl.NumberFormat('vi-VN').format(r.total_amount)} VNĐ</td>
							</tr>`;
						});
					}
				})
				.catch(error => console.error('Error:', error));
		}

		function loadRepairCost() {
			const start = document.getElementById('startDate').value;
			const end = document.getElementById('endDate').value;
			let url = '../../../backend/controller/manager/StatisticsController.php?action=get-repair-cost-statistics';
			if (start) url += `&start_date=${start}`;
			if (end) url += `&end_date=${end}`;

			fetch(url)
				.then(response => response.json())
				.then(data => {
					if (data.success) {
						document.getElementById('totalRepairCost').textContent = 
							new Intl.NumberFormat('vi-VN').format(data.total_actual_cost) + ' VNĐ';
						document.getElementById('estimatedCost').textContent = 
							new Intl.NumberFormat('vi-VN').format(data.total_estimated_cost) + ' VNĐ';
						document.getElementById('actualCost').textContent = 
							new Intl.NumberFormat('vi-VN').format(data.total_actual_cost) + ' VNĐ';
					}
				})
				.catch(error => console.error('Error:', error));
		}
	</script>
</body>
</html>
