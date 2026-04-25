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
        Schema::create('school_programs', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('school_id')->constrained('schools')->onDelete('cascade');
            $table->foreignId('department_id')->nullable()->constrained('school_departments')->onDelete('set null');
            $table->string('name');
            $table->string('code', 50)->nullable()->unique();
            $table->text('description')->nullable();
            $table->enum('degree_level', ['certificate', 'diploma', 'associate', 'bachelor', 'master', 'doctorate'])->default('bachelor');
            $table->integer('duration_years')->nullable();
            $table->integer('credits_required')->nullable();
            $table->decimal('tuition_fee', 10, 2)->nullable();
            $table->text('admission_requirements')->nullable();
            $table->string('program_coordinator')->nullable();
            $table->integer('max_students')->nullable();
            $table->integer('current_enrollment')->default(0);
            $table->string('accreditation_status')->nullable();
            $table->boolean('status')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['school_id', 'status']);
            $table->index(['department_id', 'status']);
            $table->index('degree_level');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('school_programs');
    }
};
