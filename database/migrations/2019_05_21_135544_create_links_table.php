<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLinksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('links', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title', 150)->nullable();
            $table->string('hash', 10)->index()->unique();
            $table->string('alias', 10)->index()->unique()->nullable();
            $table->string('long_url', 250);
            $table->integer('user_id')->nullable()->index();
            $table->integer('domain_id')->index()->nullable();
            $table->string('password')->nullable();
            $table->boolean('disabled')->default(0);
            $table->timestamp('expires_at')->nullable();
            $table->text('description')->nullable();
            $table->string('type', 30)->index();
            $table->integer('type_id')->index()->nullable();
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
        Schema::dropIfExists('links');
    }
}
