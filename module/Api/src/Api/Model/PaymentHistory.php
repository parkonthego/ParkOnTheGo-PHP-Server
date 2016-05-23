<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Api\Model;

use Api\Model\BaseModel;

class PaymentHistory extends BaseModel {

    public $id;
    public $merchant_payment_id;	
    public $review_note;	
    public $amount;	
    public $datetime;	
    public $user_id;	
    public $reservation_id;
    public $created_at;
    public $updated_at;

}
