<?php

namespace Api\Model;

use Api\Model\BaseModel;

class User extends BaseModel {

    public $id;
    public $email;
    public $username;
    public $display_name;
    public $password;
    public $first_name;
    public $last_name;
    public $middle_name;
    public $is_valid;
    public $created_at;
    public $updated_at;

}
