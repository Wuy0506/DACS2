<?php
class Bed {
    public $bed_id;
    public $room_id;
    public $bed_number;
    public $status;

    function __construct($id, $room_id, $bed_number, $status) {
        $this->bed_id = $id;
        $this->room_id = $room_id;
        $this->bed_number = $bed_number;
        $this->status = $status;
    }
}
?>
