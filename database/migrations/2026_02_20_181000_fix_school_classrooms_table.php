<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Rebuild classrooms table to belong to departments instead of schools.
     * School can be accessed through: classroom->department->school
     */
    public function up(): void
    {
        // First drop the foreign key on courses that references this table
        Schema::table('school_courses', function (Blueprint $table) {
            $table->dropForeign(['classroom_id']);
        });

        // Drop the old table
        Schema::dropIfExists('school_classrooms');

        // Recreate with department_id instead of school_id
        Schema::create('school_classrooms', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('department_id')->constrained('school_departments')->onDelete('cascade');
            $table->string('name', 100); // Room 101, Lab A, etc.
            $table->string('code', 50)->nullable()->unique(); // A-101, B-205
            $table->string('building', 100)->nullable(); // Building A, Main Building
            $table->integer('floor')->nullable(); // 1, 2, 3
            $table->integer('capacity')->default(30); // How many students can fit
            $table->enum('type', ['lecture_hall', 'classroom', 'lab', 'seminar', 'auditorium', 'workshop'])->default('classroom');
            $table->json('equipment')->nullable(); // ["projector", "whiteboard", "computers"]
            $table->text('description')->nullable();
            $table->boolean('has_projector')->default(false);
            $table->boolean('has_whiteboard')->default(true);
            $table->boolean('has_ac')->default(false);
            $table->boolean('is_available')->default(true); // Available for scheduling
            $table->boolean('status')->default(true); // Active/Inactive
            $table->timestamps();
            $table->softDeletes();

            $table->index(['department_id', 'building']);
            $table->index(['department_id', 'type']);
        });

        // Re-add foreign key on courses
        Schema::table('school_courses', function (Blueprint $table) {
            $table->foreign('classroom_id')->references('id')->on('school_classrooms')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop foreign key on courses
        Schema::table('school_courses', function (Blueprint $table) {
            $table->dropForeign(['classroom_id']);
        });

        Schema::dropIfExists('school_classrooms');

        // Recreate original structure with school_id
        Schema::create('school_classrooms', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('school_id')->constrained('schools')->onDelete('cascade');
            $table->string('name', 100);
            $table->string('code', 50)->nullable()->unique();
            $table->string('building', 100)->nullable();
            $table->integer('floor')->nullable();
            $table->integer('capacity')->default(30);
            $table->enum('type', ['lecture_hall', 'classroom', 'lab', 'seminar', 'auditorium', 'workshop'])->default('classroom');
            $table->json('equipment')->nullable();
            $table->text('description')->nullable();
            $table->boolean('has_projector')->default(false);
            $table->boolean('has_whiteboard')->default(true);
            $table->boolean('has_ac')->default(false);
            $table->boolean('is_available')->default(true);
            $table->boolean('status')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['school_id', 'building']);
            $table->index(['school_id', 'type']);
        });

        // Re-add foreign key on courses
        Schema::table('school_courses', function (Blueprint $table) {
            $table->foreign('classroom_id')->references('id')->on('school_classrooms')->onDelete('set null');
        });
    }
};
