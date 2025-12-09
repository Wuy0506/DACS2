
    <?php
    $activePage = basename($_SERVER['PHP_SELF']);
    $currentName = $_SESSION['user']['username'] ?? $_SESSION['user']['username'] ?? 'User';
    ?>
    <style>
            .user-img {
                position: absolute;
                height: 27px;
                width: 27px;           
                object-fit: cover;
                left: -7%;
                top: -12%;
            }
            .btn-rounded {
                border-radius: 50px;
            }
            aside.main-sidebar {
                background-image: url('../uploads/default/portrait1.jpg') !important;
                background-repeat: no-repeat;
                background-size: cover;
                background-position: center center;
            }
        </style>
    <!-- Navbar -->
            <nav class="main-header navbar navbar-expand navbar-light shadow text-sm">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
                    </li>
                    <li class="nav-item d-none d-sm-inline-block">
                        <a href="#" class="nav-link">School Dormitory Management System - Admin</a>
                    </li>
                </ul>
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item">
                        <div class="btn-group nav-link">
                            <button type="button" class="btn btn-rounded badge badge-light dropdown-toggle dropdown-icon" data-toggle="dropdown">
                                <span><img src="../uploads/avatars/1.png" class="img-circle elevation-2 user-img" alt="User Image"></span>
                                <span class="ml-3" id="user-display"><?= $currentName ?></span>
                            </button>
                            <div class="dropdown-menu" role="menu">
                                <a class="dropdown-item" href="/backend/auth.php?action=logout" onclick="handleLogout()"><span class="fas fa-sign-out-alt"></span> Logout</a>
                            </div>
                        </div>
                    </li>
                </ul>
            </nav>


    <aside class="main-sidebar sidebar-light-maroon elevation-4 sidebar-no-expand">
        <a href="dashboard.php" class="brand-link bg-gradient-maroon text-sm text-light">
            <img src="../uploads/logo.png" alt="Logo" 
                class="brand-image img-circle elevation-3"
                style="opacity:.8;width:1.5rem;height:1.5rem;max-height:unset">
            <span class="brand-text font-weight-light">DMS - PHP</span>
        </a>

        <div class="sidebar">
            <nav class="mt-1">
                <ul class="nav nav-pills nav-sidebar flex-column text-sm nav-compact nav-flat" data-widget="treeview">

                    <li class="nav-item">
                        <a href="dashboard.php" class="nav-link <?= $activePage=='dashboard.php'?'active bg-gradient-maroon':'' ?>">
                            <i class="nav-icon fas fa-tachometer-alt"></i><p>Dashboard</p>
                        </a>
                    </li>

                    <li class="nav-header">Main</li>

                    <li class="nav-item">
                        <a href="students.php" class="nav-link <?= $activePage=='students.php'?'active bg-gradient-maroon':'' ?>">
                            <i class="nav-icon fas fa-users"></i><p>Student List</p>
                        </a>
                    </li>


                    <li class="nav-item">
                        <a href="contract.php" class="nav-link <?= $activePage=='contract.php'?'active bg-gradient-maroon':'' ?>">
                            <i class="nav-icon fas fa-file"></i><p>Hơp Đồng</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="payment.php" class="nav-link <?= $activePage=='payment.php'?'active bg-gradient-maroon':'' ?>">
                            <i class="nav-icon fas fa-file"></i><p>Thanh toán</p>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="repair_management.php" class="nav-link <?= $activePage=='repair_management.php'?'active bg-gradient-maroon':'' ?>">
                            <i class="nav-icon fas fa-building"></i><p>Sửa chữa</p>
                        </a>
                    </li>


                    <li class="nav-item">
                        <a href="rooms-management.php" class="nav-link <?= $activePage=='rooms-management.php'?'active bg-gradient-maroon':'' ?>">
                            <i class="nav-icon fas fa-door-open"></i><p>Quan Lí Phòng</p>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="UserManagemet.php" class="nav-link <?= $activePage=='UserManagemet.php'?'active bg-gradient-maroon':'' ?>">
                            <i class="nav-icon fas fa-clipboard-list"></i>
                            <p>Đăng Kí Phòng</p>
                        </a>
                    </li>

                </ul>
            </nav>
        </div>
    </aside>

    <!-- Modals -->
    <div class="modal fade" id="uni_modal" role='dialog'>
        <div class="modal-dialog modal-md modal-dialog-centered" role="document">
            <div class="modal-content rounded-0">
                <div class="modal-header">
                    <h5 class="modal-title"></h5>
                </div>
                <div class="modal-body"></div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary rounded-0" id='submit'
                        onclick="$('#uni_modal form').submit()">Save</button>
                    <button type="button" class="btn btn-secondary rounded-0" data-dismiss="modal">Cancel</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="confirm_modal" role='dialog'>
        <div class="modal-dialog modal-md modal-dialog-centered" role="document">
            <div class="modal-content rounded-0">
                <div class="modal-header">
                    <h5 class="modal-title">Confirmation</h5>
                </div>
                <div class="modal-body">
                    <div id="delete_content"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary rounded-0" id='confirm'
                        onclick="">Continue</button>
                    <button type="button" class="btn btn-secondary rounded-0" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>