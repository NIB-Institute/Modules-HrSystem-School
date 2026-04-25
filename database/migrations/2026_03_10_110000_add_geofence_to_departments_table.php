<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Adds geofence settings to departments for location-based attendance verification.
     * Industry standard: Define allowed scan locations with a radius (meters).
     */
    public function up(): void
    {
        Schema::table('school_departments', function (Blueprint $table) {
            // Geofence center point
            $table->decimal('latitude', 10, 8)->nullable()->after('office_location');
            $table->decimal('longitude', 11, 8)->nullable()->after('latitude');

            // Geofence radius in meters (default 100m = typical building/office)
            $table->unsignedInteger('geofence_radius')->default(100)->after('longitude');

            // Whether to enforce geofence (some departments may not require location verification)
            $table->boolean('enforce_geofence')->default(false)->after('geofence_radius');

            // Timezone for the department (for accurate time display)
            $table->string('timezone', 50)->nullable()->after('enforce_geofence');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('school_departments', function (Blueprint $table) {
            $table->dropColumn([
                'latitude',
                'longitude',
                'geofence_radius',
                'enforce_geofence',
                'timezone',
            ]);
        });
    }
};
