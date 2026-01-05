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
        Schema::table('bills', function (Blueprint $table) {
            $table->dropColumn('tax_percent'); // remove old field
            $table->decimal('gst_percent', 5, 2)->default(0.00)->after('discount');
            $table->decimal('cgst_percent', 5, 2)->default(0.00)->after('gst_percent');
            $table->decimal('sgst_percent', 5, 2)->default(0.00)->after('cgst_percent');
            $table->decimal('total_tax_percent', 5, 2)->default(0.00)->after('sgst_percent');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bills', function (Blueprint $table) {
            //
        });
    }
};
