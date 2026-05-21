<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('drivers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('status', ['available', 'busy'])->default('available');
            $table->decimal('current_latitude', 10, 8);
            $table->decimal('current_longitude', 11, 8);
            $table->timestamps();

            // Index for status queries (finding available drivers)
            $table->index('status');
            
            // Composite index for geospatial + status queries
            $table->index(['status', 'current_latitude', 'current_longitude'], 'idx_driver_location_status');
        });

        // Add MySQL POINT column with SPATIAL index for optimized distance calculations
        DB::statement('ALTER TABLE drivers ADD COLUMN location POINT NOT NULL SRID 4326');
        DB::statement('ALTER TABLE drivers ADD SPATIAL INDEX idx_driver_spatial_location(location)');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('drivers');
    }
};
