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
        Schema::table('salary_slips', function (Blueprint $table) {
             $table->decimal('hra', 10, 2)->nullable()->after('basic_salary');
            $table->decimal('conveyance', 10, 2)->nullable()->after('hra');
            $table->decimal('medical', 10, 2)->nullable()->after('conveyance');
            $table->decimal('special_allowance', 10, 2)->nullable()->after('medical');
            $table->decimal('gross_salary', 10, 2)->nullable()->after('special_allowance');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('salary_slips', function (Blueprint $table) {
            $table->dropColumn(['hra', 'conveyance', 'medical', 'special_allowance', 'gross_salary']);
        });
    }
};
