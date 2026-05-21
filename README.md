# Real-Time Logistics Platform - Technical Assessment

A production-ready logistics platform built with **Laravel 13**, **Vue 3**, and **MySQL**, implementing strict **Domain-Driven Design (DDD)** principles for real-time order-driver assignment with geospatial optimization and concurrency control.

## 📋 Table of Contents

- [Overview](#overview)
- [Key Features](#key-features)
- [Technology Stack](#technology-stack)
- [Architecture](#architecture)
- [Setup Instructions](#setup-instructions)
- [API Documentation](#api-documentation)
- [Architectural Decisions](#architectural-decisions)
- [Database Design](#database-design)
- [Concurrency Handling](#concurrency-handling)
- [Testing](#testing)
- [Project Structure](#project-structure)

---

## 🎯 Overview

This platform demonstrates a real-world logistics system where orders are automatically assigned to the nearest available drivers using geospatial calculations. The system handles high-concurrency scenarios with pessimistic locking and provides a clean Vue 3 dashboard for real-time order management.

### Business Requirements

1. **Order Assignment**: Automatically assign orders to the nearest available driver
2. **Geospatial Optimization**: Use MySQL spatial functions for distance calculations
3. **Concurrency Control**: Prevent race conditions in high-traffic scenarios
4. **Real-time Dashboard**: Vue 3 interface for order management
5. **Clean Architecture**: Strict DDD with clear domain boundaries

---

## ✨ Key Features

### Backend (Laravel 13 + DDD)

- ✅ **Strict Domain-Driven Design** with bounded contexts
- ✅ **Geospatial Queries** using MySQL SPATIAL indexes
- ✅ **Pessimistic Locking** for race condition prevention
- ✅ **Contract-Service Pattern** for dependency injection
- ✅ **RESTful API** with resource transformers
- ✅ **Comprehensive Exception Handling** with domain-specific exceptions

### Frontend (Vue 3 + Tailwind CSS)

- ✅ **Composition API** with `<script setup>`
- ✅ **Real-time Order Management** dashboard
- ✅ **RTL Support** for Arabic language
- ✅ **Responsive Design** for all devices
- ✅ **Loading States** and error handling
- ✅ **Clean UI/UX** with Tailwind CSS

---

## 🛠 Technology Stack

### Backend
- **Framework**: Laravel 13.x
- **PHP**: 8.3+
- **Database**: MySQL 8.0+ (with spatial support)
- **Architecture**: Domain-Driven Design (DDD)

### Frontend
- **Framework**: Vue 3 (Composition API)
- **Build Tool**: Vite
- **Styling**: Tailwind CSS
- **HTTP Client**: Axios

### DevOps
- **Dependency Management**: Composer, NPM
- **Version Control**: Git

---

## 🏗 Architecture

### Domain-Driven Design Structure

```
src/
├── Domain/                          # Core Business Logic
│   ├── DriverDomain/               # Driver Bounded Context
│   │   ├── Contracts/              # Interfaces
│   │   │   └── DriverLocationContract.php
│   │   ├── Enums/                  # Value Objects
│   │   │   └── DriverStatus.php
│   │   ├── Models/                 # Domain Entities
│   │   │   └── Driver.php
│   │   └── Services/               # Domain Services
│   │       └── DriverLocationService.php
│   │
│   ├── OrderDomain/                # Order Bounded Context
│   │   ├── Contracts/
│   │   │   └── OrderAssignmentContract.php
│   │   ├── Enums/
│   │   │   └── OrderStatus.php
│   │   ├── Exceptions/             # Domain Exceptions
│   │   │   ├── NoAvailableDriverException.php
│   │   │   ├── OrderAlreadyAssignedException.php
│   │   │   └── OrderNotFoundException.php
│   │   ├── Models/
│   │   │   ├── Order.php
│   │   │   └── Scopes/
│   │   │       └── OrderScopes.php
│   │   └── Services/
│   │       └── OrderAssignmentService.php
│   │
│   └── Providers/                  # Service Bindings
│       └── DomainServiceProvider.php
│
└── Presentation/                    # HTTP Layer
    └── Cpanel/
        ├── Controllers/            # HTTP Controllers
        │   ├── OrderAssignmentController.php
        │   ├── OrdersController.php
        │   └── DriverOrdersController.php
        ├── Resources/              # API Resources
        │   ├── OrderResource.php
        │   └── DriverResource.php
        └── Routes/                 # API Routes
            └── api.php
```

### Architectural Layers

```
┌─────────────────────────────────────────┐
│         Presentation Layer              │
│  (Controllers, Resources, Routes)       │
└─────────────────┬───────────────────────┘
                  │
                  ▼
┌─────────────────────────────────────────┐
│         Application Layer               │
│    (Service Providers, Bindings)        │
└─────────────────┬───────────────────────┘
                  │
                  ▼
┌─────────────────────────────────────────┐
│          Domain Layer                   │
│  (Entities, Services, Contracts)        │
└─────────────────┬───────────────────────┘
                  │
                  ▼
┌─────────────────────────────────────────┐
│       Infrastructure Layer              │
│    (Database, External Services)        │
└─────────────────────────────────────────┘
```

---

## 🚀 Setup Instructions

### Prerequisites

- PHP 8.3 or higher
- Composer 2.x
- Node.js 18+ and NPM
- MySQL 8.0+ (with spatial support)
- Git

### 1. Clone the Repository

```bash
git clone <repository-url>
cd Winch_assessment
```

### 2. Install PHP Dependencies

```bash
composer install
```

### 3. Environment Configuration

```bash
# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate
```

### 4. Configure Database

Edit `.env` file with your MySQL credentials:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=logistics_platform
DB_USERNAME=root
DB_PASSWORD=your_password
```

### 5. Create Database

```bash
# Using MySQL CLI
mysql -u root -p -e "CREATE DATABASE logistics_platform CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# Or using Laravel
php artisan db:create
```

### 6. Run Migrations

```bash
php artisan migrate
```

This will create the following tables:
- `drivers` - Driver information with spatial location
- `orders` - Order information with pickup location
- `users`, `cache`, `jobs` - Laravel default tables

### 7. Seed Test Data (Optional)

```bash
php artisan tinker
```

```php
use Src\Domain\DriverDomain\Models\Driver;
use Src\Domain\DriverDomain\Enums\DriverStatus;
use Src\Domain\OrderDomain\Models\Order;
use Src\Domain\OrderDomain\Enums\OrderStatus;
use Illuminate\Support\Facades\DB;

// Create drivers with spatial data
$drivers = [
    ['name' => 'Ahmed Ali', 'lat' => 24.7136, 'lng' => 46.6753],      // Riyadh
    ['name' => 'Mohammed Hassan', 'lat' => 21.3891, 'lng' => 39.8579], // Jeddah
    ['name' => 'Khalid Ibrahim', 'lat' => 26.4207, 'lng' => 50.0888],  // Dammam
    ['name' => 'Omar Saleh', 'lat' => 24.5247, 'lng' => 39.5692],      // Medina
    ['name' => 'Youssef Nasser', 'lat' => 18.2164, 'lng' => 42.5053],  // Abha
];

foreach ($drivers as $data) {
    $driver = Driver::create([
        'name' => $data['name'],
        'status' => DriverStatus::Available,
        'current_latitude' => $data['lat'],
        'current_longitude' => $data['lng'],
    ]);
    
    // Update spatial column
    DB::statement(
        "UPDATE drivers SET location = ST_GeomFromText(?, 4326) WHERE id = ?",
        ["POINT({$data['lng']} {$data['lat']})", $driver->id]
    );
}

// Create orders
$orders = [
    ['lat' => 24.7200, 'lng' => 46.6800],  // Near Riyadh
    ['lat' => 21.4000, 'lng' => 39.8700],  // Near Jeddah
    ['lat' => 26.4300, 'lng' => 50.1000],  // Near Dammam
    ['lat' => 24.5300, 'lng' => 39.5800],  // Near Medina
    ['lat' => 18.2200, 'lng' => 42.5100],  // Near Abha
];

foreach ($orders as $data) {
    $order = Order::create([
        'status' => OrderStatus::Pending,
        'pickup_latitude' => $data['lat'],
        'pickup_longitude' => $data['lng'],
    ]);
    
    // Update spatial column
    DB::statement(
        "UPDATE orders SET pickup_location = ST_GeomFromText(?, 4326) WHERE id = ?",
        ["POINT({$data['lng']} {$data['lat']})", $order->id]
    );
}

echo "✓ Created " . Driver::count() . " drivers\n";
echo "✓ Created " . Order::count() . " orders\n";
```

### 8. Install Frontend Dependencies

```bash
npm install
```

### 9. Build Frontend Assets

For development (with hot reload):
```bash
npm run dev
```

For production:
```bash
npm run build
```

### 10. Start the Application

```bash
# Start Laravel development server
php artisan serve
```

### 11. Access the Application

- **Dashboard**: http://localhost:8000/dashboard
- **API Base URL**: http://localhost:8000/api

---

## 📚 API Documentation

### Base URL
```
http://localhost:8000/api
```

### Endpoints

#### 1. List All Orders
```http
GET /api/orders
```

**Query Parameters:**
- `status` (optional): Filter by status (`pending`, `assigned`, `completed`)
- `per_page` (optional): Results per page (default: 15, max: 100)
- `page` (optional): Page number

**Example:**
```bash
curl -X GET "http://localhost:8000/api/orders?status=pending&per_page=20"
```

#### 2. Assign Order to Best Driver
```http
POST /api/orders/{id}/assign
```

**Response Codes:**
- `200` - Success
- `404` - Order not found
- `409` - Order already assigned
- `503` - No available driver

**Example:**
```bash
curl -X POST http://localhost:8000/api/orders/1/assign
```

#### 3. Unassign Order
```http
POST /api/orders/{id}/unassign
```

#### 4. Get Driver Orders
```http
GET /api/drivers/{id}/orders
```

**Query Parameters:**
- `status` (optional): Filter by status
- `per_page` (optional): Results per page
- `sort_by` (optional): Sort field (default: `created_at`)
- `sort_order` (optional): `asc` or `desc`

#### 5. Get Driver Statistics
```http
GET /api/drivers/{id}/orders/statistics
```

For complete API documentation, see [API_DOCUMENTATION.md](API_DOCUMENTATION.md).

---

## 🎯 Architectural Decisions

### 1. Why Domain-Driven Design (DDD)?

#### Problem Statement
Traditional Laravel applications often suffer from:
- **Fat Controllers**: Business logic mixed with HTTP concerns
- **Anemic Models**: Models become mere data containers
- **Tight Coupling**: Difficult to test and maintain
- **Unclear Boundaries**: Domain logic scattered across the codebase

#### Our Solution: Strict DDD

We implemented a **strict DDD architecture** with the following principles:

#### A. Bounded Contexts

We identified two distinct bounded contexts:

**DriverDomain**
- Manages driver entities and their availability
- Handles geospatial location services
- Encapsulates driver-specific business rules

**OrderDomain**
- Manages order lifecycle
- Handles order assignment logic
- Encapsulates order-specific business rules

**Benefits:**
- Clear separation of concerns
- Independent evolution of domains
- Easier to understand and maintain
- Team can work on different domains simultaneously

#### B. Contract-Service Pattern

Every domain service is defined by a **contract (interface)**:

```php
// Contract defines the behavior
interface OrderAssignmentContract
{
    public function assignToBestDriver(int $orderId): Order;
}

// Service implements the contract
class OrderAssignmentService implements OrderAssignmentContract
{
    public function assignToBestDriver(int $orderId): Order
    {
        // Implementation
    }
}
```

**Benefits:**
- **Dependency Inversion**: Controllers depend on abstractions, not concrete implementations
- **Testability**: Easy to mock services in tests
- **Flexibility**: Can swap implementations without changing consumers
- **Clear API**: Contract documents expected behavior

#### C. Layered Architecture

**Presentation Layer** (`src/Presentation/`)
- HTTP controllers (thin, no business logic)
- API resources for response formatting
- Route definitions

**Domain Layer** (`src/Domain/`)
- Business entities (Models)
- Domain services (business logic)
- Value objects (Enums)
- Domain exceptions
- Contracts (interfaces)

**Benefits:**
- **Single Responsibility**: Each layer has one job
- **Testability**: Can test domain logic without HTTP
- **Maintainability**: Changes in one layer don't affect others

#### D. Dependency Injection

All dependencies are injected via constructor:

```php
class OrderAssignmentController extends Controller
{
    public function __construct(
        private readonly OrderAssignmentContract $orderAssignment
    ) {}
}
```

**Benefits:**
- **Loose Coupling**: Easy to change implementations
- **Testability**: Can inject mocks for testing
- **Explicit Dependencies**: Clear what each class needs

### 2. Why This Approach for Logistics?

Logistics systems have:
- **Complex Business Rules**: Driver availability, location matching, assignment logic
- **Multiple Domains**: Drivers, Orders, Routes, Customers
- **High Concurrency**: Multiple staff assigning orders simultaneously
- **Scalability Needs**: Must handle growth in orders and drivers

DDD provides:
- **Clear Domain Boundaries**: Each domain can scale independently
- **Business Logic Isolation**: Easy to modify rules without breaking other parts
- **Testability**: Can test critical assignment logic in isolation
- **Team Collaboration**: Different teams can own different domains

---

## 💾 Database Design

### Why MySQL for This Project?

#### Requirements Analysis

Our logistics platform requires:
1. **Geospatial Queries**: Find nearest driver to order location
2. **ACID Transactions**: Ensure data consistency during assignments
3. **Pessimistic Locking**: Prevent race conditions
4. **High Read/Write Performance**: Handle concurrent order assignments
5. **Spatial Indexing**: Optimize distance calculations

#### MySQL vs PostgreSQL Comparison

| Feature | MySQL 8.0+ | PostgreSQL | Our Choice |
|---------|-----------|------------|------------|
| Spatial Support | ✅ Native SPATIAL indexes | ✅ PostGIS extension | MySQL |
| ACID Compliance | ✅ InnoDB engine | ✅ Native | Both |
| Pessimistic Locking | ✅ `FOR UPDATE` | ✅ `FOR UPDATE` | Both |
| Performance | ✅ Excellent for reads | ✅ Excellent for complex queries | MySQL |
| Ease of Setup | ✅ Simple | ⚠️ Requires PostGIS | MySQL |
| Laravel Support | ✅ First-class | ✅ First-class | Both |

#### Decision: MySQL 8.0+

**Primary Reasons:**

1. **Native Spatial Support**
   - Built-in SPATIAL indexes (no extensions needed)
   - `ST_Distance_Sphere()` for accurate distance calculations
   - `POINT` data type with SRID 4326 (WGS 84)

2. **Performance for Our Use Case**
   - Optimized for high-concurrency read/write operations
   - Excellent performance with spatial indexes
   - Lower latency for simple geospatial queries

3. **Operational Simplicity**
   - No additional extensions to install
   - Easier deployment and maintenance
   - Better documentation for spatial features

4. **Industry Standard**
   - Widely used in logistics/delivery platforms
   - Large community support
   - Proven at scale (Uber, DoorDash use MySQL)

### Database Schema

#### Drivers Table

```sql
CREATE TABLE drivers (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    status ENUM('available', 'busy') DEFAULT 'available',
    current_latitude DECIMAL(10,8) NOT NULL,
    current_longitude DECIMAL(11,8) NOT NULL,
    location POINT NOT NULL SRID 4326,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    
    INDEX idx_status (status),
    INDEX idx_driver_location_status (status, current_latitude, current_longitude),
    SPATIAL INDEX idx_driver_spatial_location (location)
);
```

#### Orders Table

```sql
CREATE TABLE orders (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    status ENUM('pending', 'assigned', 'completed') DEFAULT 'pending',
    pickup_latitude DECIMAL(10,8) NOT NULL,
    pickup_longitude DECIMAL(11,8) NOT NULL,
    pickup_location POINT NOT NULL SRID 4326,
    driver_id BIGINT UNSIGNED NULL,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    
    INDEX idx_status (status),
    INDEX idx_driver_id (driver_id),
    INDEX idx_order_status_driver (status, driver_id),
    INDEX idx_order_location_status (status, pickup_latitude, pickup_longitude),
    SPATIAL INDEX idx_order_spatial_location (pickup_location),
    INDEX idx_created_at (created_at),
    
    FOREIGN KEY (driver_id) REFERENCES drivers(id) ON DELETE SET NULL
);
```

### Indexing Strategy

#### 1. Status Indexes
```sql
INDEX idx_status (status)
```
**Purpose**: Fast filtering of pending/assigned/completed orders
**Query**: `WHERE status = 'pending'`

#### 2. Composite Indexes
```sql
INDEX idx_driver_location_status (status, current_latitude, current_longitude)
```
**Purpose**: Optimize queries that filter by status and location
**Query**: `WHERE status = 'available' AND current_latitude BETWEEN ... AND current_longitude BETWEEN ...`

#### 3. Spatial Indexes
```sql
SPATIAL INDEX idx_driver_spatial_location (location)
```
**Purpose**: Ultra-fast geospatial queries
**Query**: `ST_Distance_Sphere(location, ST_GeomFromText('POINT(...)', 4326))`

**Performance Impact:**
- Without spatial index: O(n) - scans all rows
- With spatial index: O(log n) - uses R-tree structure

#### 4. Foreign Key Indexes
```sql
INDEX idx_driver_id (driver_id)
```
**Purpose**: Fast joins and lookups
**Query**: `WHERE driver_id = 5`

### Geospatial Query Example

```php
// Find nearest available driver within 10km
$driver = Driver::query()
    ->select([
        'drivers.*',
        DB::raw("ST_Distance_Sphere(
            location,
            ST_GeomFromText(?, 4326)
        ) as distance")
    ])
    ->setBindings(["POINT($longitude $latitude)"])
    ->where('status', DriverStatus::Available)
    ->havingRaw('distance <= ?', [10000]) // 10km in meters
    ->orderBy('distance', 'asc')
    ->first();
```

**Query Execution:**
1. Uses `idx_status` to filter available drivers
2. Uses `idx_driver_spatial_location` for distance calculation
3. Returns nearest driver in milliseconds

---

## 🔒 Concurrency Handling

### The Problem: Race Conditions

In a high-traffic logistics platform, multiple staff members might try to assign the same order or driver simultaneously:

#### Scenario 1: Double Assignment
```
Time    Staff A                     Staff B
----    -------                     -------
T1      Read Order #1 (pending)     
T2                                  Read Order #1 (pending)
T3      Assign to Driver #5         
T4                                  Assign to Driver #7
T5      ❌ Order assigned to TWO drivers!
```

#### Scenario 2: Driver Overload
```
Time    Staff A                     Staff B
----    -------                     -------
T1      Read Driver #5 (available)  
T2                                  Read Driver #5 (available)
T3      Assign Order #1             
T4                                  Assign Order #2
T5      ❌ Driver has TWO orders but status still "available"!
```

### Our Solution: Pessimistic Locking

We use **pessimistic locking** with `lockForUpdate()` to prevent race conditions:

```php
public function assignToBestDriver(int $orderId): Order
{
    return DB::transaction(function () use ($orderId) {
        // Step 1: Lock the order row
        $order = Order::lockForUpdate()->find($orderId);
        
        if (!$order || !$order->isPending()) {
            throw new OrderAlreadyAssignedException($orderId);
        }
        
        // Step 2: Find nearest driver
        $driver = $this->driverLocationService->findNearestAvailableDriver(
            latitude: (float) $order->pickup_latitude,
            longitude: (float) $order->pickup_longitude
        );
        
        if (!$driver) {
            throw new NoAvailableDriverException($orderId);
        }
        
        // Step 3: Lock the driver row
        $driver = Driver::lockForUpdate()->find($driver->id);
        
        // Step 4: Double-check driver is still available
        if (!$driver || !$driver->isAvailable()) {
            throw new NoAvailableDriverException($orderId);
        }
        
        // Step 5: Perform atomic assignment
        $order->status = OrderStatus::Assigned;
        $order->driver_id = $driver->id;
        $order->save();
        
        $driver->status = DriverStatus::Busy;
        $driver->save();
        
        return $order->load('driver');
    });
}
```

### How It Works

#### 1. Database Transaction
```php
DB::transaction(function () {
    // All operations are atomic
});
```
**Guarantees:**
- All operations succeed or all fail
- No partial updates
- ACID compliance

#### 2. Pessimistic Locking
```php
$order = Order::lockForUpdate()->find($orderId);
```

**SQL Generated:**
```sql
SELECT * FROM orders WHERE id = ? FOR UPDATE
```

**What Happens:**
- Row is **locked** until transaction completes
- Other transactions **wait** if they try to lock the same row
- Prevents concurrent modifications

#### 3. Lock Sequence

```
Transaction A                   Transaction B
-------------                   -------------
BEGIN                          BEGIN
SELECT ... FOR UPDATE (Order)  
                               SELECT ... FOR UPDATE (Order) ⏳ WAITING
SELECT ... FOR UPDATE (Driver) 
UPDATE orders                  
UPDATE drivers                 
COMMIT                         
                               ⏳ Lock released, now can proceed
                               SELECT ... FOR UPDATE (Order)
                               ❌ Order already assigned!
                               ROLLBACK
```

#### 4. Double-Check Pattern

After acquiring the driver lock, we verify availability again:

```php
// Lock acquired
$driver = Driver::lockForUpdate()->find($driver->id);

// Double-check (another transaction might have assigned this driver)
if (!$driver || !$driver->isAvailable()) {
    throw new NoAvailableDriverException($orderId);
}
```

### Performance Considerations

#### Lock Wait Timeout

MySQL default: 50 seconds

```sql
SET innodb_lock_wait_timeout = 50;
```

**Our Strategy:**
- Keep transactions **short** (< 100ms)
- Lock only what's needed
- Release locks quickly

#### Deadlock Prevention

**Potential Deadlock:**
```
Transaction A: Lock Order #1 → Lock Driver #5
Transaction B: Lock Driver #5 → Lock Order #1
❌ Deadlock!
```

**Our Prevention:**
- Always lock in same order: Order first, then Driver
- Keep lock duration minimal
- MySQL automatically detects and resolves deadlocks

#### Monitoring

```php
Log::info("Order assigned successfully", [
    'order_id' => $orderId,
    'driver_id' => $driver->id,
    'lock_wait_time' => $lockWaitTime,
]);
```

### Alternative Approaches Considered

#### 1. Optimistic Locking
```php
// Check version before update
UPDATE orders SET status = 'assigned', version = version + 1
WHERE id = ? AND version = ?
```

**Why Not Used:**
- Higher retry rate in high-concurrency
- More complex error handling
- Not suitable for critical operations

#### 2. Queue-Based Assignment
```php
// Push to queue
AssignOrderJob::dispatch($orderId);
```

**Why Not Used:**
- Adds latency (not real-time)
- More complex infrastructure
- Overkill for this use case

**When to Use:**
- Batch processing
- Non-critical operations
- Very high concurrency (1000+ req/s)

### Testing Concurrency

```bash
# Simulate concurrent requests
ab -n 100 -c 10 -p order.json -T application/json \
   http://localhost:8000/api/orders/1/assign
```

**Expected Results:**
- 1 success (200)
- 99 failures (409 - Already Assigned)
- No data corruption
- No double assignments

---

## 🧪 Testing

### Manual Testing

#### 1. Test Order Assignment

```bash
# Create test data (see Setup Instructions)

# Assign order
curl -X POST http://localhost:8000/api/orders/1/assign

# Expected: Success with driver details
```

#### 2. Test Concurrency

```bash
# Terminal 1
curl -X POST http://localhost:8000/api/orders/1/assign

# Terminal 2 (immediately)
curl -X POST http://localhost:8000/api/orders/1/assign

# Expected: One success, one "already assigned" error
```

#### 3. Test No Available Drivers

```bash
# Mark all drivers as busy
php artisan tinker
>>> Driver::query()->update(['status' => 'busy']);

# Try to assign
curl -X POST http://localhost:8000/api/orders/1/assign

# Expected: 503 - No available driver
```

### Automated Testing

```bash
# Run PHPUnit tests
php artisan test

# Run with coverage
php artisan test --coverage
```

---

## 📁 Project Structure

```
Winch_assessment/
├── app/                            # Laravel default app directory
├── bootstrap/                      # Laravel bootstrap files
│   ├── app.php                    # Application bootstrap
│   └── providers.php              # Service provider registration
├── config/                         # Configuration files
├── database/
│   └── migrations/                # Database migrations
│       ├── 2024_01_01_000001_create_drivers_table.php
│       └── 2024_01_01_000002_create_orders_table.php
├── public/                         # Public assets
├── resources/
│   ├── css/
│   │   └── app.css                # Tailwind CSS
│   ├── js/
│   │   ├── app.js                 # Vue app entry
│   │   ├── bootstrap.js           # Axios config
│   │   └── components/
│   │       └── OrdersDashboard.vue # Main dashboard
│   └── views/
│       └── dashboard.blade.php    # Dashboard view
├── routes/
│   └── web.php                    # Web routes
├── src/                            # DDD Source Code
│   ├── Domain/                    # Domain Layer
│   │   ├── DriverDomain/         # Driver Bounded Context
│   │   │   ├── Contracts/
│   │   │   ├── Enums/
│   │   │   ├── Models/
│   │   │   └── Services/
│   │   ├── OrderDomain/          # Order Bounded Context
│   │   │   ├── Contracts/
│   │   │   ├── Enums/
│   │   │   ├── Exceptions/
│   │   │   ├── Models/
│   │   │   └── Services/
│   │   └── Providers/
│   │       └── DomainServiceProvider.php
│   └── Presentation/              # Presentation Layer
│       └── Cpanel/
│           ├── Controllers/
│           ├── Resources/
│           └── Routes/
│               └── api.php
├── storage/                        # Storage directory
├── tests/                          # Test files
├── vendor/                         # Composer dependencies
├── node_modules/                   # NPM dependencies
├── .env                           # Environment configuration
├── .env.example                   # Environment template
├── composer.json                  # PHP dependencies
├── package.json                   # Node dependencies
├── vite.config.js                 # Vite configuration
├── tailwind.config.js             # Tailwind configuration
├── API_DOCUMENTATION.md           # API documentation
├── FRONTEND_SETUP.md              # Frontend setup guide
├── DASHBOARD_FEATURES.md          # Dashboard features
└── README.md                      # This file
```

---

## 🎓 Key Learnings

### 1. Domain-Driven Design
- Clear separation of concerns
- Bounded contexts for complex domains
- Contract-Service pattern for flexibility

### 2. Geospatial Optimization
- MySQL SPATIAL indexes for performance
- ST_Distance_Sphere for accurate calculations
- POINT data type with SRID 4326

### 3. Concurrency Control
- Pessimistic locking for critical operations
- Database transactions for atomicity
- Double-check pattern for safety

### 4. Clean Architecture
- Thin controllers, fat services
- Dependency injection for testability
- Resource transformers for consistent APIs

### 5. Modern Frontend
- Vue 3 Composition API
- Tailwind CSS for rapid development
- Proper error handling and loading states

---

## 📝 License

This project is a technical assessment and is not licensed for commercial use.

---

## 👤 Author

**Technical Assessment Submission**

For questions or clarifications, please contact the assessment coordinator.

---

## 🙏 Acknowledgments

- Laravel Framework
- Vue.js Team
- Tailwind CSS
- MySQL Spatial Extensions
- Domain-Driven Design Community

---

**Built with ❤️ using Laravel, Vue 3, and MySQL**
