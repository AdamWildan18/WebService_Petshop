<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $guarded = ['id'];
    public $timestamps = true;

    public function user()
    {
        return $this->belongsTo('app\Models\User');
    }

    public function order()
    {
        return $this->hasMany('App\Models\Order');
    }
}
