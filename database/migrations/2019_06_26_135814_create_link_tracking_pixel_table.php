<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLinkTrackingPixelTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('link_tracking_pixel', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('link_id')->index();
            $table->integer('tracking_pixel_id')->index();
            $table->timestamps();

            $table->unique(['link_id', 'tracking_pixel_id'], 'link_pixel_unique');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('link_tracking_pixel');
    }
}
