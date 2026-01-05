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
        Schema::create('employee_leaves', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id'); // Employee
            $table->string('department');
            $table->string('leave_type')->nullable(); // e.g., Vacation, Personal Reason
            $table->text('reason')->nullable(); // For "Other" or detailed input
            $table->date('from_date');
            $table->date('to_date');
            $table->string('approved_by')->nullable(); // Manager/Admin
            $table->string('status')->default('Pending'); // Pending, Approved, Rejected
            $table->string('document')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_leaves');
    }
};
