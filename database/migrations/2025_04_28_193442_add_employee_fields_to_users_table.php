<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('mobile_no')->nullable();
            $table->integer('experience')->nullable();
            $table->string('photo')->nullable();
            $table->string('city')->nullable();
            $table->string('location')->nullable();
            $table->text('address')->nullable();
            $table->string('aadhar_card')->nullable();
            $table->string('pan_card')->nullable();
            $table->string('bank_account')->nullable();
            $table->string('ifsc_code')->nullable();
            $table->string('designation')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'mobile_no', 
                'experience', 
                'photo', 
                'city', 
                'location', 
                'address', 
                'aadhar_card', 
                'pan_card', 
                'bank_account', 
                'ifsc_code', 
                'designation'
            ]);
        });
    }
};
