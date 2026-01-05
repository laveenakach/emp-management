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
             // Drop old column
            $table->dropColumn('department_id');
            // Add new column
            $table->string('department')->after('ifsc_code')->nullable(); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Drop old column
            $table->dropColumn('department_id');
            // Add new column
            $table->string('department')->after('ifsc_code')->nullable(); 
        });
    }
};
