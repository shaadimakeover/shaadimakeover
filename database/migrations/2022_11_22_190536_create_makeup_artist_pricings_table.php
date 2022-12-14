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
        Schema::create('makeup_artist_pricings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('artist_id')->nullable();
            $table->foreign('artist_id')->on('users')->references('id')->onDelete('cascade');
            $table->foreignId('pricing_service_id')->nullable()->constrained()->onDelete('cascade');
            $table->float('price')->nullable();
            $table->text('description')->nullable();
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
        Schema::dropIfExists('makeup_artist_pricings');
    }
};
