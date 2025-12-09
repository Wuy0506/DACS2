<?php
class RoomRegistration {
    public $registration_id;
    public $student_id;
    public $room_id;
    public $start_date;
    public $end_date;
    public $status;
    public $request_date;
    public $approved_by;

    function __construct($id, $student_id, $room_id, $start_date, $end_date, $status, $request_date, $approved_by) {
        $this->registration_id = $id;
        $this->student_id = $student_id;
        $this->room_id = $room_id;
        $this->start_date = $start_date;
        $this->end_date = $end_date;
        $this->status = $status;
        $this->request_date = $request_date;
        $this->approved_by = $approved_by;
    }
}
?>
