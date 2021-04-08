<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;

class HelperController extends Controller
{
    static function ratting($ratting)
    {
        $output = '';
        for ($i = 1; $i <= 5; $i++) {
            if ($i <= round($ratting, 1))
                $output .= '<span class="fa fa-stack" > <i class="fa fa-star fa-stack-2x" ></i ></span >';
            else {
                if (is_float($ratting) && round($ratting, 0) == $i) {
                    $output .= '<span class="fa fa-stack" ><i class="fa fa-star-half-o fa-stack-2x" ></i ></span>';
                }else{
                    $output .= '<span class="fa fa-stack" ><i class="fa fa-star-o fa-stack-2x" ></i ></span>';
                }
            }
        }
        echo $output;
    }

    static function calculate_discount($selling_price, $discount, $discount_type){
        $selling_price = ($selling_price > 0) ? $selling_price : 1;
        if($discount_type == '%'){
            $price = $selling_price - ( $discount * $selling_price) / 100;
        }elseif($discount_type == 'fixed'){
            $price = $discount;
            $discount = $selling_price - $discount;
            //make persentage
            $discount = round(((($selling_price - $discount) - $selling_price)/$selling_price) * 100);
            $discount_type = Config::get('siteSetting.currency_symble');
        }elseif($discount_type == 'cash-back'){
            $price = $selling_price;
            $discount = $discount_type .' '. $discount;

        }else{
            $price = $selling_price - $discount;
            //make persentage
            $discount = round(((($selling_price - $discount) - $selling_price)/$selling_price) * 100);
            $discount_type = Config::get('siteSetting.currency_symble');
        }
        $output = [
            'price' => $price,
            'discount' => $discount,
            'discount_type' => $discount_type
        ];
        return $output;
    }

}
