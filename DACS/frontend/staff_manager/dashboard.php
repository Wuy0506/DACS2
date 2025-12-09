<?php
// Kiểm tra đăng nhập và phân quyền qua PHP session
session_start();

include_once __DIR__ . '/../../backend/permission.php';
checkPermission(['manager','staff']);

$username = $_SESSION['user']['username'] ?? $_SESSION['user']['username'] ?? 'NV';
?>
<!DOCTYPE html>
<html lang="en" style="height: auto;">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard | DMS</title>
    <link rel="icon" href="../uploads/logo.png" />

    <!-- Font Awesome -->
    <link rel="stylesheet" href="../plugins/fontawesome-free/css/all.min.css">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <!-- Tempusdominus Bootstrap 4 -->
    <link rel="stylesheet" href="../plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css">
    <!-- iCheck -->
    <link rel="stylesheet" href="../plugins/icheck-bootstrap/icheck-bootstrap.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="../dist/css/adminlte.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="../dist/css/custom.css">
    <!-- overlayScrollbars -->
    <link rel="stylesheet" href="../plugins/overlayScrollbars/css/OverlayScrollbars.min.css">
    <!-- Daterange picker -->
    <link rel="stylesheet" href="../plugins/daterangepicker/daterangepicker.css">
    <!-- Summernote -->
    <link rel="stylesheet" href="../plugins/summernote/summernote-bs4.min.css">
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="../plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css">
    <!-- Flatpickr -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <!-- Custom Dashboard CSS -->
    <link rel="stylesheet" href="css/dasboard.css">
 
    
</head>

<body class="sidebar-mini layout-fixed control-sidebar-slide-open layout-navbar-fixed text-sm">
    <?php include('includes/include.php'); ?>

    <!-- Content Wrapper -->
    <div class="content-wrapper pt-3">
        <section class="content text-dark">
            <div class="container-fluid">
                <h1>Welcome, <span id="welcome-user">NV KTX</span>!</h1>
                <hr>
                <div class="row">
                    <div class="col-lg-4 col-md-4 col-sm-12">
                        <div class="info-box">
                            <span class="info-box-icon bg-gradient-secondary elevation-1"><i
                                    class="fas fa-building"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Total Dorms</span>
                                <span class="info-box-number text-right">4</span>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4 col-md-4 col-sm-12">
                        <div class="info-box">
                            <span class="info-box-icon bg-gradient-maroon elevation-1"><i
                                    class="fas fa-door-closed"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Total Rooms</span>
                                <span class="info-box-number text-right">6</span>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4 col-md-4 col-sm-12">
                        <div class="info-box">
                            <span class="info-box-icon bg-gradient-light elevation-1"><i
                                    class="fas fa-users"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Registered Students</span>
                                <span class="info-box-number text-right">2</span>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4 col-md-4 col-sm-12">
                        <div class="info-box">
                            <span class="info-box-icon bg-gradient-light elevation-1"><i class="fas fa-file"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Total Active Accounts</span>
                                <span class="info-box-number text-right">2</span>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4 col-md-4 col-sm-12">
                        <div class="info-box">
                            <span class="info-box-icon bg-gradient-warning elevation-1"><i
                                    class="fas fa-coins"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">This Month Total Collection</span>
                                <span class="info-box-number text-right">₱ 13,500.00</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Calendar Widget -->
                <div class="row">
                    <div class="col-lg-4 col-md-6 col-sm-12">
                        <div class="card">
                            <div class="card-header border-0 bg-white">
                                <h5 class="card-title mb-0" style="color: #999; font-weight: 500;">Calendar</h5>
                            </div>
                            <div class="card-body p-3">
                                <div id="calendar-widget"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            
        </section>
    </div>

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
                    <button type="button" class="btn btn-primary rounded-0" id='confirm' onclick="">Continue</button>
                    <button type="button" class="btn btn-secondary rounded-0" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

   <?php include 'includes/footer.php'; ?>
    </div>
    <!-- ./wrapper -->

    <!-- jQuery -->
    <script src="../plugins/jquery/jquery.min.js"></script>
    <!-- jQuery UI 1.11.4 -->
    <script src="../plugins/jquery-ui/jquery-ui.min.js"></script>
    <!-- SweetAlert2 -->
    <script src="../plugins/sweetalert2/sweetalert2.min.js"></script>
    <!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
    <script>
    $.widget.bridge('uibutton', $.ui.button);
    var _base_url_ = '../';
    </script>
    <!-- Bootstrap 4 -->
    <script src="../plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <!-- ChartJS -->
    <script src="../plugins/chart.js/Chart.min.js"></script>
    <!-- Select2 -->
    <script src="../plugins/select2/js/select2.full.min.js"></script>
    <!-- daterangepicker -->
    <script src="../plugins/moment/moment.min.js"></script>
    <script src="../plugins/daterangepicker/daterangepicker.js"></script>
    <!-- Tempusdominus Bootstrap 4 -->
    <script src="../plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js"></script>
    <!-- Summernote -->
    <script src="../plugins/summernote/summernote-bs4.min.js"></script>
    <!-- overlayScrollbars -->
    <script src="../plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js"></script>
    <!-- AdminLTE App -->
    <script src="../dist/js/adminlte.js"></script>
    <!-- Flatpickr -->
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

    <script>
    // Đồng bộ localStorage với PHP session
    const phpUsername = '<?php echo addslashes($username); ?>';
    if (phpUsername) {
        localStorage.setItem('loggedIn', 'true');
        localStorage.setItem('username', phpUsername);
    }

    // Set current year
    document.getElementById('current-year').textContent = new Date().getFullYear();

    // Hiển thị username
    const username = phpUsername || localStorage.getItem('username') || 'Admin';
    document.getElementById('welcome-user').textContent = username;

    // Initialize Calendar Widget
    document.addEventListener('DOMContentLoaded', function() {
        flatpickr('#calendar-widget', {
            inline: true,
            defaultDate: new Date(),
            prevArrow: "<span title='Previous month'>&laquo;</span>",
            nextArrow: "<span title='Next month'>&raquo;</span>",
            onChange: function(selectedDates, dateStr, instance) {
                console.log('Selected date:', dateStr);
            }
        });
    });

    function handleLogout() {
        // Xóa localStorage
        localStorage.removeItem('loggedIn');
        localStorage.removeItem('username');
        
        // Gọi logout API
        window.location.href = '../../backend/auth.php?logout=1';
    }

    // Preloader Functions
    function start_loader() {
        if ($('#preloader').length === 0) {
            $('body').append('<div id="preloader" style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9999; display: flex; align-items: center; justify-content: center;"><div class="spinner-border text-light" role="status" style="width: 3rem; height: 3rem;"><span class="sr-only">Loading...</span></div></div>');
        }
    }

    function end_loader() {
        $('#preloader').fadeOut('fast', function() {
            $(this).remove();
        });
    }

    // Toast Notification
    window.alert_toast = function($msg = 'TEST', $bg = 'success', $pos = '') {
        Swal.mixin({
            toast: true,
            position: $pos || 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true
        }).fire({
            icon: $bg,
            title: $msg
        });
    }

    // Universal Modal
    window.uni_modal = function($title = '', $url = '', $size = "") {
        start_loader();
        $('#uni_modal .modal-title').html($title);
        $('#uni_modal .modal-body').html('<div class="alert alert-info"><i class="fa fa-info-circle"></i> Demo: This would load form from: <code>' + $url + '</code></div>');
        if ($size != '') {
            $('#uni_modal .modal-dialog').addClass($size + ' modal-dialog-centered');
        } else {
            $('#uni_modal .modal-dialog').removeAttr("class").addClass("modal-dialog modal-md modal-dialog-centered");
        }
        $('#uni_modal').modal({
            show: true,
            backdrop: 'static',
            keyboard: false,
            focus: true
        });
        end_loader();
    }

    // Confirmation Modal
    window._conf = function($msg = '', $func = '', $params = []) {
        $('#confirm_modal #confirm').attr('onclick', $func + "(" + $params.join(',') + ")");
        $('#confirm_modal #delete_content').html($msg);
        $('#confirm_modal').modal('show');
    }
    </script>
</body>

</html>