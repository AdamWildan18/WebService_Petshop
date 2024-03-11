<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $guarded = [''];

    public $timestamp = true;

    public function user(){
        return $this->belongsTo('App\Models\User');
    }

    public function product(){
        return $this->belongsTo('App\Models\Product');
    }

}
