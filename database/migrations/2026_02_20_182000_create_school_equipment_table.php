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
        // Equipment types table
        Schema::create('school_equipment', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('name', 100);
            $table->string('slug', 100)->unique();
            $table->string('icon', 50)->nullable(); // lucide icon name
            $table->text('description')->nullable();
            $table->enum('category', ['technology', 'furniture', 'safety', 'accessibility', 'other'])->default('other');
            $table->integer('qty')->default(0);
            $table->decimal('price_total', 12, 2)->nullable();
            $table->boolean('status')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index('category');
        });

        // Pivot table for classroom-equipment relationship (like product attributes)
        Schema::create('school_classroom_equipment', function (Blueprint $table) {
            $table->id();
            $table->foreignId('classroom_id')->constrained('school_classrooms')->onDelete('cascade');
            $table->foreignId('equipment_id')->constrained('school_equipment')->onDelete('cascade');
            $table->string('value', 255)->nullable(); // e.g., "Epson 4K", "Interactive 75-inch"
            $table->integer('quantity')->default(1);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['classroom_id', 'equipment_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('school_classroom_equipment');
        Schema::dropIfExists('school_equipment');
    }
};
