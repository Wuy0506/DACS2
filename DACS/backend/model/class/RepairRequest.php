<?php
class RepairRequest {
    public $repair_id;
    public $room_id;
    public $student_id;
    public $description;
    public $image_url;
    public $priority;
    public $status;
    public $report_date;
    public $received_by;
    public $approved_by;
    public $estimated_cost;
    public $actual_cost;

    function __construct($id, $room_id, $student_id, $description, $image_url, $priority, $status, $report_date, $received_by, $approved_by, $estimated_cost, $actual_cost) {
        $this->repair_id = $id;
        $this->room_id = $room_id;
        $this->student_id = $student_id;
        $this->description = $description;
        $this->image_url = $image_url;
        $this->priority = $priority;
        $this->status = $status;
        $this->report_date = $report_date;
        $this->received_by = $received_by;
        $this->approved_by = $approved_by;
        $this->estimated_cost = $estimated_cost;
        $this->actual_cost = $actual_cost;
    }
}
?>
