<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTrackingPixelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tracking_pixels', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 80);
            $table->string('type', 40)->index();
            $table->string('pixel_id')->nullable();
            $table->integer('user_id')->index();
            $table->text('head_code')->nullable();
            $table->text('body_code')->nullable();
            $table->timestamps();

            $table->unique(['name', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tracking_pixels');
    }
}
