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
        Schema::create('school_courses', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('department_id')->nullable()->constrained('school_departments')->onDelete('set null');
            $table->foreignId('program_id')->nullable()->constrained('school_programs')->onDelete('set null');
            $table->foreignId('instructor_id')->nullable()->constrained('employees')->onDelete('set null');
            $table->string('name');
            $table->string('code', 50)->nullable()->unique();
            $table->text('description')->nullable();
            $table->integer('credits')->default(3);
            $table->enum('type', ['required', 'elective', 'core'])->default('required');
            $table->integer('semester')->nullable();
            $table->integer('year')->nullable();
            $table->integer('max_students')->default(30);
            $table->integer('current_enrollment')->default(0);
            $table->string('schedule')->nullable();
            $table->string('room', 100)->nullable();
            $table->json('prerequisites')->nullable();
            $table->text('syllabus')->nullable();
            $table->boolean('status')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['department_id', 'status']);
            $table->index(['program_id', 'status']);
            $table->index('type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('school_courses');
    }
};
