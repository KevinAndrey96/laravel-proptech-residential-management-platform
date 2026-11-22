# PropTech Residential Management Platform

[![CI](https://github.com/KevinAndrey96/proptech-residential-management-platform/actions/workflows/ci.yml/badge.svg)](https://github.com/KevinAndrey96/proptech-residential-management-platform/actions/workflows/ci.yml)
[![PHP Version](https://img.shields.io/badge/PHP-8.1%20%7C%208.2-777BB4?logo=php&logoColor=white)](https://www.php.net/)
[![Laravel](https://img.shields.io/badge/Laravel-8.83-FF2D20?logo=laravel&logoColor=white)](https://laravel.com/)
[![MySQL](https://img.shields.io/badge/MySQL-8.0-4479A1?logo=mysql&logoColor=white)](https://www.mysql.com/)
[![Docker](https://img.shields.io/badge/Docker-Compose-2496ED?logo=docker&logoColor=white)](https://www.docker.com/)
[![License: MIT](https://img.shields.io/badge/License-MIT-green.svg)](LICENSE)

An enterprise condominium administration, clubhouse social zone booking, parking allocation, and payments management platform. Successfully deployed and operated in production across multi-tower residential complexes (including Reserva de Toscana), managing hundreds of apartments, inhabitants, social spaces, and financial dues.

---

## Key Features

- **Clubhouse & Social Zones Booking Engine**:
  - Configurable capacity limits, time slot intervals (`strip` in minutes), and day-of-week operating schedules.
  - Conflict prevention for concurrent reservations and per-resident daily limits.
  - Automated cron command (`php artisan booking:cancel`) to expire abandoned reservations and update past schedules.
  - Email notification pipeline via Mailable templates upon reservation confirmation.

- **Parking Allocation & Security Guard Operations**:
  - Segmented parking zones (Resident vs Visitor).
  - Real-time space occupancy toggle and maintenance lockouts.
  - Detailed security guard entry/exit registry (`DetailSpaceOccupation`) capturing license plate, visitor name, destination apartment, and observation logs.

- **Financial Dues & Payments Management**:
  - Resident monthly condominium dues tracking and verification.
  - Multi-year and month filtering, payment receipt download, and duplicate payment prevention.
  - Resident self-service portal to verify payment status and billing history.

- **Resident, Vehicle & Pet Registry**:
  - Apartment and tower inventory mapping with inhabitant counts.
  - Co-habitant records, authorized vehicles, and pet registry.
  - One-click status toggling (Habilitado / Inhabilitado) to govern amenity access.

- **Role-Based Access Control (RBAC)**:
  - Granular permissions powered by Spatie Laravel-Permission.
  - Built-in roles: `Superadmin`, `Administrator`, `Receptionist`, `Resident`, and `Watchman` (Guardia de Seguridad).

---

## Clean Architecture & System Design

The application follows Clean Architecture principles to isolate domain business rules from HTTP controllers and persistence layers:

```
app/
├── Console/
│   └── Commands/
│       └── CancelBookings.php           # Automated booking expiration worker
├── DTOs/
│   └── WatchmanDTO.php                  # Data Transfer Objects
├── Http/
│   ├── Controllers/                     # Thin controllers orchestrating requests
│   │   ├── Bookings/
│   │   ├── Residents/
│   │   ├── Parkings/
│   │   ├── ParkingSpaces/
│   │   ├── Payments/
│   │   └── Services/
│   └── Middleware/
├── Models/                              # Eloquent entities & relationships
│   ├── Booking.php
│   ├── DetailSpaceOccupation.php
│   ├── Parking.php
│   ├── ParkingSpace.php
│   ├── Payment.php
│   ├── Resident.php
│   └── Service.php
├── Providers/
│   ├── RepositoriesServicesProvider.php # IoC bindings for repository contracts
│   └── UseCasesServicesProvider.php     # IoC bindings for use case contracts
├── Repositories/                        # Data access layer
│   ├── Contracts/                       # Abstract repository interfaces
│   ├── DetailSpaceOccupations/
│   ├── ParkingSpaces/
│   ├── Parkings/
│   ├── Payments/
│   └── Residents/
└── UseCases/                            # Encapsulated application business rules
    ├── Contracts/                       # UseCase interfaces
    ├── Bookings/                        # Reservation validation & processing
    ├── Residents/                       # Resident onboarding & status transitions
    ├── Roles/                           # RBAC assignment logic
    └── Services/                        # Social zones CRUD & schedule management
```

---

## Tech Stack

| Component | Technology | Version |
| :--- | :--- | :--- |
| **Backend Framework** | Laravel | 8.83.27 |
| **Runtime Language** | PHP | 8.1 / 8.2 |
| **Database** | MySQL / SQLite (Testing) | 8.0 |
| **Authorization** | Spatie Laravel-Permission | 4.4 |
| **Excel / Reports** | Maatwebsite Excel | 3.1 |
| **DataTables** | Yajra DataTables Oracle | 9.21 |
| **Containerization** | Docker & Docker Compose | 3.8 |
| **CI / CD** | GitHub Actions | Ubuntu Latest |

---

## Quickstart with Docker

### 1. Clone the Repository
```bash
git clone git@github.com:KevinAndrey96/proptech-residential-management-platform.git
cd proptech-residential-management-platform
```

### 2. Configure Environment
```bash
cp .env.example .env
```

### 3. Launch Containers
```bash
docker compose up -d --build
```
This boots up:
- **Web**: Nginx 1.25 Alpine on port `8000`
- **App**: PHP 8.1 FPM with required extensions (pdo_mysql, gd, zip, bcmath)
- **Database**: MySQL 8.0 on port `3306`
- **Mail**: Mailpit web UI on port `8025` (SMTP port `1025`)

### 4. Run Migrations & Seed Initial Data
```bash
docker compose exec app php artisan key:generate
docker compose exec app php artisan migrate --seed
```

### 5. Access the Platform
- **Application Portal**: [http://localhost:8000](http://localhost:8000)
- **Mailpit Webhook / Inbox**: [http://localhost:8025](http://localhost:8025)

---

## Default Seeded Accounts

The database seeder provisions standard test accounts for evaluation:

| Role | Email | Password | Access Scope |
| :--- | :--- | :--- | :--- |
| **Superadmin** | `superadmin@example.com` | `superadmin123` | Full system control & settings |
| **Administrator** | `administrator@example.com` | `administrator123` | Complex management & payments |
| **Receptionist** | `receptionist@example.com` | `receptionist123` | Social zones, news, visitors |
| **Resident** | `resident@example.com` | `resident123` | Social zone bookings & payment logs |

---

## Automated Testing

The platform includes a test suite covering feature workflows and unit business rules:

```bash
# Run tests inside Docker
docker compose exec app vendor/bin/phpunit

# Or run via ephemeral container
docker run --rm -v $(pwd):/var/www/html residential-complex-app vendor/bin/phpunit
```

### Test Coverage Highlights
- **`BookingTest`**: Service listing, reservation creation, 24-hour expiration cron validation, and status transitions.
- **`ResidentTest`**: Administrator resident registration, duplicate apartment prevention, and status toggles.
- **`ParkingTest`**: Parking lot auto-space generation, status and availability toggles, and security guard entry logging.
- **`PaymentTest`**: Condominium dues recording, duplicate payment prevention for the same month, and resident history view.
- **`ServiceUseCaseTest`**: Clean architecture use cases for clubhouse social amenities creation, schedule hours, and soft-deletion.

---

## License

This project is open-source software licensed under the [MIT License](LICENSE).
