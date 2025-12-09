<?php
include_once '../../backend/controller/quanLySinhVienController.php';

$controller = new quanLySinhVienController();

$id = $_GET['id'];
$student = $controller->getOne($id);

if(!$student){
    echo "<h4 class='text-danger'>Student not found!</h4>";
    exit;
}
?>

<div class="container-fluid">

    <div class="form-group">
        <label><b>User ID:</b></label>
        <div><?= $student['user_id'] ?></div>
    </div>

    <div class="form-group">
        <label><b>Username:</b></label>
        <div><?= $student['username'] ?></div>
    </div>

    <div class="form-group">
        <label><b>Họ và tên:</b></label>
        <div><?= $student['full_name'] ?></div>
    </div>

    <div class="form-group">
        <label><b>Email:</b></label>
        <div><?= $student['email'] ?></div>
    </div>

    <div class="form-group">
        <label><b>Số điện thoại:</b></label>
        <div><?= $student['phone'] ?></div>
    </div>

    <div class="form-group">
        <label><b>Mã sinh viên:</b></label>
        <div><?= $student['student_id'] ?></div>
    </div>

    <div class="form-group">
        <label><b>Khoa:</b></label>
        <div><?= $student['faculty'] ?></div>
    </div>

    <div class="form-group">
        <label><b>Ngành:</b></label>
        <div><?= $student['major'] ?></div>
    </div>

    <div class="form-group">
        <label><b>Giới tính:</b></label>
        <div>
            <?= $student['gender'] == 'male' ? 'Nam' : ($student['gender'] == 'female' ? 'Nữ' : 'Khác') ?>
        </div>
    </div>

    <div class="form-group">
        <label><b>Ngày sinh:</b></label>
        <div><?= $student['date_of_birth'] ?></div>
    </div>

    <div class="form-group">
        <label><b>Địa chỉ:</b></label>
        <div><?= $student['address'] ?></div>
    </div>

</div>