<?php
class StatisticsLog {
    public $stat_id;
    public $report_type;
    public $data_json;
    public $generated_at;
    public $generated_by;

    function __construct($id, $report_type, $data_json, $generated_at, $generated_by) {
        $this->stat_id = $id;
        $this->report_type = $report_type;
        $this->data_json = $data_json;
        $this->generated_at = $generated_at;
        $this->generated_by = $generated_by;
    }
}
?>
