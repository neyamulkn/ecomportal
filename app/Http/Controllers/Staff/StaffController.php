<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\OrderDetail;
use App\Models\Product;
use App\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StaffController extends Controller
{
    public function dashboard()
    {
        $vendor_id = Auth::guard('vendor')->id();
        $data= [];
        $data['allProducts'] = Product::where('vendor_id', $vendor_id)->count();
        $data['pendingProducts'] = Product::where('vendor_id', $vendor_id)->where('status', 0)->count();
        $data['outOfStock'] = Product::where('vendor_id', $vendor_id)->where('stock', '<=', 0)->count();
        $data['rejectProducts'] = Product::where('vendor_id', $vendor_id)->where('status', 2)->count();
        $data['allOrders'] = OrderDetail::where('vendor_id', $vendor_id)->groupBy('order_id')->count();
        $data['pendingOrders'] = OrderDetail::where('vendor_id', $vendor_id)->where('shipping_status', 'pending')->groupBy('order_id')->count();
        $data['completeOrders'] = OrderDetail::where('vendor_id', $vendor_id)->where('shipping_status', 'complete')->groupBy('order_id')->count();
        $data['rejectOrders'] = OrderDetail::where('vendor_id', $vendor_id)->where('shipping_status', 'reject')->groupBy('order_id')->count();

        return view('vendors.dashboard')->with($data);

    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Vendor  $vendor
     * @return \Illuminate\Http\Response
     */
    public function show(Vendor $vendor)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Vendor  $vendor
     * @return \Illuminate\Http\Response
     */
    public function edit(Vendor $vendor)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Vendor  $vendor
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Vendor $vendor)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Vendor  $vendor
     * @return \Illuminate\Http\Response
     */
    public function destroy(Vendor $vendor)
    {
        //
    }
}
