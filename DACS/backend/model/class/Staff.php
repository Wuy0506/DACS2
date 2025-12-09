<?php
class Staff {
    public $staff_id;
    public $position;
    public $hire_date;

    function __construct($id, $position, $hire_date) {
        $this->staff_id = $id;
        $this->position = $position;
        $this->hire_date = $hire_date;
    }
}
?>
