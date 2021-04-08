<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductVariation extends Model
{
    use HasFactory;
    protected $guarded = [];

    public $timestamps = false;

    public function get_attribute(){
        return $this->belongsTo(ProductAttribute::class, 'attribute_id', 'id');
    }

    // get variation details by variation_id in product front details page
    public function get_variationDetails(){
        return $this->hasMany(ProductVariationDetails::class, 'variation_id', 'id');
    }
}
