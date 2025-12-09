<?php
class SystemSettings {
    public $setting_id;
    public $setting_name;
    public $setting_value;
    public $last_updated;
    public $updated_by;

    function __construct($id, $name, $value, $last_updated, $updated_by) {
        $this->setting_id = $id;
        $this->setting_name = $name;
        $this->setting_value = $value;
        $this->last_updated = $last_updated;
        $this->updated_by = $updated_by;
    }
}
?>
