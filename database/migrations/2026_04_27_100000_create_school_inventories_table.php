<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('school_inventories', function (Blueprint $table) {
            $table->id();
            $table->uuid()->unique();

            // Identity
            $table->string('asset_tag')->unique()->index();
            $table->string('serial_number')->nullable()->index();
            $table->string('name')->nullable();

            // Catalogue / type (which Equipment row this physical item is an instance of)
            $table->foreignId('equipment_id')
                ->constrained('school_equipment')
                ->restrictOnDelete();

            // Current location: at most one of classroom / department / employee
            $table->foreignId('classroom_id')
                ->nullable()
                ->constrained('school_classrooms')
                ->nullOnDelete();
            $table->foreignId('department_id')
                ->nullable()
                ->constrained('school_departments')
                ->nullOnDelete();
            $table->foreignId('assigned_to_user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            // Lifecycle
            $table->string('status')->default('in_stock')
                ->comment('in_stock|in_use|maintenance|retired|lost|disposed');
            $table->string('condition')->default('good')
                ->comment('new|good|fair|poor');

            // Acquisition
            $table->date('purchased_at')->nullable();
            $table->decimal('cost', 12, 2)->nullable();
            $table->string('vendor')->nullable();
            $table->date('warranty_until')->nullable();

            $table->text('notes')->nullable();

            // Photos / receipts / docs (URLs returned by the MediaLibrary).
            $table->json('images')->nullable();

            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['equipment_id', 'status']);
            $table->index(['classroom_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('school_inventories');
    }
};
