<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Transactions extends Model
{
    protected $table = 'transaction';

    protected $fillable = ['email', 'plan_id','gateway','payment_id','payment_status','payment_proof','user_id','payment_amount','date','coupon_code','coupon_percentage'];


	public $timestamps = false;


}
