<?php
class Room {
    public $room_id;
    public $building;
    public $floor;
    public $capacity;
    public $available_beds;
    public $gender_restriction;
    public $price_per_month;
    public $status;

    function __construct($id, $building, $floor, $capacity, $available_beds, $gender_restriction, $price_per_month, $status) {
        $this->room_id = $id;
        $this->building = $building;
        $this->floor = $floor;
        $this->capacity = $capacity;
        $this->available_beds = $available_beds;
        $this->gender_restriction = $gender_restriction;
        $this->price_per_month = $price_per_month;
        $this->status = $status;
    }
}
?>
