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
        Schema::table('quotations', function (Blueprint $table) {
            $table->decimal('subtotal', 10, 2)->after('status')->nullable();
            $table->decimal('gst', 10, 2)->after('subtotal')->nullable();
            $table->decimal('grand_total', 10, 2)->after('gst')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quotations', function (Blueprint $table) {
            $table->dropColumn(['subtotal', 'gst', 'grand_total']);
        });
    }
};
