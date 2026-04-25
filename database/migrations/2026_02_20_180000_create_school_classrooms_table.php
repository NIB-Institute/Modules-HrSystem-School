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
        Schema::create('school_classrooms', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('school_id')->constrained('schools')->onDelete('cascade');
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

            $table->index(['school_id', 'building']);
            $table->index(['school_id', 'type']);
        });

        // Add classroom_id to courses table
        Schema::table('school_courses', function (Blueprint $table) {
            $table->foreignId('classroom_id')->nullable()->after('instructor_id')->constrained('school_classrooms')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('school_courses', function (Blueprint $table) {
            $table->dropConstrainedForeignId('classroom_id');
        });

        Schema::dropIfExists('school_classrooms');
    }
};
