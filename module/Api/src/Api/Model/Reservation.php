<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Api\Model;

use Api\Model\BaseModel;

class Reservation extends BaseModel {

    public $id;
    public $user_id;
    public $parking_id;
    public $starting_time;
    public $end_time;
    public $cost;
    public $status;
    public $created_at;
    public $updated_at;

}
