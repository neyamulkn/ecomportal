<?php

namespace App\Providers;

use App\Models\GeneralSetting;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Paginator::useBootstrap();

        if(!Session::has('siteSetting')){
            Session::put('siteSetting', GeneralSetting::first());
        }
        Config::set('siteSetting', Session::get('siteSetting'));
        view()->share('siteSetting', Session::get('siteSetting'));
    }
}
