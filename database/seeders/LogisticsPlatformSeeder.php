<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Src\Domain\DriverDomain\Models\Driver;
use Src\Domain\DriverDomain\Enums\DriverStatus;
use Src\Domain\OrderDomain\Models\Order;
use Src\Domain\OrderDomain\Enums\OrderStatus;

class LogisticsPlatformSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🚀 Seeding logistics platform data...');

        // Clear existing data
        $this->command->info('Clearing existing data...');
        Order::query()->delete();
        Driver::query()->delete();

        // Seed drivers
        $this->command->info('Creating drivers...');
        $this->seedDrivers();

        // Seed orders
        $this->command->info('Creating orders...');
        $this->seedOrders();

        $this->command->info('✅ Seeding completed successfully!');
        $this->command->info('📊 Summary:');
        $this->command->info('   - Drivers: ' . Driver::count());
        $this->command->info('   - Orders: ' . Order::count());
        $this->command->info('   - Available Drivers: ' . Driver::where('status', DriverStatus::Available)->count());
        $this->command->info('   - Pending Orders: ' . Order::where('status', OrderStatus::Pending)->count());
    }

    /**
     * Seed drivers with locations across Saudi Arabia.
     */
    private function seedDrivers(): void
    {
        $drivers = [
            [
                'name' => 'Ahmed Ali',
                'status' => 'available',
                'lat' => 24.7136,
                'lng' => 46.6753,
                'city' => 'Riyadh'
            ],
            [
                'name' => 'Mohammed Hassan',
                'status' => 'available',
                'lat' => 21.3891,
                'lng' => 39.8579,
                'city' => 'Jeddah'
            ],
            [
                'name' => 'Khalid Ibrahim',
                'status' => 'available',
                'lat' => 26.4207,
                'lng' => 50.0888,
                'city' => 'Dammam'
            ],
            [
                'name' => 'Omar Saleh',
                'status' => 'available',
                'lat' => 24.5247,
                'lng' => 39.5692,
                'city' => 'Medina'
            ],
            [
                'name' => 'Youssef Nasser',
                'status' => 'available',
                'lat' => 18.2164,
                'lng' => 42.5053,
                'city' => 'Abha'
            ],
            [
                'name' => 'Abdullah Mansour',
                'status' => 'available',
                'lat' => 24.7500,
                'lng' => 46.7000,
                'city' => 'Riyadh North'
            ],
            [
                'name' => 'Faisal Ahmad',
                'status' => 'available',
                'lat' => 21.4200,
                'lng' => 39.8200,
                'city' => 'Jeddah North'
            ],
            [
                'name' => 'Saeed Khalid',
                'status' => 'busy',
                'lat' => 26.3500,
                'lng' => 50.2000,
                'city' => 'Dammam South'
            ],
            [
                'name' => 'Hassan Ali',
                'status' => 'available',
                'lat' => 24.4667,
                'lng' => 39.6000,
                'city' => 'Medina West'
            ],
            [
                'name' => 'Ibrahim Yousef',
                'status' => 'available',
                'lat' => 18.2500,
                'lng' => 42.4500,
                'city' => 'Abha East'
            ],
        ];

        foreach ($drivers as $data) {
            // Insert with spatial data in one query
            DB::statement("
                INSERT INTO drivers (name, status, current_latitude, current_longitude, location, created_at, updated_at)
                VALUES (?, ?, ?, ?, ST_GeomFromText(?, 4326), NOW(), NOW())
            ", [
                $data['name'],
                $data['status'],
                $data['lat'],
                $data['lng'],
                "POINT({$data['lng']} {$data['lat']})"
            ]);

            $this->command->info("   ✓ Created driver: {$data['name']} ({$data['city']})");
        }
    }

    /**
     * Seed orders with pickup locations.
     */
    private function seedOrders(): void
    {
        $orders = [
            // Riyadh area orders
            [
                'lat' => 24.7200,
                'lng' => 46.6800,
                'status' => 'pending',
                'location' => 'Riyadh - Al Olaya'
            ],
            [
                'lat' => 24.6900,
                'lng' => 46.7200,
                'status' => 'pending',
                'location' => 'Riyadh - Al Malqa'
            ],
            [
                'lat' => 24.7400,
                'lng' => 46.6500,
                'status' => 'pending',
                'location' => 'Riyadh - King Fahd District'
            ],
            
            // Jeddah area orders
            [
                'lat' => 21.4000,
                'lng' => 39.8700,
                'status' => 'pending',
                'location' => 'Jeddah - Al Hamra'
            ],
            [
                'lat' => 21.3700,
                'lng' => 39.8400,
                'status' => 'pending',
                'location' => 'Jeddah - Al Rawdah'
            ],
            
            // Dammam area orders
            [
                'lat' => 26.4300,
                'lng' => 50.1000,
                'status' => 'pending',
                'location' => 'Dammam - Al Faisaliyah'
            ],
            [
                'lat' => 26.4000,
                'lng' => 50.0700,
                'status' => 'pending',
                'location' => 'Dammam - Al Shati'
            ],
            
            // Medina area orders
            [
                'lat' => 24.5300,
                'lng' => 39.5800,
                'status' => 'pending',
                'location' => 'Medina - Al Haram'
            ],
            [
                'lat' => 24.5100,
                'lng' => 39.5500,
                'status' => 'pending',
                'location' => 'Medina - Quba'
            ],
            
            // Abha area orders
            [
                'lat' => 18.2200,
                'lng' => 42.5100,
                'status' => 'pending',
                'location' => 'Abha - City Center'
            ],
            
            // Some assigned orders
            [
                'lat' => 24.7100,
                'lng' => 46.6700,
                'status' => 'assigned',
                'location' => 'Riyadh - Diplomatic Quarter'
            ],
            [
                'lat' => 21.3950,
                'lng' => 39.8650,
                'status' => 'assigned',
                'location' => 'Jeddah - Corniche'
            ],
            
            // Some completed orders
            [
                'lat' => 26.4250,
                'lng' => 50.0950,
                'status' => 'completed',
                'location' => 'Dammam - King Fahd Park'
            ],
            [
                'lat' => 24.5200,
                'lng' => 39.5700,
                'status' => 'completed',
                'location' => 'Medina - Al Madinah Museum'
            ],
            [
                'lat' => 18.2180,
                'lng' => 42.5080,
                'status' => 'completed',
                'location' => 'Abha - Al Soudah Park'
            ],
        ];

        foreach ($orders as $data) {
            // Insert with spatial data in one query
            DB::statement("
                INSERT INTO orders (status, pickup_latitude, pickup_longitude, pickup_location, created_at, updated_at)
                VALUES (?, ?, ?, ST_GeomFromText(?, 4326), NOW(), NOW())
            ", [
                $data['status'],
                $data['lat'],
                $data['lng'],
                "POINT({$data['lng']} {$data['lat']})"
            ]);

            $orderId = DB::getPdo()->lastInsertId();
            $this->command->info("   ✓ Created order #{$orderId}: {$data['location']} ({$data['status']})");
        }
    }
}
