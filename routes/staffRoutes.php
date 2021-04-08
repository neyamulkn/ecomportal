<?php

use Illuminate\Support\Facades\Route;

route::group(['middleware' => ['auth', 'admin']], function(){

});

//get product subcategory by category ID
Route::get('get/subcategory/{cat_id}', 'AjaxController@get_subcategory')->name('getSubCategory');
//get product sub child category by sub category ID
Route::get('get/subchild/category/{subcat_id}', 'AjaxController@get_subchild_category')->name('getSubChildCategory');

Route::get('get/attribute/{cat_id}', 'AjaxController@getAttributeByCategory')->name('getAttributeByCategory');
Route::get('get/brand/{cat_id}', 'AjaxController@getBrand')->name('getBrand');
// get product feature in product upload
Route::get('get/feature/{cat_id}', 'AjaxController@getFeature')->name('getFeature');
//get menu source
Route::get('get/menu/sourch/{type}', 'AjaxController@getMenuSourch')->name('getMenuSourch');

//get search keyword in header
Route::get('search/keyword', 'AjaxController@search_keyword')->name('search_keyword');

Route::get('status/change', 'AjaxController@statusChange')->name('statusChange');




