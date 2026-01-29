<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Schema::table('enum_of_tasks', function (Blueprint $table) {
        //     //
        // });

        DB::statement("
            ALTER TABLE tasks 
            MODIFY status ENUM(
                'Not Started',
                'Submitted',
                'Approved',
                'Rejected'
            ) 
            DEFAULT 'Not Started'
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('enum_of_tasks', function (Blueprint $table) {
            //
        });
    }
};
