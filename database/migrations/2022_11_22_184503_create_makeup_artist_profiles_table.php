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
            $table->unsignedBigInteger('artist_id')->nullable();
            $table->foreign('artist_id')->on('users')->references('id')->onDelete('cascade');
            $table->string('artist_business_name')->nullable();
            $table->string('artist_business_email')->nullable();
            $table->string('artist_business_phone')->nullable();
            $table->string('artist_location')->nullable();
            $table->boolean('is_featured_artist')->default(true);
            $table->text('artist_about')->nullable();
            $table->string('artist_working_since')->nullable();
            $table->boolean('artist_can_do_makeup_at')->nullable()->comment("1=Studio & your Venue both place,0=Only your Venue");
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
