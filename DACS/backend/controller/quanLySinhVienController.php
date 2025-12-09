<?php
require_once __DIR__ . '../../model/staff/quanLySinhVienModel.php';
class quanLySinhVienController{
    private $model;

    public function __construct() {
        $this->model = new quanLySinhVienModel();
    }

    // API: lấy toàn bộ sinh viên
    public function getAll() {
        return $this->model->getAllStudent();
    }

    public function deleteSV($user_id){
        return $this->model->deleteStudent($user_id);
    }

    public function getOne($user_id){
        return $this->model->getById($user_id);
    }

    public function updateSV($data){
        return $this->model->updateStudent($data);
    }

    public function createSV($data){
    return $this->model->createStudent($data);
}



}


?>