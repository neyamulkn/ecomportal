<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->integer('vendor_id')->nullable();
            $table->string('title');
            $table->string('slug');
            $table->text('summery')->nullable();
            $table->longText('description');
            $table->integer('category_id');
            $table->integer('subcategory_id')->nullable();
            $table->integer('childcategory_id')->nullable();
            $table->integer('brand_id')->nullable();
            $table->integer('cart_button_id')->nullable();
            $table->char('feature_image', 225)->default('default.png')->nullable();
            $table->decimal('purchase_price')->default(0);
            $table->decimal('selling_price')->default(0);
            $table->char('discount',5)->nullable();
            $table->string('stock', 15)->nullable();
            $table->integer('total_stock')->default(0);
            $table->date('manufacture_date')->nullable();
            $table->date('expired_date')->nullable();
            $table->string('sku', 75)->nullable();
            $table->string('hsn')->nullable();
            $table->integer('views')->default(0);
            $table->tinyInteger('featured')->nullable();
            $table->tinyInteger('best')->nullable();
            $table->tinyInteger('top')->nullable();
            $table->tinyInteger('hot')->nullable();
            $table->tinyInteger('video')->nullable();
            $table->integer('weight')->nullable();
            $table->integer('length')->nullable();
            $table->integer('width')->nullable();
            $table->integer('height')->nullable();
            $table->tinyInteger('cash_on_delivery')->nullable();
            $table->char('shipping_method', 10)->nullable();
            $table->decimal('order_price_above')->nullable();
            $table->integer('order_qty')->nullable();
            $table->tinyInteger('free_shipping')->nullable();
            $table->integer('ship_region_id')->nullable();
            $table->decimal('shipping_cost')->nullable();
            $table->decimal('discount_shipping_cost')->nullable();
            $table->decimal('other_region_cost')->nullable();
            $table->char('shipping_time', 50)->nullable();
            $table->integer('sales')->default(0);
            $table->double('avg_ratting')->default(0);
            $table->string('meta_title')->nullable();
            $table->text('meta_keywords')->nullable();
            $table->text('meta_description')->nullable();
            $table->text('meta_image')->nullable();
            $table->integer('created_by');
            $table->integer('updated_by')->nullable();
            $table->integer('position')->nullable();
            $table->softDeletes();
            $table->tinyInteger('approved')->default(0);
            $table->string('status', '10')->default('pending')->comment('pending,active,reject');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('products');
    }
}
