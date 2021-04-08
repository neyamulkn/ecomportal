<?php

namespace App\Http\Controllers;

use App\Admin;
use App\Models\Transaction;
use App\User;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WalletController extends Controller
{
    public function index()
    {
        $data['customers'] = User::orderBy('name', 'asc')->get();
        return view('admin.customer.wallet')->with($data);
    }
    // store brand
    public function store(Request $request)
    {
    }

}
