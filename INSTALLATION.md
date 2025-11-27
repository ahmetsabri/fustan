# Tailoring Order Management System - Installation Guide

## Prerequisites

- PHP 8.2 or higher
- Composer
- Node.js and NPM
- MySQL/PostgreSQL/SQLite database

## Installation Steps

### 1. Install Dependencies

The Spatie Media Library and Filament plugin are already installed. If you need to reinstall:

```bash
composer require spatie/laravel-medialibrary
composer require filament/spatie-laravel-media-library-plugin
```

### 2. Publish Spatie Media Library Migration (if not already published)

```bash
php artisan vendor:publish --provider="Spatie\MediaLibrary\MediaLibraryServiceProvider" --tag="medialibrary-migrations"
```

### 3. Run Migrations

```bash
php artisan migrate
```

### 4. Seed the Database

```bash
php artisan db:seed
```

This will create:
- 1 Admin user (admin@example.com / password)
- 2 Customer Service users (cs1@example.com, cs2@example.com / password)
- 3 Tailor users (tailor1@example.com, tailor2@example.com, tailor3@example.com / password)
- 5 Sample products
- 10 Sample customers
- 10 Sample orders

### 5. Create Storage Link

```bash
php artisan storage:link
```

This is required for Spatie Media Library to serve uploaded files.

### 6. Configure Filesystem

Make sure your `.env` file has the correct filesystem configuration:

```
FILESYSTEM_DISK=public
```

### 7. Access the Admin Panel

Navigate to: `http://your-domain/admin`

Login with:
- Email: `admin@example.com`
- Password: `password`

## User Roles

### Admin
- Full access to all resources
- Can manage users, products, customers, and orders

### Customer Service
- Can create/view/edit orders
- Can create/view/edit customers
- Can view products
- Can view tailors list
- Cannot manage users or products

### Tailor
- Can only view orders assigned to them
- Can update order status
- Can view customer info and measurements for their orders
- Cannot create or delete orders

## Features

### Order Management
- Auto-generated order numbers (format: ORD-YYYYMMDD-XXX)
- Customer selection with option to create new customer
- Product selection (optional)
- Measurements section with Arabic labels:
  - الطول (Length)
  - الكتف (Shoulder)
  - الصدر (Chest)
  - الخصر (Waist)
  - الورك (Hip)
  - الكم (Sleeve)
  - ملاحظات_المقاسات (Measurement Notes)
- Tailor assignment
- Status tracking with color coding
- Order attachments (multiple images)

### Product Management
- Multiple image uploads using Spatie Media Library
- Primary image selection
- Active/inactive toggle

### Customer Management
- Customer information management
- Order history via relation manager

### Dashboard
- Statistics widgets showing:
  - Total orders (today, this week, this month)
  - Pending orders
  - In progress orders
  - Completed orders today
- Role-based filtering

## Arabic/RTL Support

The system is configured for Arabic language and RTL layout. The Filament panel is set to:
- Locale: `ar`
- Direction: `rtl`

## Spatie Media Library Collections

### Products
- Collection: `images` (multiple images)

### Orders
- Collection: `attachments` (multiple images)

## Order Status Colors

- `pending`: Gray
- `in_progress`: Blue
- `completed`: Green
- `delivered`: Success (green)
- `cancelled`: Danger (red)

## Validation Rules

- Email must be unique for users
- Tailor can only be assigned if role is 'tailor'
- All required fields are validated in forms

## Troubleshooting

### Images not displaying
1. Run `php artisan storage:link`
2. Check file permissions on `storage/app/public`
3. Verify `FILESYSTEM_DISK=public` in `.env`

### Permission errors
1. Check that policies are registered in `AuthServiceProvider`
2. Verify user roles are set correctly

### Migration errors
1. Make sure all migrations are run: `php artisan migrate:fresh`
2. Check database connection in `.env`

## Additional Notes

- All passwords in seeders are: `password`
- Order numbers are auto-generated and unique
- The system uses Filament v4 with the new schema-based form structure
- Spatie Media Library is configured for both products and orders

