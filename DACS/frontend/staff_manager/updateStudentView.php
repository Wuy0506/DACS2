<?php
include_once '../../backend/controller/quanLySinhVienController.php';

$controller = new quanLySinhVienController();
$id = $_GET['user_id'];
$student = $controller->getOne($id); // hàm getOne sẽ viết ở dưới
?>

<div class="container-fluid">
    <form action="" id="update-form">

        <input type="hidden" name="user_id" value="<?= $student['user_id'] ?>">

        <div class="form-group">
            <label>Họ và Tên</label>
            <input type="text" class="form-control" name="full_name" value="<?= $student['full_name'] ?>">
        </div>

        <div class="form-group">
            <label>Giới tính</label>
            <select class="form-control" name="gender">
    <option value="male"   <?= ($student['gender'] == 'Nam') ? 'selected' : '' ?>>Nam</option>
    <option value="female" <?= ($student['gender'] == 'Nữ') ? 'selected' : '' ?>>Nữ</option>
    <option value="other"  <?= ($student['gender'] == 'Khác') ? 'selected' : '' ?>>Khác</option>
</select>

        </div>


        <div class="form-group">
            <label>Email</label>
            <input type="email" class="form-control" name="email" value="<?= $student['email'] ?>">
        </div>

        <div class="form-group">
            <label>Phone</label>
            <input type="text" class="form-control" name="phone" value="<?= $student['phone'] ?>">
        </div>

        <div class="form-group">
            <label>Khoa</label>
            <input type="text" class="form-control" name="faculty" value="<?= $student['faculty'] ?>">
        </div>

        <div class="form-group">
            <label>Ngành</label>
            <input type="text" class="form-control" name="major" value="<?= $student['major'] ?>">
        </div>

        <div class="form-group">
            <label>Ngày sinh</label>
            <input type="date" class="form-control" name="date_of_birth" value="<?= $student['date_of_birth'] ?>">
        </div>

        <div class="form-group">
            <label>Địa chỉ</label>
            <input type="text" class="form-control" name="address" value="<?= $student['address'] ?>">
        </div>

        

        <button class="btn btn-primary">Save</button>

    </form>
</div>

<script>
$("#update-form").submit(function(e){
    e.preventDefault();
    start_loader();

    $.ajax({
        url: "updateStudent.php",
        method: "POST",
        data: $(this).serialize(),
        success: function(resp){
            end_loader();
            let data = JSON.parse(resp);

            if(data.status === "success"){
                alert_toast("Updated successfully!", "success");
                setTimeout(()=> location.reload(), 1000);
            } else {
                alert_toast("Update failed: " + data.message, "error");
            }
        }
    });
});
</script>