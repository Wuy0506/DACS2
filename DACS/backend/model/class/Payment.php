<?php
class Payment {
    public $payment_id;
    public $student_id;
    public $contract_id;
    public $payment_type;
    public $amount;
    public $payment_date;
    public $payment_method;
    public $description;

    function __construct($id, $student_id, $contract_id, $payment_type, $amount, $payment_date, $payment_method, $description) {
        $this->payment_id = $id;
        $this->student_id = $student_id;
        $this->contract_id = $contract_id;
        $this->payment_type = $payment_type;
        $this->amount = $amount;
        $this->payment_date = $payment_date;
        $this->payment_method = $payment_method;
        $this->description = $description;
    }
}
?>
