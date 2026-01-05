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
       Schema::table('clients', function (Blueprint $table) {
            $table->decimal('bank_account', 10, 2)->nullable()->after('gstin');
            $table->decimal('ifsc_code', 10, 2)->nullable()->after('bank_account');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->dropColumn(['pan_card','bank_account','ifsc_code']);
        });
    }
};
