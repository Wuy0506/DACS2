<?php
class Contract {
    public $contract_id;
    public $registration_id;
    public $created_date;
    public $end_date;
    public $status;

    function __construct($id, $registration_id, $created_date, $end_date, $status) {
        $this->contract_id = $id;
        $this->registration_id = $registration_id;
        $this->created_date = $created_date;
        $this->end_date = $end_date;
        $this->status = $status;
    }
}
?>
