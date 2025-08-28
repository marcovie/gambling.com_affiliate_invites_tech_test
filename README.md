# Gambling.com Group - Affiliate Locator Tech Test

This is a Laravel application that identifies affiliates within 100km of Gambling.com Group's Dublin office. The system reads affiliate data from a text file and displays matching affiliates sorted by affiliate_id, using precise geographic calculations with the great-circle distance formula.

## Description

This Laravel application features:

-   Clean web interface with simple HTML/CSS styling
-   JSON API endpoint demonstrating service layer reuse
-   Service pattern implementation for business logic separation
-   Error handling with email notifications
-   Caching system for performance optimization
-   Comprehensive Unit and Feature tests
-   Production-ready code structure

## Features

-   **Geographic Distance Calculation**: Uses the great-circle distance formula for accurate distance calculations from Dublin office (53.3340285, -6.2535495)
-   **Web Interface**: Simple, clean HTML interface with basic styling
-   **JSON API**: RESTful API endpoint for programmatic access to affiliate data
-   **Service Pattern**: Clean separation of business logic using Service classes with reuse across web and API controllers
-   **Caching**: File-based caching of affiliate data with configurable TTL
-   **Error Handling**: Exception handling with optional email notifications
-   **Testing**: Comprehensive Feature and Unit tests

## Requirements

-   PHP 8.2 or higher
-   Composer
-   Laravel 12

## Setup Instructions

```bash
# Step 1 - Clone the repository
git clone https://github.com/marcovie/gambling.com_affiliate_invites_tech_test
cd gambling.com_affiliate_invites_tech_test-main

# Step 2 - Install PHP dependencies
composer install -o

# Step 3 - Copy environment file
cp .env.example .env

# Step 4 - Generate application key
php artisan key:generate

# Step 5 - Start the development server
php artisan serve
```

The application will be available at: **http://127.0.0.1:8000**

The JSON API endpoint will be available at: **http://127.0.0.1:8000/api/affiliates**

### Optional Configuration

The application works out of the box, but you can customize these settings in `.env`:

```bash
# Cache TTL for affiliate data (default: 3600 seconds)
AFFILIATE_CACHE_TTL=3600

# Distance limit (default: 100km)
AFFILIATE_DISTANCE_LIMIT_KM=100

# Dublin office coordinates (defaults provided)
DUBLIN_OFFICE_LATITUDE=53.3340285
DUBLIN_OFFICE_LONGITUDE=-6.2535495
```

## Project Structure

This Laravel application follows MVC architecture with the following key components:

### Controllers

-   `app/Http/Controllers/AffiliateController.php` - Main invokable controller handling affiliate display
-   `app/Http/Controllers/Api/AffiliateApiController.php` - API controller providing JSON responses

### Services

-   `app/Services/AffiliateService.php` - Core business logic for affiliate distance calculations, caching, and file processing

### DTOs (Data Transfer Objects)

-   `app/DTOs/AffiliateDTO.php` - Affiliate data structure
-   `app/DTOs/CoordinateDTO.php` - Geographic coordinate data structure

### Helpers

-   `app/Helpers/GeographicHelper.php` - Reusable geographic calculation utilities for distance calculations and filtering

### Resources

-   `app/Http/Resources/AffiliateResource.php` - JSON API resource for formatting affiliate data responses

### Views

-   `resources/views/affiliates/index.blade.php` - Main HTML view with table display

### Data

-   `storage/app/private/affiliates.txt` - Affiliate data file (JSON format, one per line)

### Configuration

-   `config/services.php` - Application configuration including Dublin coordinates and cache settings

## API Endpoints

The application provides a JSON API endpoint that demonstrates service layer reuse between the web interface and API:

### GET /api/affiliates

Returns a JSON response containing all affiliates within 100km of the Dublin office, sorted by affiliate ID.

**Example Response:**

```json
{
    "data": [
        {
            "affiliate_id": 1,
            "name": "John Doe",
            "latitude": 53.35,
            "longitude": -6.25,
            "distance": 25.5
        },
        {
            "affiliate_id": 2,
            "name": "Jane Smith",
            "latitude": 53.4,
            "longitude": -6.3,
            "distance": 45.2
        }
    ]
}
```

**Error Response (404):**

```json
{
    "data": []
}
```

### Service Layer Reuse

The API endpoint demonstrates clean architecture by reusing the same `AffiliateService` that powers the web interface. This approach ensures:

-   **Consistency**: Both web and API interfaces use identical business logic
-   **Maintainability**: Updates to affiliate processing logic automatically apply to both interfaces
-   **Testability**: Service layer can be tested independently of presentation layer
-   **Scalability**: Easy to add to for (mobile app, CLI, etc.) using the same core service

## Testing

Run the test suite using Laravel's built-in testing commands:

```bash
# Run all tests
php artisan test

# Run tests with coverage
php artisan test --coverage

# Run specific test types
php artisan test tests/Feature
php artisan test tests/Unit

# Code style check (optional)
./vendor/bin/pint --test

# Check any larastan issues (optional)
./vendor/bin/phpstan analyse --memory-limit=512M
```

### Test Coverage

The project includes:

-   **Feature Tests**: End-to-end testing of both web and API controllers
-   **Unit Tests**: Testing of individual components (DTOs, Services, Helpers)

## Business Logic

### Distance Calculation

The application uses the **great-circle distance formula** from [Wikipedia](https://en.wikipedia.org/wiki/Great-circle_distance) to calculate distances between coordinates.

**Dublin Office Coordinates**: 53.3340285, -6.2535495

### GeographicHelper Utility

A reusable helper class (`app/Helpers/GeographicHelper.php`) provides geographic calculation functionality that can be used throughout the system:

-   **`calculateDistance()`** - Calculates great-circle distance between two coordinates
-   **`filterByDistance()`** - Filters collections of objects by distance from a reference point and sorts results

This helper was created for potential reuse in other parts of the system that may require similar geographic calculations.

### Affiliate Processing

1. Reads affiliate data from `storage/app/private/affiliates.txt`
2. Calculates distance from Dublin office for each affiliate
3. Filters affiliates within 100km radius
4. Returns results sorted by Affiliate ID (ascending)

### Input Format

Each line in `affiliates.txt` contains a JSON object:

```json
{
    "latitude": "53.123456",
    "affiliate_id": 12,
    "name": "John Doe",
    "longitude": "-6.654321"
}
```

## Application Features

-   **File-based Data**: No database required - reads from text file
-   **Dual Interface**: Both web UI and JSON API access the same business logic
-   **Caching**: Configurable caching to avoid repeated file reads
-   **Error Handling**: Graceful error handling with optional email notifications
-   **Clean Architecture**: Separation of concerns using Services and DTOs
-   **Configuration-driven**: All settings configurable via environment variables

## Production Deployment

For production deployment:

```bash
# Optimize for production
php artisan optimize
composer install --optimize-autoloader --no-dev

# Set proper permissions
chmod -R 755 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache

# Update environment
APP_ENV=production
APP_DEBUG=false
```

## Troubleshooting

### Common Issues

1. **Affiliate data file not found**

    - Ensure `storage/app/private/affiliates.txt` exists
    - Check file permissions: `chmod 644 storage/app/private/affiliates.txt`

2. **Permission errors**

    - Fix storage permissions: `chmod -R 755 storage bootstrap/cache`

3. **Cache issues**
    - Clear Laravel cache: `php artisan cache:clear`

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
