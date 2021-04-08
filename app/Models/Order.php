<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Order extends Model
{
    use \App\Traits\Vendor;
    protected $guarded = [];

    public function customer(){
        return $this->belongsTo(User::class, 'user_id');
    }

    public function order_details(){
        return $this->hasMany(OrderDetail::class, 'order_id', 'order_id');
    }

    public function orderCancelReason(){
        return $this->hasMany(OrderCancelReason::class, 'order_id', 'order_id');
    }

    public function seller_order_details(){
        $vendor_id = $this->vendor_id();
        return $this->hasMany(OrderDetail::class, 'order_id', 'order_id')->where('vendor_id', $vendor_id);
    }

    public function shipping_method(){
        return $this->belongsTo(ShippingMethod::class, 'shipping_method_id');
    }

    public function shipping_state(){
        return $this->hasOne(State::class, 'name', 'shipping_region');
    }

    public function get_country(){
        return $this->hasOne(Country::class, 'id', 'billing_country');
    }

    public function get_state(){
        return $this->hasOne(State::class, 'id', 'billing_region');
    }
    public function get_city(){
        return $this->hasOne(City::class, 'id',  'billing_city');
    }
    public function get_area(){
        return $this->hasOne(Area::class, 'id','billing_area');
    }
}
