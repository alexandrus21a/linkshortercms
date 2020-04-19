<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLinkOverlayTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('link_overlays', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('user_id')->index();
            $table->string('position', 20);
            $table->string('message');
            $table->string('label')->nullable();
            $table->string('btn_link')->nullable();
            $table->string('btn_text')->nullable();
            $table->text('colors');
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
        Schema::dropIfExists('link_overlays');
    }
}
