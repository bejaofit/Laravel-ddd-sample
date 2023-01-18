<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('event', function (Blueprint $table) {
            $table->id();
            $table->string('eventName')->index();
            $table->string('relatedId')->index();
            $table->string('initiatorId')->index()->nullable();
            $table->string('stream')->index()->nullable();
            $table->integer('userId');
            $table->integer('occurredOn');
            $table->json('data');
        });
        Schema::create('tables', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('bookId')->index()->nullable();
            $table->integer('guests');
            $table->string('status')->index();
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
        //
    }
};
