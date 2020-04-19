<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLinkGroupLinkTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('link_group_link', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('link_id')->index();
            $table->integer('link_group_id')->index();
            $table->timestamps();

            $table->unique(['link_id', 'link_group_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('link_group_link');
    }
}
