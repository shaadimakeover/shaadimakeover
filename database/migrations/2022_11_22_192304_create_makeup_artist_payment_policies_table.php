<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('makeup_artist_payment_policies', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('artist_id')->nullable();
            $table->foreign('artist_id')->on('users')->references('id')->onDelete('cascade');
            $table->integer('percentage_of_pay')->nullable();
            $table->enum('time_to_pay', ['AT THE TIME OF BOOKING', 'ON EVENT DATE','AFTER DELIVERABLES ARE DELIVERED'])->nullable();
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
        Schema::dropIfExists('makeup_artist_payment_policies');
    }
};
