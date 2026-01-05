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
        Schema::table('candidate_invoices', function (Blueprint $table) {
            $table->decimal('convenience_fees', 10, 2)->after('discount')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('candidate_invoices', function (Blueprint $table) {
            $table->dropColumn(['convenience_fees']);
        });
    }
};
