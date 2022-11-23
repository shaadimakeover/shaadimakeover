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
        Schema::create('makeup_artist_profiles', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('expert_id')->nullable();
            $table->foreign('expert_id')->on('users')->references('id')->onDelete('cascade');
            $table->string('formal_name')->nullable();
            $table->string('introduction')->nullable();
            $table->string('working_since')->nullable();
            $table->string('can_do_makeup_at')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->integer('rating')->nullable();
            $table->string('place_availability')->nullable();
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
        Schema::dropIfExists('makeup_artist_profiles');
    }
};
