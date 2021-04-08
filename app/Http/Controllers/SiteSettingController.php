<?php

namespace App\Http\Controllers;

use App\Models\SiteSetting;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SiteSettingController extends Controller
{
    // Status change function
    public function siteSettingActiveDeactive(Request $request){

        $status = DB::table('site_settings')->where('type', $request->field)->first();
        $field =  $request->field;
        if($status){
            if($status->value == 1){
                DB::table('site_settings')->where('type', $request->field)->update(['value' => 0]);
            }else{
                DB::table('site_settings')->where('type', $request->field)->update(['value' => 1]);
            }
            $output = array( 'status' => true, 'message' => $field. ' update successful.');
        }else{
            $output = array( 'status' => false, 'message' => $field. ' can\'t update.!');
        }
        return response()->json($output);
    }

    public function siteSettingUpdate(Request $request){
        $data = $request->except('_token');
        foreach($data as $field => $value) {
            SiteSetting::where('type', $field)->update(['value' => $value]);
        }
        Toastr::success('Update success');
        return back();
    }
}
