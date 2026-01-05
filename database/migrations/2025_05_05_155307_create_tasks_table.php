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
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->unsignedBigInteger('assigned_by'); // employer
            $table->unsignedBigInteger('assigned_to'); // employee or team leader
            $table->enum('priority', ['Low', 'Medium', 'High'])->default('Medium');
            $table->enum('status', ['Not Started', 'In Progress', 'Completed', 'Blocked'])->default('Not Started');
            $table->enum('role', ['Owner', 'Reviewer', 'Collaborator'])->default('Owner');
            $table->date('start_date')->nullable();
            $table->date('due_date')->nullable();
            $table->string('file_path');
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('assigned_by')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('assigned_to')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
