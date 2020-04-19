<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLinkClicksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('link_clicks', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('link_id')->index();
            $table->string('link_type', 20)->index();
            $table->string('platform', 30)->nullable()->index();
            $table->string('device', 30)->nullable()->index();
            $table->string('browser', 30)->nullable()->index();
            $table->string('location', 5)->nullable()->index();
            $table->boolean('crawler')->default(0)->index();
            $table->string('referrer')->nullable();
            $table->string('ip')->nullable();
            $table->timestamp('created_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('link_clicks');
    }
}
