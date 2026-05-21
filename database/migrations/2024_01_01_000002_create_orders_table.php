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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->enum('status', ['pending', 'assigned', 'completed'])->default('pending');
            $table->decimal('pickup_latitude', 10, 8);
            $table->decimal('pickup_longitude', 11, 8);
            $table->foreignId('driver_id')->nullable()->constrained('drivers')->nullOnDelete();
            $table->timestamps();

            // Index for status queries (finding pending/assigned orders)
            $table->index('status');
            
            // Index for driver assignments
            $table->index('driver_id');
            
            // Composite index for status + driver queries
            $table->index(['status', 'driver_id'], 'idx_order_status_driver');
            
            // Composite index for geospatial queries on pending orders
            $table->index(['status', 'pickup_latitude', 'pickup_longitude'], 'idx_order_location_status');
            
            // Index for timestamp-based queries (recent orders)
            $table->index('created_at');
        });

        // Add MySQL POINT column with SPATIAL index for optimized distance calculations
        DB::statement('ALTER TABLE orders ADD COLUMN pickup_location POINT NOT NULL SRID 4326');
        DB::statement('ALTER TABLE orders ADD SPATIAL INDEX idx_order_spatial_location(pickup_location)');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
