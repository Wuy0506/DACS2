<?php
// admin/index.php
require_once __DIR__ . 'p/../../../backend/permission.php';
checkPermission(['manager']);
// Gọi Model
require_once __DIR__ . '/../../../backend/model/staff/ContractModel.php';
$contractModel = new ContractModel();

// 1. Tự động xử lý hết hạn (Chạy ngầm mỗi khi admin vào trang chủ)
$contractModel->processExpiredContracts();

// (Phần gửi mail nhắc nhở thì nên cân nhắc vì nếu F5 liên tục sẽ gửi nhiều mail, 
// tốt nhất chỉ dùng Process Expired ở cách này)
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Responsive Admin &amp; Dashboard Template based on Bootstrap 5">
    <meta name="author" content="AdminKit">
    <meta name="keywords"
        content="adminkit, bootstrap, bootstrap 5, admin, dashboard, template, responsive, css, sass, html, theme, front-end, ui kit, web">

    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link rel="shortcut icon" href="img/icons/icon-48x48.png" />

    <link rel="canonical" href="https://demo-basic.adminkit.io/" />

    <title>Dashboard - Quản lý KTX</title>

    <link href="css/app.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="wrapper">

        <?php include 'include/include.php'; ?>


        <main class="content">
            <div class="container-fluid p-0">

                <h1 class="h3 mb-3"><strong>Dashboard</strong> Tổng quan</h1>

                <div class="row">
                    <div class="col-xl-6 col-xxl-5 d-flex">
                        <div class="w-100">
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col mt-0">
                                                    <h5 class="card-title">Tổng sinh viên</h5>
                                                </div>

                                                <div class="col-auto">
                                                    <div class="stat text-primary">
                                                        <i class="align-middle" data-feather="users"></i>
                                                    </div>
                                                </div>
                                            </div>
                                            <h1 class="mt-1 mb-3" id="totalStudents">0</h1>
                                            <div class="mb-0">
                                                <span class="text-muted">Sinh viên đang ở KTX</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col mt-0">
                                                    <h5 class="card-title">Phòng trống</h5>
                                                </div>

                                                <div class="col-auto">
                                                    <div class="stat text-success">
                                                        <i class="align-middle" data-feather="home"></i>
                                                    </div>
                                                </div>
                                            </div>
                                            <h1 class="mt-1 mb-3" id="emptyRooms">0</h1>
                                            <div class="mb-0">
                                                <span class="text-muted">Phòng có sẵn</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col mt-0">
                                                    <h5 class="card-title">Tổng phòng</h5>
                                                </div>

                                                <div class="col-auto">
                                                    <div class="stat text-primary">
                                                        <i class="align-middle" data-feather="grid"></i>
                                                    </div>
                                                </div>
                                            </div>
                                            <h1 class="mt-1 mb-3" id="totalRooms">0</h1>
                                            <div class="mb-0">
                                                <span class="text-muted">Phòng trong hệ thống</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col mt-0">
                                                    <h5 class="card-title">Hợp đồng hiệu lực</h5>
                                                </div>

                                                <div class="col-auto">
                                                    <div class="stat text-success">
                                                        <i class="align-middle" data-feather="file-text"></i>
                                                    </div>
                                                </div>
                                            </div>
                                            <h1 class="mt-1 mb-3" id="activeContracts">0</h1>
                                            <div class="mb-0">
                                                <span class="text-muted">Hợp đồng đang hoạt động</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-6 col-xxl-7">
                        <div class="card flex-fill w-100">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Doanh thu theo tháng</h5>
                            </div>
                            <div class="card-body py-3">
                                <div class="chart chart-sm">
                                    <canvas id="chartjs-dashboard-line"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12 col-md-6 col-xxl-3 d-flex order-2 order-xxl-3">
                        <div class="card flex-fill w-100">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Trạng thái phòng</h5>
                            </div>
                            <div class="card-body d-flex">
                                <div class="align-self-center w-100">
                                    <div class="py-3">
                                        <div class="chart chart-xs">
                                            <canvas id="chartjs-dashboard-pie"></canvas>
                                        </div>
                                    </div>

                                    <table class="table mb-0">
                                        <tbody>
                                            <tr>
                                                <td>Phòng trống</td>
                                                <td class="text-end" id="pieEmptyRooms">0</td>
                                            </tr>
                                            <tr>
                                                <td>Phòng đầy</td>
                                                <td class="text-end" id="pieFullRooms">0</td>
                                            </tr>
                                            <tr>
                                                <td>Bảo trì</td>
                                                <td class="text-end" id="pieMaintenanceRooms">0</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-12 col-xxl-6 d-flex order-3 order-xxl-2">
                        <div class="card flex-fill w-100">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Phê duyệt yêu cầu sửa chữa</h5>
                            </div>
                            <div class="card-body" style="max-height: 300px; overflow-y: auto;">
                                <table class="table table-hover my-0">
                                    <thead style="position: sticky; top: 0; background: white; z-index: 1;">
                                        <tr>
                                            <th>Phòng</th>
                                            <th>Mô tả</th>
                                            <th>Chi phí dự kiến</th>
                                            <th>Hành động</th>
                                        </tr>
                                    </thead>
                                    <tbody id="pendingRepairApprovals">
                                        <tr>
                                            <td colspan="4" class="text-center">Đang tải...</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-6 col-xxl-3 d-flex order-1 order-xxl-1">
                        <div class="card flex-fill">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Yêu cầu sửa chữa</h5>
                            </div>
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="flex-grow-1">
                                        <h5 class="mb-0">Chờ xử lý</h5>
                                        <small class="text-muted">Yêu cầu mới</small>
                                    </div>
                                    <h3 class="mb-0 text-warning" id="pendingRepairs">0</h3>
                                </div>
                                <div class="d-flex align-items-center mb-3">
                                    <div class="flex-grow-1">
                                        <h5 class="mb-0">Đang sửa</h5>
                                        <small class="text-muted">Đang xử lý</small>
                                    </div>
                                    <h3 class="mb-0 text-primary" id="inProgressRepairs">0</h3>
                                </div>
                                <div class="d-flex align-items-center mb-3">
                                    <div class="flex-grow-1">
                                        <h5 class="mb-0">Hoàn thành</h5>
                                        <small class="text-muted">Đã xong</small>
                                    </div>
                                    <h3 class="mb-0 text-success" id="completedRepairs">0</h3>
                                </div>
                                <div class="d-flex align-items-center">
                                    <div class="flex-grow-1">
                                        <h5 class="mb-0">Khẩn cấp</h5>
                                        <small class="text-muted">Cần xử lý ngay</small>
                                    </div>
                                    <h3 class="mb-0 text-danger" id="urgentRepairs">0</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12 col-lg-8 col-xxl-9 d-flex">
                        <div class="card flex-fill">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Thống kê phòng theo tòa nhà</h5>
                            </div>
                            <table class="table table-hover my-0">
                                <thead>
                                    <tr>
                                        <th>Tòa nhà</th>
                                        <th class="d-none d-xl-table-cell">Tổng phòng</th>
                                        <th class="d-none d-xl-table-cell">Tổng giường</th>
                                        <th>Phòng trống</th>
                                        <th class="d-none d-md-table-cell">Phòng đầy</th>
                                        <th class="d-none d-md-table-cell">Bảo trì</th>
                                    </tr>
                                </thead>
                                <tbody id="buildingStatistics">
                                    <tr>
                                        <td colspan="6" class="text-center">Đang tải dữ liệu...</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="col-12 col-lg-4 col-xxl-3 d-flex">
                        <div class="card flex-fill w-100">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Doanh thu tháng này</h5>
                            </div>
                            <div class="card-body d-flex w-100">
                                <div class="align-self-center chart chart-lg">
                                    <canvas id="chartjs-dashboard-bar"></canvas>
                                </div>
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
                            <a class="text-muted" href="https://adminkit.io/"
                                target="_blank"><strong>AdminKit</strong></a> - <a class="text-muted"
                                href="https://adminkit.io/" target="_blank"><strong>Bootstrap Admin
                                    Template</strong></a> &copy;
                        </p>
                    </div>
                    <div class="col-6 text-end">
                        <ul class="list-inline">
                            <li class="list-inline-item">
                                <a class="text-muted" href="https://adminkit.io/" target="_blank">Support</a>
                            </li>
                            <li class="list-inline-item">
                                <a class="text-muted" href="https://adminkit.io/" target="_blank">Help Center</a>
                            </li>
                            <li class="list-inline-item">
                                <a class="text-muted" href="https://adminkit.io/" target="_blank">Privacy</a>
                            </li>
                            <li class="list-inline-item">
                                <a class="text-muted" href="https://adminkit.io/" target="_blank">Terms</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </footer>
    </div>
    </div>

    <script src="js/app.js"></script>
    <script>
    window.LOGIN_LOGIN_PAGE = '../../../LoginDarkSunSet/login.php';
    window.LOGIN_AUTH_ENDPOINT = '../../../backend/auth.php';
    </script>
    <script>
    <?php include('../staff_manager/includes/login_session.php'); ?>
    </script>

    <script>
    var lineChart = null;
    document.addEventListener("DOMContentLoaded", function() {
        loadMonthlyRevenueChart();
    });

    function loadMonthlyRevenueChart() {
        const currentYear = new Date().getFullYear();
        fetch('../../../backend/controller/manager/StatisticsController.php?action=get-monthly-statistics&year=' +
                currentYear)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const monthlyData = data.monthly_statistics;

                    // Khởi tạo dữ liệu cho 12 tháng
                    const revenueByMonth = new Array(12).fill(0);

                    // Điền dữ liệu thực vào các tháng tương ứng
                    monthlyData.forEach(item => {
                        if (item.month >= 1 && item.month <= 12) {
                            revenueByMonth[item.month - 1] = item.revenue || 0;
                        }
                    });

                    var ctx = document.getElementById("chartjs-dashboard-line").getContext("2d");
                    var gradient = ctx.createLinearGradient(0, 0, 0, 225);
                    gradient.addColorStop(0, "rgba(215, 227, 244, 1)");
                    gradient.addColorStop(1, "rgba(215, 227, 244, 0)");

                    // Xóa biểu đồ cũ nếu có
                    if (lineChart) lineChart.destroy();

                    // Tạo biểu đồ mới
                    lineChart = new Chart(document.getElementById("chartjs-dashboard-line"), {
                        type: "line",
                        data: {
                            labels: ["Tháng 1", "Tháng 2", "Tháng 3", "Tháng 4", "Tháng 5", "Tháng 6",
                                "Tháng 7", "Tháng 8", "Tháng 9", "Tháng 10", "Tháng 11", "Tháng 12"
                            ],
                            datasets: [{
                                label: "Doanh thu (VNĐ)",
                                fill: true,
                                backgroundColor: gradient,
                                borderColor: window.theme.primary,
                                data: revenueByMonth
                            }]
                        },
                        options: {
                            maintainAspectRatio: false,
                            legend: {
                                display: false
                            },
                            tooltips: {
                                intersect: false,
                                callbacks: {
                                    label: function(tooltipItem) {
                                        return 'Doanh thu: ' + Number(tooltipItem.yLabel)
                                            .toLocaleString('vi-VN') + ' đ';
                                    }
                                }
                            },
                            hover: {
                                intersect: true
                            },
                            plugins: {
                                filler: {
                                    propagate: false
                                }
                            },
                            scales: {
                                xAxes: [{
                                    reverse: false,
                                    gridLines: {
                                        color: "rgba(0,0,0,0.0)"
                                    }
                                }],
                                yAxes: [{
                                    ticks: {
                                        stepSize: 1000000,
                                        callback: function(value) {
                                            return (value / 1000000).toFixed(1) + 'M';
                                        }
                                    },
                                    display: true,
                                    borderDash: [3, 3],
                                    gridLines: {
                                        color: "rgba(0,0,0,0.0)"
                                    }
                                }]
                            }
                        }
                    });
                }
            })
            .catch(error => console.error('Error loading monthly revenue:', error));
    }
    </script>
    <script>
    var barChart = null;

    function updateRepairChart(statistics) {
        const labels = ['Chờ xử lý', 'Đang sửa', 'Hoàn thành', 'Từ chối', 'Khẩn cấp'];
        const values = [
            statistics.pending || 0,
            statistics.inProgress || 0,
            statistics.completed || 0,
            statistics.rejected || 0,
            statistics.urgent || 0
        ];
        const colors = [
            window.theme.warning,
            window.theme.primary,
            window.theme.success,
            window.theme.danger,
            window.theme.info
        ];

        if (barChart) {
            barChart.destroy();
        }

        barChart = new Chart(document.getElementById("chartjs-dashboard-bar"), {
            type: "bar",
            data: {
                labels: labels,
                datasets: [{
                    label: "Yêu cầu sửa chữa",
                    backgroundColor: colors,
                    borderColor: colors,
                    hoverBackgroundColor: colors,
                    hoverBorderColor: colors,
                    data: values,
                    barPercentage: .65,
                    categoryPercentage: .55
                }]
            },
            options: {
                maintainAspectRatio: false,
                legend: {
                    display: false
                },
                tooltips: {
                    callbacks: {
                        label: function(tooltipItem) {
                            return tooltipItem.yLabel + ' yêu cầu';
                        }
                    }
                },
                scales: {
                    yAxes: [{
                        gridLines: {
                            display: false
                        },
                        stacked: false,
                        ticks: {
                            beginAtZero: true,
                            stepSize: 1
                        }
                    }],
                    xAxes: [{
                        stacked: false,
                        gridLines: {
                            color: "transparent"
                        }
                    }]
                }
            }
        });
    }
    </script>
    <script>
    // Load dashboard statistics
    document.addEventListener("DOMContentLoaded", function() {
        loadOverallStatistics();
        loadRepairStatistics();
        loadRoomStatisticsByBuilding();
        loadPendingRepairApprovals();
    });

    function loadOverallStatistics() {
        fetch('../../../backend/controller/manager/StatisticsController.php?action=get-overall-statistics')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const stats = data.statistics;

                    // Update overview cards
                    document.getElementById('totalStudents').textContent = stats.students.total_students || 0;
                    document.getElementById('emptyRooms').textContent = stats.rooms.empty_rooms || 0;
                    document.getElementById('totalRooms').textContent = stats.rooms.total_rooms || 0;
                    document.getElementById('activeContracts').textContent = stats.contracts.active_contracts || 0;

                    // Update pie chart data
                    document.getElementById('pieEmptyRooms').textContent = stats.rooms.empty_rooms || 0;
                    document.getElementById('pieFullRooms').textContent = stats.rooms.full_rooms || 0;
                    document.getElementById('pieMaintenanceRooms').textContent = stats.rooms.maintenance_rooms || 0;

                    // Update pie chart
                    updatePieChart(
                        stats.rooms.empty_rooms || 0,
                        stats.rooms.full_rooms || 0,
                        stats.rooms.maintenance_rooms || 0
                    );
                }
            })
            .catch(error => console.error('Error loading statistics:', error));
    }

    function loadRepairStatistics() {
        fetch('../../../backend/controller/manager/ManagerRepairController.php?action=get-statistics')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const stats = data.stats;
                    document.getElementById('pendingRepairs').textContent = stats.pending || 0;
                    // "Đang sửa" = waiting_approval + approved
                    const inProgress = (parseInt(stats.waiting_approval) || 0) + (parseInt(stats.approved) || 0);
                    document.getElementById('inProgressRepairs').textContent = inProgress;
                    document.getElementById('completedRepairs').textContent = stats.completed || 0;
                    document.getElementById('urgentRepairs').textContent = stats.urgent || 0;
                    const repairStats = {
                        pending: stats.pending || 0,
                        inProgress,
                        completed: stats.completed || 0,
                        rejected: stats.rejected || 0,
                        urgent: stats.urgent || 0
                    };
                    updateRepairChart(repairStats);
                }
            })
            .catch(error => console.error('Error loading repair statistics:', error));
    }

    function loadRoomStatisticsByBuilding() {
        fetch('../../../backend/controller/manager/StatisticsController.php?action=get-room-statistics-by-building')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const tbody = document.getElementById('buildingStatistics');
                    tbody.innerHTML = '';

                    if (data.buildings.length === 0) {
                        tbody.innerHTML = '<tr><td colspan="6" class="text-center">Chưa có dữ liệu</td></tr>';
                    } else {
                        data.buildings.forEach(building => {
                            const row = `<tr>
									<td><strong>Tòa ${building.building}</strong></td>
									<td class="d-none d-xl-table-cell">${building.total_rooms}</td>
									<td class="d-none d-xl-table-cell">${building.total_capacity}</td>
									<td><span class="badge bg-success">${building.empty_rooms}</span></td>
									<td class="d-none d-md-table-cell"><span class="badge bg-danger">${building.full_rooms}</span></td>
									<td class="d-none d-md-table-cell"><span class="badge bg-warning">${building.maintenance_rooms}</span></td>
								</tr>`;
                            tbody.innerHTML += row;
                        });
                    }
                }
            })
            .catch(error => console.error('Error loading building statistics:', error));
    }

    var pieChart = null;

    function updatePieChart(empty, full, maintenance) {
        // Xóa biểu đồ cũ nếu có
        if (pieChart) {
            pieChart.destroy();
        }

        // Tạo biểu đồ mới
        pieChart = new Chart(document.getElementById("chartjs-dashboard-pie"), {
            type: "pie",
            data: {
                labels: ["Phòng trống", "Phòng đầy", "Bảo trì"],
                datasets: [{
                    data: [empty, full, maintenance],
                    backgroundColor: [
                        window.theme.success,
                        window.theme.danger,
                        window.theme.warning
                    ],
                    borderWidth: 5
                }]
            },
            options: {
                responsive: !window.MSInputMethodContext,
                maintainAspectRatio: false,
                legend: {
                    display: false
                },
                cutoutPercentage: 75
            }
        });
    }

    // Load danh sách yêu cầu sửa chữa cần phê duyệt
    function loadPendingRepairApprovals() {
        fetch('../../../backend/controller/manager/ManagerRepairController.php?action=get-pending-approvals')
            .then(response => response.json())
            .then(data => {
                const tbody = document.getElementById('pendingRepairApprovals');
                tbody.innerHTML = '';

                if (data.success && data.repairs && data.repairs.length > 0) {
                    data.repairs.forEach(repair => {
                        const cost = repair.estimated_cost ? Number(repair.estimated_cost).toLocaleString(
                            'vi-VN') + ' đ' : 'Chưa có';
                        const roomLabel = repair.room_name || (
                            `Tòa ${repair.building} - ${repair.room_name || ''}`.trim());
                        const row = `<tr>
								<td><strong>${roomLabel}</strong></td>
								<td title="${repair.description}">${repair.description.substring(0, 30)}${repair.description.length > 30 ? '...' : ''}</td>
								<td class="text-center">${cost}</td>
								<td>
									<div class="btn-group">
										<button type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
											Hành động
										</button>
										<ul class="dropdown-menu dropdown-menu-end">
											<li>
												<a class="dropdown-item" href="javascript:void(0)" onclick="viewRepairDetail(${repair.repair_id})">
													<i class="align-middle me-2" data-feather="eye"></i> Xem chi tiết
												</a>
											</li>
											<li><hr class="dropdown-divider"></li>
											<li>
												<a class="dropdown-item" href="javascript:void(0)" onclick="approveRepair(${repair.repair_id})">
													<i class="align-middle me-2" data-feather="check"></i> Phê duyệt
												</a>
											</li>
											<li>
												<a class="dropdown-item text-danger" href="javascript:void(0)" onclick="rejectRepair(${repair.repair_id})">
													<i class="align-middle me-2" data-feather="x"></i> Từ chối
												</a>
											</li>
										</ul>
									</div>
								</td>
							</tr>`;
                        tbody.innerHTML += row;
                    });
                    // Re-init feather icons for new buttons
                    if (typeof feather !== 'undefined') feather.replace();
                } else {
                    tbody.innerHTML =
                        '<tr><td colspan="4" class="text-center text-muted">Không có yêu cầu nào cần phê duyệt</td></tr>';
                }
            })
            .catch(error => {
                console.error('Error loading pending approvals:', error);
                document.getElementById('pendingRepairApprovals').innerHTML =
                    '<tr><td colspan="4" class="text-center text-danger">Lỗi tải dữ liệu</td></tr>';
            });
    }

    // Phê duyệt yêu cầu sửa chữa
    function approveRepair(repairId) {
        if (!confirm('Bạn có chắc muốn phê duyệt yêu cầu này?')) return;

        fetch('../../../backend/controller/manager/ManagerRepairController.php?action=approve', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    repair_id: repairId
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Đã phê duyệt thành công!');
                    loadPendingRepairApprovals();
                    loadRepairStatistics();
                } else {
                    alert('Lỗi: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Có lỗi xảy ra!');
            });
    }

    // Xem chi tiết yêu cầu sửa chữa
    function viewRepairDetail(repairId) {
        fetch(
                `../../../backend/controller/manager/ManagerRepairController.php?action=get-request-details&repair_id=${repairId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success && data.request) {
                    const repair = data.request;
                    const cost = repair.estimated_cost ? Number(repair.estimated_cost).toLocaleString('vi-VN') +
                        ' VND' : 'Chưa có';
                    const actualCost = repair.actual_cost ? Number(repair.actual_cost).toLocaleString('vi-VN') +
                        ' VND' : 'Chưa có';
                    const roomLabel = repair.room_name || `Tòa ${repair.building} - N/A`;

                    // Xác định badge cho trạng thái
                    let statusBadge = 'bg-secondary';
                    if (repair.status === 'Chờ xử lý') statusBadge = 'bg-warning';
                    else if (repair.status === 'Đang sửa') statusBadge = 'bg-info';
                    else if (repair.status === 'Hoàn thành') statusBadge = 'bg-success';
                    else if (repair.status === 'Từ chối') statusBadge = 'bg-danger';

                    // Xác định trạng thái phê duyệt
                    let approvalStatus = 'Chưa gửi';
                    let approvalBadge = 'bg-secondary';
                    if (repair.approved_by) {
                        approvalStatus = 'Đã phê duyệt';
                        approvalBadge = 'bg-success';
                    } else if (repair.received_by) {
                        approvalStatus = 'Chờ phê duyệt';
                        approvalBadge = 'bg-warning';
                    }

                    const detailHTML = `
							<div class="row">
								<div class="col-md-6">
									<h6 class="mb-3 text-primary"><i class="align-middle" data-feather="info"></i> Thông tin chung</h6>
									<table class="table table-sm table-borderless">
										<tr>
											<th width="45%">Phòng:</th>
											<td><strong>${roomLabel}</strong></td>
										</tr>
										<tr>
											<th>Sinh viên:</th>
											<td>${repair.student_name || 'N/A'}</td>
										</tr>
										<tr>
											<th>Điện thoại:</th>
											<td>${repair.student_phone || 'N/A'}</td>
										</tr>
										<tr>
											<th>Email:</th>
											<td>${repair.student_email || 'N/A'}</td>
										</tr>
										<tr>
											<th>Trạng thái:</th>
											<td><span class="badge ${statusBadge}">${repair.status}</span></td>
										</tr>
										<tr>
											<th>Mức độ:</th>
											<td><span class="badge ${repair.priority === 'Khẩn cấp' ? 'bg-danger' : 'bg-info'}">${repair.priority || 'Thường'}</span></td>
										</tr>
										<tr>
											<th>Ngày báo cáo:</th>
											<td>${repair.report_date ? new Date(repair.report_date).toLocaleString('vi-VN') : 'N/A'}</td>
										</tr>
										${repair.received_by || repair.staff_name ? `
										<tr>
											<th>Ngày tiếp nhận:</th>
											<td>${repair.report_date ? new Date(repair.report_date).toLocaleString('vi-VN') : 'N/A'}</td>
										</tr>
										` : ''}
										${repair.staff_name ? `
										<tr>
											<th>Người xử lý:</th>
											<td><strong class="text-primary">${repair.staff_name}</strong></td>
										</tr>
										` : ''}
										${repair.manager_name ? `
										<tr>
											<th>Người được chỉ định:</th>
											<td><strong class="text-info">${repair.manager_name}</strong></td>
										</tr>
										` : ''}
										<tr>
											<th>Chi phí ước tính:</th>
											<td><strong class="text-primary">${cost}</strong></td>
										</tr>
										${repair.actual_cost ? `
										<tr>
											<th>Chi phí thực tế:</th>
											<td><strong class="text-success">${actualCost}</strong></td>
										</tr>
										` : ''}
										<tr>
											<th>Phê duyệt:</th>
											<td><span class="badge ${approvalBadge}">${approvalStatus}</span></td>
										</tr>
									</table>
								</div>
								<div class="col-md-6">
									<h6 class="mb-3 text-primary"><i class="align-middle" data-feather="file-text"></i> Mô tả sự cố</h6>
									<div class="alert alert-light border">
										<textarea class="form-control border-0" rows="4" readonly disabled style="resize: none;">${repair.description || 'Không có mô tả'}</textarea>
									</div>
									
									${repair.image_url ? `
									<h6 class="mb-3 text-primary"><i class="align-middle" data-feather="image"></i> Hình ảnh</h6>
									<div class="text-center">
										<img src="../../../${repair.image_url}" class="img-fluid rounded shadow" alt="Hình ảnh sự cố" style="max-height: 300px; cursor: pointer;" onclick="window.open(this.src, '_blank')">
									</div>
									` : `
									<div class="alert alert-secondary text-center">
										<i class="align-middle" data-feather="image"></i>
										<p class="mb-0">Không có hình ảnh</p>
									</div>
									`}
								</div>
							</div>
						`;

                    document.getElementById('repairDetailContent').innerHTML = detailHTML;
                    const modal = new bootstrap.Modal(document.getElementById('repairDetailModal'));
                    modal.show();

                    // Re-init feather icons
                    if (typeof feather !== 'undefined') feather.replace();
                } else {
                    alert('Không thể tải chi tiết: ' + (data.message || 'Lỗi không xác định'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Có lỗi xảy ra khi tải chi tiết!');
            });
    }

    // Từ chối yêu cầu sửa chữa
    function rejectRepair(repairId) {
        const reason = prompt('Nhập lý do từ chối:');
        if (reason === null) return;

        fetch('../../../backend/controller/manager/ManagerRepairController.php?action=reject', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    repair_id: repairId,
                    reason: reason
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Đã từ chối yêu cầu!');
                    loadPendingRepairApprovals();
                    loadRepairStatistics();
                } else {
                    alert('Lỗi: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Có lỗi xảy ra!');
            });
    }
    </script>

    <!-- Modal Chi Tiết Yêu Cầu Sửa Chữa -->
    <div class="modal fade" id="repairDetailModal" tabindex="-1" aria-labelledby="repairDetailModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="repairDetailModalLabel">
                        <i class="align-middle me-2" data-feather="tool"></i>
                        Chi Tiết Yêu Cầu Sửa Chữa
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body" id="repairDetailContent">
                    <div class="text-center">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Đang tải...</span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                </div>
            </div>
        </div>
    </div>

</body>

</html>