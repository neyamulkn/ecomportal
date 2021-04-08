<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSubModulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sub_modules', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('module_id');
            $table->string('submodule_name', 175);
            $table->string('slug', 175);
            $table->string('route', 125)->nullable();
            $table->tinyInteger('is_view_vissible')->nullable()->default(1);
            $table->tinyInteger('is_add_vissible')->nullable()->default(1);
            $table->tinyInteger('is_edit_vissible')->nullable()->default(1);
            $table->tinyInteger('is_delete_vissible')->nullable()->default(1);
            $table->tinyInteger('is_hidden_sidebar')->nullable();
            $table->tinyInteger('is_hidden_role_permission')->nullable();
            $table->tinyInteger('position')->default(0);
            $table->tinyInteger('status')->default(1);
            $table->integer('updated_by');
            $table->foreign('module_id')->references('id')->on('modules')->onDelete('cascade');
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
        Schema::dropIfExists('sub_modules');
    }
}
