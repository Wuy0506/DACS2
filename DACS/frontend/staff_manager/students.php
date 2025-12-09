<?php 
include_once __DIR__ . '/../../backend/permission.php';
checkPermission(['manager','staff']);

include_once '../../backend/controller/quanLySinhVienController.php';

$controller = new quanLySinhVienController();
$students = $controller->getAll();

?>
<!DOCTYPE html>
<html lang="en" style="height: auto;">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Students | DMS</title>
    <link rel="icon" href="../uploads/logo.png" />
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="../plugins/fontawesome-free/css/all.min.css">
    <!-- DataTables -->
    <link rel="stylesheet" href="../plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="../plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
    <link rel="stylesheet" href="../plugins/datatables-buttons/css/buttons.bootstrap4.min.css">
    <!-- Select2 -->
    <link rel="stylesheet" href="../plugins/select2/css/select2.min.css">
    <link rel="stylesheet" href="../plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="../dist/css/adminlte.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="../dist/css/custom.css">
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="../plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css">

</head>
<body class="sidebar-mini layout-fixed control-sidebar-slide-open layout-navbar-fixed text-sm">
    
    <div class="wrapper">
        <?php include "includes/include.php"; ?>


        <!-- Content Wrapper -->
        <div class="content-wrapper pt-3">
            <section class="content text-dark">
                <div class="container-fluid">
                    <div class="card card-outline rounded-0 card-maroon">
                        <div class="card-header">
                            <h3 class="card-title">List of Students</h3>
                            <div class="card-tools">
                                <button class="btn btn-flat btn-primary" 
                                    onclick="uni_modal('Create Student', 'addStudentView.php')">
                                <span class="fas fa-plus"></span> Create New
                            </button>

                            </div>
                        </div>
                        <div class="card-body">
                            <div class="container-fluid">
                                <table class="table table-hover table-striped table-bordered" id="studentsTable">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Mã SV</th>
                                            <th>Họ và Tên</th>
                                            <th>Action</th>
                                            
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
foreach($students as $row){
                                        ?>
                                            <tr>
                                                <td><?= $row['user_id'] ?></td>
                                                <td><?= $row['student_id'] ?></td>
                                                <td><?= $row['full_name'] ?></td>
                                                
                                                <td align="center">
                                                    <div class="btn-group">
                                                        <button type="button" class="btn btn-flat p-1 btn-default btn-sm dropdown-toggle dropdown-icon" data-toggle="dropdown">
                                                            Action
                                                        </button>
                                                        <div class="dropdown-menu" role="menu">
                                                            <a class="dropdown-item" href="#" onclick="viewStudent(<?= $row['user_id'] ?>)">
                                                                <span class="fa fa-eye text-dark"></span> View
                                                            </a>
                                                            <div class="dropdown-divider"></div>
                                                            <a class="dropdown-item" href="#" onclick="editStudent(<?= $row['user_id'] ?>)">
                                                                <span class="fa fa-edit text-primary"></span> Edit
                                                            </a>
                                                            <div class="dropdown-divider"></div>
                                                            <a class="dropdown-item" href="#" onclick="deleteStudent(<?= $row['user_id'] ?>, '<?= $row['student_id'] ?>')">
                                                                <span class="fa fa-trash text-danger"></span> Delete
                                                            </a>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php
                                            }
                                        ?>
                                    </tbody>
                                </table>
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
                        <!-- <button type="button" class="btn btn-primary rounded-0" id='submit' onclick="$('#uni_modal form').submit()">Save</button> -->
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
        
        <!-- Footer -->
        <footer class="main-footer text-sm">
            <strong>Copyright © <span id="current-year"></span>.</strong>
            All rights reserved.
            <div class="float-right d-none d-sm-inline-block">
                <b>DMS - PHP (by: <a href="mailto:oretnom23@gmail.com" target="blank">oretnom23</a>)</b> v1.0
            </div>
        </footer>
    </div>
    <!-- ./wrapper -->

    <!-- jQuery -->
    <script src="../plugins/jquery/jquery.min.js"></script>
    <!-- jQuery UI -->
    <script src="../plugins/jquery-ui/jquery-ui.min.js"></script>
    <!-- SweetAlert2 -->
    <script src="../plugins/sweetalert2/sweetalert2.min.js"></script>
    <!-- Resolve conflict -->
    <script>
        $.widget.bridge('uibutton', $.ui.button);
        var _base_url_ = '../';
    </script>
    <!-- Bootstrap 4 -->
    <script src="../plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <!-- DataTables -->
    <script src="../plugins/datatables/jquery.dataTables.min.js"></script>
    <script src="../plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
    <script src="../plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
    <script src="../plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
    <!-- Select2 -->
    <script src="../plugins/select2/js/select2.full.min.js"></script>
    <!-- Summernote -->
    <script src="../plugins/summernote/summernote-bs4.min.js"></script>
    <!-- AdminLTE App -->
    <script src="../dist/js/adminlte.js"></script>
<script>

    
        document.getElementById('current-year').textContent = new Date().getFullYear();
       
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
            var Toast = Swal.mixin({
                toast: true,
                position: $pos || 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true
            });
            Toast.fire({
                icon: $bg,
                title: $msg
            });
        }
        
        // Universal Modal
        window.uni_modal = function($title = '', $url = '', $size = "") {
            start_loader();
            $('#uni_modal .modal-title').html($title);

            $.ajax({
                url: $url,
                error: err => {
                    console.log(err);
                    alert("Error loading page " + $url);
                    end_loader();
                },
                success: function(resp){
                    $('#uni_modal .modal-body').html(resp);

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
            });
        }
// Confirmation Modal
        window._conf = function($msg = '', $func = '', $params = []) {
            $('#confirm_modal #confirm').attr('onclick', $func + "(" + $params.join(',') + ")");
            $('#confirm_modal #delete_content').html($msg);
            $('#confirm_modal').modal('show');
        }
        
        // Initialize DataTable
        $(document).ready(function(){
            $('#studentsTable').DataTable({
                columnDefs: [
                    { orderable: false, targets: [3] }
                ],
                order: [[0, 'asc']]
            });
            
            $('.dataTable td, .dataTable th').addClass('py-1 px-2 align-middle');
        });
        
        function viewStudent(id) {
            uni_modal("<i class='fa fa-eye'></i> View Student Details", "view_student.php?id=" + id);
        }
        
        function editStudent(id) {
        
            uni_modal("<i class='fa fa-edit'></i> Edit Student", "updateStudentView.php?user_id=" + id);
        }

        
        function deleteStudent(id, code) {
            _conf("Are you sure to delete Student [<b>" + code + "</b>] permanently?", "delete_student", [id]);
        }

        
        function delete_student(id) {
            start_loader();
            
            $.ajax({
                url: "deleteStudent.php?id=" + id,
                method: "GET",
                success: function(resp){
                    let data = JSON.parse(resp);
                    end_loader();

                    if(data.status === "success"){
                        alert_toast("Student deleted successfully!", "success");
                        setTimeout(()=> location.reload(), 1000);
                    } else {
                        alert_toast("Delete failed: " + data.message, "error");
                    }
                }
            });
        }

    </script>

</body>
</html>