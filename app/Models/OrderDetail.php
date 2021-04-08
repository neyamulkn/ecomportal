<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderDetail extends Model
{
    protected $guarded = [];

    public function product(){
        return $this->belongsTo(Product::class);
    }
    public function order(){
        return $this->belongsTo(Order::class, 'order_id', 'order_id');
    }
    public function seller(){
        return $this->belongsTo(\App\Vendor::class, 'vendor_id');
    }
}
