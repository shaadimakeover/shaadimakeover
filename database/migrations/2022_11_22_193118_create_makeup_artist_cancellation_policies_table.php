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
        Schema::create('makeup_artist_cancellation_policies', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('expert_id')->nullable();
            $table->foreign('expert_id')->on('users')->references('id')->onDelete('cascade');
            $table->text('cancellation_policy')->nullable();
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
        Schema::dropIfExists('makeup_artist_cancellation_policies');
    }
};
