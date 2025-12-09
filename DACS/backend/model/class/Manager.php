<?php
class Manager {
    public $manager_id;
    public $department;
    public $hire_date;

    function __construct($id, $department, $hire_date) {
        $this->manager_id = $id;
        $this->department = $department;
        $this->hire_date = $hire_date;
    }
}
?>
