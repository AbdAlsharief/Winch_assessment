# Quick Start Guide

Get the logistics platform running in **5 minutes**!

## Prerequisites Check

```bash
# Check PHP version (need 8.3+)
php -v

# Check Composer
composer --version

# Check Node.js (need 18+)
node -v

# Check MySQL
mysql --version
```

## Installation (Copy & Paste)

```bash
# 1. Install dependencies
composer install
npm install

# 2. Setup environment
cp .env.example .env
php artisan key:generate

# 3. Create database
mysql -u root -p -e "CREATE DATABASE logistics_platform CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# 4. Configure database (edit .env)
# Set your MySQL password in DB_PASSWORD

# 5. Run migrations
php artisan migrate

# 6. Build frontend
npm run build

# 7. Start server
php artisan serve
```

## Seed Test Data

```bash
php artisan tinker
```

Then paste this:

```php
use Src\Domain\DriverDomain\Models\Driver;
use Src\Domain\DriverDomain\Enums\DriverStatus;
use Src\Domain\OrderDomain\Models\Order;
use Src\Domain\OrderDomain\Enums\OrderStatus;
use Illuminate\Support\Facades\DB;

// Create 5 drivers
$drivers = [
    ['name' => 'Ahmed Ali', 'lat' => 24.7136, 'lng' => 46.6753],
    ['name' => 'Mohammed Hassan', 'lat' => 21.3891, 'lng' => 39.8579],
    ['name' => 'Khalid Ibrahim', 'lat' => 26.4207, 'lng' => 50.0888],
    ['name' => 'Omar Saleh', 'lat' => 24.5247, 'lng' => 39.5692],
    ['name' => 'Youssef Nasser', 'lat' => 18.2164, 'lng' => 42.5053],
];

foreach ($drivers as $data) {
    $driver = Driver::create([
        'name' => $data['name'],
        'status' => DriverStatus::Available,
        'current_latitude' => $data['lat'],
        'current_longitude' => $data['lng'],
    ]);
    DB::statement("UPDATE drivers SET location = ST_GeomFromText(?, 4326) WHERE id = ?", ["POINT({$data['lng']} {$data['lat']})", $driver->id]);
}

// Create 5 orders
$orders = [
    ['lat' => 24.7200, 'lng' => 46.6800],
    ['lat' => 21.4000, 'lng' => 39.8700],
    ['lat' => 26.4300, 'lng' => 50.1000],
    ['lat' => 24.5300, 'lng' => 39.5800],
    ['lat' => 18.2200, 'lng' => 42.5100],
];

foreach ($orders as $data) {
    $order = Order::create([
        'status' => OrderStatus::Pending,
        'pickup_latitude' => $data['lat'],
        'pickup_longitude' => $data['lng'],
    ]);
    DB::statement("UPDATE orders SET pickup_location = ST_GeomFromText(?, 4326) WHERE id = ?", ["POINT({$data['lng']} {$data['lat']})", $order->id]);
}

echo "✓ Created " . Driver::count() . " drivers\n";
echo "✓ Created " . Order::count() . " orders\n";
exit;
```

## Access the Application

- **Dashboard**: http://localhost:8000/dashboard
- **API**: http://localhost:8000/api/orders

## Test the API

```bash
# List orders
curl http://localhost:8000/api/orders

# Assign order to nearest driver
curl -X POST http://localhost:8000/api/orders/1/assign

# Get driver's orders
curl http://localhost:8000/api/drivers/1/orders
```

## Troubleshooting

### Database Connection Error
```bash
# Check MySQL is running
sudo systemctl status mysql

# Start MySQL
sudo systemctl start mysql
```

### CSRF Token Error
```bash
# Clear cache
php artisan cache:clear
php artisan config:clear
```

### Assets Not Loading
```bash
# Rebuild assets
npm run build

# Or run dev server
npm run dev
```

## Next Steps

1. Read [README.md](README.md) for full documentation
2. Check [API_DOCUMENTATION.md](API_DOCUMENTATION.md) for API details
3. See [DASHBOARD_FEATURES.md](DASHBOARD_FEATURES.md) for frontend features

## Need Help?

Check the logs:
```bash
tail -f storage/logs/laravel.log
```

Happy coding! 🚀
