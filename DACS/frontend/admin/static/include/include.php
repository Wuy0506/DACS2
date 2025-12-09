<?php
    // Lấy tên file hiện tại (ví dụ: index.php, manager-staff.php)
    $activePage = basename($_SERVER['PHP_SELF']);

     $currentName = $_SESSION['user']['username'] ?? $_SESSION['user']['username'] ?? 'manager';
?>

<nav id="sidebar" class="sidebar js-sidebar">
    <div class="sidebar-content js-simplebar">
        <a class="sidebar-brand" href="index.php">
            <span class="align-middle">Quản lý KTX</span>
        </a>

        <ul class="sidebar-nav">
            <li class="sidebar-header">
                Trang chính
            </li>

            <li class="sidebar-item <?= ($activePage == 'index.php') ? 'active' : '' ?>">
                <a class="sidebar-link" href="index.php">
                    <i class="align-middle" data-feather="sliders"></i> <span class="align-middle">Dashboard</span>
                </a>
            </li>

            <li class="sidebar-header">
                Quản lý Nhân viên
            </li>

            <li class="sidebar-item <?= ($activePage == 'manager-staff.php') ? 'active' : '' ?>">
                <a class="sidebar-link" href="manager-staff.php">
                    <i class="align-middle" data-feather="users"></i> <span class="align-middle">Quản lý Nhân viên</span>
                </a>
            </li>

            <li class="sidebar-header">
                Cấu hình Hệ thống
            </li>

            <li class="sidebar-item <?= ($activePage == 'manager-settings.php') ? 'active' : '' ?>">
                <a class="sidebar-link" href="manager-settings.php">
                    <i class="align-middle" data-feather="settings"></i> <span class="align-middle">Cấu hình Hệ thống</span>
                </a>
            </li>

            <li class="sidebar-header">
                Báo cáo & Thống kê
            </li>

            <li class="sidebar-item <?= ($activePage == 'manager-statistics.php') ? 'active' : '' ?>">
                <a class="sidebar-link" href="manager-statistics.php">
                    <i class="align-middle" data-feather="bar-chart-2"></i> <span class="align-middle">Thống kê Tổng quan</span>
                </a>
            </li>

            <li class="sidebar-item <?= ($activePage == 'manager-revenue.php') ? 'active' : '' ?>">
                <a class="sidebar-link" href="manager-revenue.php">
                    <i class="align-middle" data-feather="dollar-sign"></i> <span class="align-middle">Doanh thu & Chi phí</span>
                </a>
            </li>

        </ul>
    </div>
</nav>

<div class="main">
    <nav class="navbar navbar-expand navbar-light navbar-bg">
        <a class="sidebar-toggle js-sidebar-toggle">
            <i class="hamburger align-self-center"></i>
        </a>

        <div class="navbar-collapse collapse">
            <ul class="navbar-nav navbar-align">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle d-none d-sm-inline-block" href="#" data-bs-toggle="dropdown">
                        <img src="img/avatars/avatar.jpg" class="avatar img-fluid rounded me-1" alt="Manager Avatar" id="user-avatar" /> 
                        
                        <span class="text-dark" id="user-display"><?= $currentName ?></span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-end">
                        <a class="dropdown-item" href="/backend/auth.php?action=logout" onclick="handleLogout(); return false;">Log out</a>
                    </div>
                </li>
            </ul>
        </div>
    </nav>
