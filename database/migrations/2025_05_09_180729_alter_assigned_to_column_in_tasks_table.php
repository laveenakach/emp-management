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
        
         Schema::table('tasks', function (Blueprint $table) {
            // Drop the foreign key first
            $table->dropForeign(['assigned_to']);
        });

        Schema::table('tasks', function (Blueprint $table) {
            // Now you can safely modify the column
            $table->text('assigned_to')->change();
        });

        Schema::table('tasks', function (Blueprint $table) {
            // If you want to re-add the foreign key, make sure to point it to the correct table and column
            // Remove this if you're not re-adding the FK due to type incompatibility
            // $table->foreign('assigned_to')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            // Revert back to original type
            $table->dropForeign(['assigned_to']);
            $table->unsignedBigInteger('assigned_to')->change();
            $table->foreign('assigned_to')->references('id')->on('users');
        });
    }
};
