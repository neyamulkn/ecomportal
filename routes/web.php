<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
*/

define('SUPER_ADMIN', 1);
define('ADMIN', 2);
define('VENDOR', 3);
define('STAFF', 4);
define('USER', 5);

Auth::routes();

//reset for
Route::get('password/recover', 'Auth\ForgotPasswordController@passwordRecover')->name('password.recover');
//forgot password notify send
Route::match(array('GET','POST'), 'password/recover/notify', 'Auth\ForgotPasswordController@passwordRecoverNotify')->name('password.recover');
//verify token or otp
Route::get('password/recover/verify', 'Auth\ForgotPasswordController@passwordRecoverVerify')->name('password.recoverVerify');
//passord update
Route::post('password/recover/update', 'Auth\ForgotPasswordController@passwordRecoverUpdate')->name('password.recoverUpdate');


Route::get('/converter', 'HomeController@converter');
Route::get('/example1', 'SslCommerzPaymentController@exampleEasyCheckout');
Route::get('/example2', 'SslCommerzPaymentController@exampleHostedCheckout');

// Route::get('/pay', 'SslCommerzPaymentController@sslCommerzPayment');
// Route::post('/pay-via-ajax', 'SslCommerzPaymentController@payViaAjax');

Route::post('/sslcommerz/success', 'SslCommerzPaymentController@success');
Route::post('/sslcommerz/fail', 'SslCommerzPaymentController@fail');
Route::post('/sslcommerz/cancel', 'SslCommerzPaymentController@cancel');

Route::post('/sslcommerz/ipn', 'SslCommerzPaymentController@ipn');
//SSLCOMMERZ END

Route::get('sitemap','SitemapController@index');
Route::get('sitemap.xml','SitemapController@index');
Route::get('sitemap.xml/products','SitemapController@products');
Route::get('sitemap.xml/categories','SitemapController@categories');

Route::get('category/sitemap', 'SitemapController@catSitemap')->name('category-sitemap');

Route::get('/', 'HomeController@index')->name('home');
Route::get('category/{catslug?}/{subslug?}/{childslug?}', 'HomeController@category')->name('home.category');
//search products
Route::get('search', 'HomeController@search')->name('product.search');

Route::get('product/{slug?}', 'HomeController@product_details')->name('product_details');

Route::get('cart/add', 'CartController@cartAdd')->name('cart.add');
Route::get('cart', 'CartController@cartView')->name('cart');
Route::get('cart/update', 'CartController@cartUpdate')->name('cart.update');
Route::get('cart/item/remove/{id}', 'CartController@itemRemove')->name('cart.itemRemove');
Route::get('cart/remove/allitem', 'CartController@clearCart')->name('cart.clear');
Route::get('cart/view/header', 'AjaxController@getCartHead')->name('getCartHead');

//apply coupon
Route::get('coupon/apply', 'HomeController@couponApply')->name('coupon.apply');
//add to cart for direct buy
Route::post('buy/direct', 'HomeController@buyDirect')->name('buyDirect');
Route::get('checkout/{buy_product_id?}', 'User\CheckoutController@checkout')->name('checkout');
Route::get('checkout/shipping/{buy_product_id?}', 'User\CheckoutController@shipping')->name('shipping');

//paypal payment
Route::get('order/paypal/payment/{orderId}', 'PaypalController@paypalPayment')->name('paypalPayment');
Route::get('paypal/payment/status/success', 'PaypalController@paymentSuccess')->name('paypalPaymentSuccess');
Route::get('paypal/payment/status/cancel', 'PaypalController@paymentCancel')->name('paypalPaymentCancel');


//order tracking
Route::get('order-tracking', 'User\OrderController@orderTracking')->name('orderTracking');

Route::get('check/unique/value', 'AjaxController@checkField')->name('checkField');
//product quickview
Route::get('quickview/product/{product_id}', 'HomeController@quickview')->name('quickview');


//seller store routes
Route::get('shop', 'ShopController@shop')->name('shop');
Route::get('shop/{shop_name}', 'ShopController@shop_details')->name('shop_details');
Route::get('shop/{shop_name}/products/{catslug?}', 'ShopController@seller_products')->name('seller_products');
Route::get('shop/{shop_name}/reviews', 'ShopController@shop_reviews')->name('seller_reviews');

Route::get('blog', 'BlogController@blog')->name('blog');
Route::get('blog/{slug}', 'BlogController@blog_details')->name('blog_details');

Route::get('more/{slug}', 'HomeController@moreProducts')->name('moreProducts');

Route::get('offers', 'OfferController@offers')->name('offers');
Route::get('offer/{slug}', 'OfferController@offerDetails')->name('offer.details');
Route::get('offer/{slug}/purchase/shipping/address', 'OfferController@buyOffer')->name('offer.buyOffer')->middleware('auth');
Route::get('offer/get/city/{id}', 'OfferController@getCity')->name('offer.getCity');
Route::post('offer/shipping/address/insert', 'OfferController@shippingAddressInsert')->name('offer.shippingAddressInsert')->middleware('auth');

Route::get('offer/get/shipping/address/{id}', 'OfferController@getShippingAddress')->name('offer.getShippingAddress')->middleware('auth');

Route::match(array('GET','POST'), 'offer/payment/process/', 'OfferController@processToPay')->name('offer.processToPay')->middleware('auth');

Route::get('offer/product/select/robotics', 'OfferController@offerPrizeSelect')->name('offer.prizeSelect');
Route::get('offer/winner/product/{order_id}', 'OfferController@offerPrizeWinner')->name('offer.prizeWinner');

Route::get('offer/{slug}/404', 'OfferController@offer404')->name('offer404');

//pay shipping cost
Route::get('offer/shipping/cost/{order_id}', 'OfferController@shippingCostPayment')->name('shippingCostPayment');

Route::get('gift-cards', 'HomeController@giftCards')->name('giftCards');

Route::get('brand', 'FrontPagesController@topBrand')->name('topBrand');
Route::get('brand/{slug}', 'FrontPagesController@brandProducts')->name('brandProducts');

Route::get('today-deals', 'FrontPagesController@todayDeals')->name('todayDeals');
Route::get('mega-discount', 'FrontPagesController@megaDiscount')->name('megaDiscount');
Route::get('{page}', 'FrontPagesController@page')->name('page');

Route::get('social-login/redirect/{provider}', 'SocialLoginController@redirectToProvider')->name('social.login');
Route::get('social-login/{provider}/callback', 'SocialLoginController@handleProviderCallback')->name('social.callback');

Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('home');




