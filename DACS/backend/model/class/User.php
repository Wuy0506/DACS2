<?php
class User {
    public $user_id;
    public $username;
    public $password;
    public $full_name;
    public $email;
    public $phone;
    public $role;

    function __construct($id, $username, $password, $full_name, $email, $phone, $role) {
        $this->user_id = $id;
        $this->username = $username;
        $this->password = $password;
        $this->full_name = $full_name;
        $this->email = $email;
        $this->phone = $phone;
        $this->role = $role;
    }
}
?>
