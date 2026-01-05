# Kesbangpol Kaltara - Government Portal

## Project Overview

This is the official website for Kesbangpol (Kesatuan Bangsa dan Politik / National Unity and Politics Agency) of Kalimantan Utara Province, Indonesia. Built using Laravel 11 with Filament 3 admin panel.

## Tech Stack

- **Framework**: Laravel 11
- **PHP Version**: 8.2+
- **Admin Panel**: Filament 3
- **Database**: MySQL/PostgreSQL
- **Frontend**: Blade Templates, Tailwind CSS, Vite
- **Authentication**: Laravel Sanctum, Filament Breezy

## Key Packages

- `filament/filament` - Admin panel framework
- `bezhansalleh/filament-shield` - Role-based access control
- `bezhansalleh/filament-exceptions` - Exception handling
- `spatie/laravel-medialibrary` - Media management
- `filament/spatie-laravel-settings-plugin` - Settings management
- `filament/spatie-laravel-tags-plugin` - Tagging system
- `jeffgreco13/filament-breezy` - Authentication & profile management
- `barryvdh/laravel-dompdf` - PDF generation
- `z3d0x/filament-logger` - Activity logging

## Project Structure

```
app/
├── Filament/           # Filament admin resources, pages, widgets
├── Http/               # Controllers, Middleware, Requests
├── Models/             # Eloquent models
├── Providers/          # Service providers
├── Services/           # Business logic services
└── Settings/           # Spatie settings classes

config/                 # Configuration files
database/
├── migrations/         # Database migrations
├── seeders/           # Database seeders
└── factories/         # Model factories

resources/
├── views/             # Blade templates
├── css/               # Stylesheets
└── js/                # JavaScript files

routes/
├── web.php            # Web routes
├── api.php            # API routes
└── admin.php          # Admin panel routes (if separate)

public/
├── assets/            # Public assets (vendor libraries, images)
└── storage/           # Symlinked storage

tests/                 # PHPUnit tests
```

## Development Commands

```bash
# Start development server
php artisan serve

# Run migrations
php artisan migrate

# Fresh migration with seeding
php artisan migrate:fresh --seed

# Clear all caches
php artisan optimize:clear

# Cache for production
php artisan optimize

# Generate icons cache (for Filament)
php artisan icons:cache

# Run tests
php artisan test

# Code formatting
./vendor/bin/pint

# Frontend development
npm run dev

# Frontend build
npm run build
```

## Admin Panel Access

- **URL**: `/admin`
- **Default Credentials**:
  - Email: superadmin@starter-kit.com
  - Password: superadmin

## Filament Resources

Resources are located in `app/Filament/Resources/`. Each resource typically includes:
- Resource class (main configuration)
- Pages (List, Create, Edit, View)
- RelationManagers (for related data)

## Settings

Settings are managed via Spatie Laravel Settings. Settings classes are in `app/Settings/` and can be configured through the admin panel.

## Permissions & Roles

Managed by Filament Shield. Permissions are auto-generated based on resources and can be assigned to roles through the admin panel.

## Media Management

Using Spatie Media Library. Models that need media should implement `HasMedia` interface and use `InteractsWithMedia` trait.

## Coding Standards

- Follow PSR-12 coding standards
- Use Laravel Pint for code formatting
- Write descriptive commit messages
- Keep controllers thin, use services for business logic
- Use Form Requests for validation
- Use Resources for API responses
