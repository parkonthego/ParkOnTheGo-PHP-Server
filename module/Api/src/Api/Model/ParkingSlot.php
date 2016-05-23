<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Api\Model;

use Api\Model\BaseModel;

class ParkingSlot extends BaseModel {

    public $id;
    public $latitude;
    public $longitude;
    public $description;
    public $price;
    public $created_at;
    public $updated_at;

}
