# PropTech Residential Management Platform

[![CI](https://github.com/KevinAndrey96/proptech-residential-management-platform/actions/workflows/ci.yml/badge.svg)](https://github.com/KevinAndrey96/proptech-residential-management-platform/actions/workflows/ci.yml)
[![PHP](https://img.shields.io/badge/PHP-8.1%20%7C%208.2-777BB4?logo=php&logoColor=white)](https://www.php.net/)
[![Laravel](https://img.shields.io/badge/Laravel-8.83-FF2D20?logo=laravel&logoColor=white)](https://laravel.com/)
[![MySQL](https://img.shields.io/badge/MySQL-8.0-4479A1?logo=mysql&logoColor=white)](https://www.mysql.com/)
[![Docker](https://img.shields.io/badge/Docker-Compose-2496ED?logo=docker&logoColor=white)](https://www.docker.com/)

Plataforma de administración de condominios: reservas del salón social y zonas comunes, asignación de parqueaderos, gestión de pagos y de residentes. Operada en producción en complejos residenciales de varias torres (incluido Reserva de Toscana), con cientos de apartamentos, habitantes, espacios sociales y cuotas administrativas.

---

## Características principales

- **Motor de reservas de salón social y zonas comunes**:
  - Límites de capacidad configurables, intervalos de horario (`strip` en minutos) y horarios de operación por día de la semana.
  - Prevención de conflictos en reservas simultáneas y límites diarios por residente.
  - Comando automatizado (`php artisan booking:cancel`) para expirar reservas abandonadas y actualizar horarios pasados.
  - Notificación por correo mediante plantillas Mailable al confirmar una reserva.

- **Asignación de parqueaderos y operación de seguridad**:
  - Zonas de parqueo segmentadas (residente y visitante).
  - Cambio de ocupación en tiempo real y bloqueos por mantenimiento.
  - Registro detallado de entradas y salidas del guardia de seguridad (`DetailSpaceOccupation`), con placa, nombre del visitante, apartamento de destino y observaciones.

- **Cuotas y gestión de pagos**:
  - Seguimiento y verificación de las cuotas mensuales del condominio.
  - Filtros por año y mes, descarga de comprobantes y prevención de pagos duplicados.
  - Portal de autoservicio para que el residente consulte el estado de pagos y su historial de facturación.

- **Registro de residentes, vehículos y mascotas**:
  - Inventario de apartamentos y torres con número de habitantes.
  - Registro de convivientes, vehículos autorizados y mascotas.
  - Cambio de estado con un clic (Habilitado / Inhabilitado) para controlar el acceso a las zonas comunes.

- **Control de acceso basado en roles (RBAC)**:
  - Permisos granulares con Spatie Laravel-Permission.
  - Roles incluidos: `Superadmin`, `Administrator`, `Receptionist`, `Resident` y `Watchman` (Guardia de Seguridad).

---

## Arquitectura limpia y diseño del sistema

La aplicación sigue los principios de Arquitectura Limpia para aislar las reglas de negocio de los controladores HTTP y de la capa de persistencia:

```
app/
├── Console/
│   └── Commands/
│       └── CancelBookings.php           # Worker de expiración de reservas
├── DTOs/
│   └── WatchmanDTO.php                  # Objetos de transferencia de datos
├── Http/
│   ├── Controllers/                     # Controladores delgados que orquestan las peticiones
│   │   ├── Bookings/
│   │   ├── Residents/
│   │   ├── Parkings/
│   │   ├── ParkingSpaces/
│   │   ├── Payments/
│   │   └── Services/
│   └── Middleware/
├── Models/                              # Entidades Eloquent y sus relaciones
│   ├── Booking.php
│   ├── DetailSpaceOccupation.php
│   ├── Parking.php
│   ├── ParkingSpace.php
│   ├── Payment.php
│   ├── Resident.php
│   └── Service.php
├── Providers/
│   ├── RepositoriesServicesProvider.php # Enlaces del contenedor IoC para los contratos de repositorio
│   └── UseCasesServicesProvider.php     # Enlaces del contenedor IoC para los contratos de casos de uso
├── Repositories/                        # Capa de acceso a datos
│   ├── Contracts/                       # Interfaces abstractas de repositorio
│   ├── DetailSpaceOccupations/
│   ├── ParkingSpaces/
│   ├── Parkings/
│   ├── Payments/
│   └── Residents/
└── UseCases/                            # Reglas de negocio de la aplicación encapsuladas
    ├── Contracts/                       # Interfaces de casos de uso
    ├── Bookings/                        # Validación y procesamiento de reservas
    ├── Residents/                       # Alta de residentes y cambios de estado
    ├── Roles/                           # Lógica de asignación de roles RBAC
    └── Services/                        # CRUD de zonas sociales y gestión de horarios
```

---

## Stack tecnológico

| Componente | Tecnología | Versión |
| :--- | :--- | :--- |
| **Framework backend** | Laravel | 8.83.27 |
| **Lenguaje de ejecución** | PHP | 8.1 / 8.2 |
| **Base de datos** | MySQL / SQLite (pruebas) | 8.0 |
| **Autorización** | Spatie Laravel-Permission | 4.4 |
| **Excel / reportes** | Maatwebsite Excel | 3.1 |
| **DataTables** | Yajra DataTables Oracle | 9.21 |
| **Contenedores** | Docker y Docker Compose | 3.8 |
| **CI / CD** | GitHub Actions | Ubuntu Latest |

---

## Inicio rápido con Docker

### 1. Clonar el repositorio
```bash
git clone git@github.com:KevinAndrey96/proptech-residential-management-platform.git
cd proptech-residential-management-platform
```

### 2. Configurar el entorno
```bash
cp .env.example .env
```
Edita `.env` y define `DB_PASSWORD`. Docker Compose lee esta variable para la base de datos y no arranca si está vacía.

### 3. Levantar los contenedores
```bash
docker compose up -d --build
```
Esto levanta:
- **Web**: Nginx 1.25 Alpine en el puerto `8000`
- **App**: PHP 8.1 FPM con las extensiones necesarias (pdo_mysql, gd, zip, bcmath)
- **Base de datos**: MySQL 8.0 en el puerto `3306`
- **Correo**: interfaz web de Mailpit en el puerto `8025` (SMTP en el `1025`)

### 4. Generar la clave y cargar datos iniciales
```bash
docker compose exec app php artisan key:generate
docker compose exec app php artisan migrate --seed
```

### 5. Acceder a la plataforma
- **Portal de la aplicación**: [http://localhost:8000](http://localhost:8000)
- **Buzón de Mailpit**: [http://localhost:8025](http://localhost:8025)

---

## Cuentas de prueba precargadas

El seeder de la base de datos crea cuentas de prueba para evaluación. No las uses en producción y cámbialas antes de desplegar.

| Rol | Correo | Contraseña | Alcance |
| :--- | :--- | :--- | :--- |
| **Superadmin** | `superadmin@example.com` | `superadmin123` | Control total del sistema y configuración |
| **Administrator** | `administrator@example.com` | `administrator123` | Gestión del complejo y pagos |
| **Receptionist** | `receptionist@example.com` | `receptionist123` | Zonas sociales, noticias y visitantes |
| **Resident** | `resident@example.com` | `resident123` | Reservas de zonas sociales y registro de pagos |

---

## Pruebas automatizadas

La plataforma incluye una suite de pruebas que cubre los flujos funcionales y las reglas de negocio:

```bash
# Ejecutar las pruebas dentro de Docker
docker compose exec app vendor/bin/phpunit

# O en un contenedor efímero
docker run --rm -v $(pwd):/var/www/html residential-complex-app vendor/bin/phpunit
```

### Cobertura destacada
- **`BookingTest`**: listado de servicios, creación de reservas, validación de expiración de 24 horas por cron y transiciones de estado.
- **`ResidentTest`**: registro de residentes por el administrador, prevención de apartamentos duplicados y cambios de estado.
- **`ParkingTest`**: generación automática de espacios de parqueo, cambio de estado y disponibilidad, y registro de entradas del guardia.
- **`PaymentTest`**: registro de cuotas, prevención de pagos duplicados para el mismo mes y vista del historial del residente.
- **`ServiceUseCaseTest`**: casos de uso de arquitectura limpia para crear zonas sociales, definir horarios y eliminación lógica.

---

## Licencia

Sin licencia de código abierto. Todos los derechos reservados.

El código se publica solo como referencia. Su uso, copia, modificación o redistribución requiere autorización expresa del titular.
