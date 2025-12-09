<div class="container-fluid">
    <form id="create-form">

        <h5><b>Thông tin tài khoản</b></h5>

        <div class="form-group">
            <label>Username</label>
            <input type="text" class="form-control" name="username" required>
        </div>

        <div class="form-group">
            <label>Password</label>
            <input type="password" class="form-control" name="password" required>
        </div>

        <h5 class="mt-3"><b>Thông tin sinh viên</b></h5>

        <div class="form-group">
            <label>Mã sinh viên</label>
            <input type="text" class="form-control" name="student_id" required>
        </div>

        <div class="form-group">
            <label>Họ và Tên</label>
            <input type="text" class="form-control" name="full_name" required>
        </div>

        <div class="form-group">
            <label>Giới tính</label>
            <select class="form-control" name="gender" required>
                <option value="male">Nam</option>
                <option value="female">Nữ</option>
                <option value="other">Khác</option>
            </select>
        </div>

        <div class="form-group">
            <label>Ngày sinh</label>
            <input type="date" class="form-control" name="date_of_birth" required>
        </div>

        <div class="form-group">
            <label>Khoa</label>
            <input type="text" class="form-control" name="faculty" required>
        </div>

        <div class="form-group">
            <label>Ngành</label>
            <input type="text" class="form-control" name="major" required>
        </div>

        <div class="form-group">
            <label>Địa chỉ</label>
            <input type="text" class="form-control" name="address" required>
        </div>

        <div class="form-group">
            <label>Email</label>
            <input type="email" class="form-control" name="email" required>
        </div>

        <div class="form-group">
            <label>Số điện thoại</label>
            <input type="text" class="form-control" name="phone" required>
        </div>

        <button class="btn btn-primary">Save</button>

    </form>
</div>

<script>
$("#create-form").submit(function(e){
    e.preventDefault();
    start_loader();

    $.ajax({
        url: "addStudent.php",
        method: "POST",
        data: $(this).serialize(),
        success: function(resp){
            end_loader();
            let data = JSON.parse(resp);

            if(data.status === "success"){
                alert_toast("Student created successfully!", "success");
                setTimeout(() => location.reload(), 1000);
            } else {
                alert_toast("Failed: " + data.message, "error");
            }
        }
    });
});
</script>