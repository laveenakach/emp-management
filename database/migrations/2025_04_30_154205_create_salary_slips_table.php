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
        Schema::create('salary_slips', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('users')->onDelete('cascade');
            $table->string('month'); // e.g., "April 2025"
            $table->string('year');  // e.g., "2025"
            $table->string('file_path'); // Path to the uploaded PDF
            $table->boolean('is_viewed')->default(false); // Track if employee viewed it
            $table->integer('total_present_days');
            $table->integer('total_leave_days');
            $table->integer('total_absent_days');
            $table->decimal('basic_salary', 10, 2);
            $table->decimal('deductions', 10, 2)->nullable();
            $table->decimal('net_salary', 10, 2);
            $table->string('status')->default('generated'); // generated or paid
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('salary_slips');
    }
};
