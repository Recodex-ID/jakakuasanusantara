# Face Recognition Attendance System

A comprehensive attendance management system built with Laravel 12 and integrated with Biznet Face Recognition API for secure, contactless employee attendance tracking.

## Overview

This system provides a modern solution for employee attendance management using facial recognition technology. It features role-based access control, location-based attendance validation, and real-time monitoring capabilities.

## Features

### Admin Features

- **Employee Management**: Create, update, and manage employee profiles
- **Location Management**: Define attendance locations with geographic coordinates
- **Face Gallery Management**: Enroll and manage employee face data
- **Attendance Monitoring**: Real-time attendance tracking and manual overrides
- **Comprehensive Reports**: Attendance history, analytics, and export capabilities

### Employee Features

- **Face Recognition Check-in/Out**: Secure attendance recording using facial recognition
- **Location Validation**: Ensure attendance is recorded at assigned locations
- **Personal Dashboard**: View attendance history and personal statistics
- **Working Hours Management**: Flexible work schedules with late tolerance

## Technology Stack

- **Backend**: Laravel 12 with Blade templating
- **Database**: MySQL/SQLite
- **Frontend**: Blade + Alpine.js
- **Face Recognition**: Biznet Face Recognition API
- **Authentication**: Laravel's built-in authentication
- **Styling**: Bootstrap/Tailwind CSS

## System Requirements

- PHP 8.2+
- Laravel 12
- MySQL 8.0+ or SQLite
- Composer
- Node.js & NPM

## Installation

1. Clone the repository:

```bash
git clone https://github.com/your-username/jakakuasanusantara.git
cd jakakuasanusantara
```

2. Install dependencies:

```bash
composer install
npm install
```

3. Environment setup:

```bash
cp .env.example .env
php artisan key:generate
```

4. Configure database in `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=attendance_system
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

5. Configure Biznet Face API:

```env
BIZNET_FACE_API_URL=https://fr.neoapi.id/risetai/face-api/
BIZNET_FACE_API_TOKEN=your_api_token_here
BIZNET_FACE_API_TIMEOUT=30
BIZNET_FACE_SIMILARITY_THRESHOLD=0.75
```

6. Run migrations and seeders:

```bash
php artisan migrate --seed
```

7. Build frontend assets:

```bash
npm run dev
```

8. Start the development server:

```bash
php artisan serve
```

## Database Schema

The system uses the following core tables:

- **users**: System users (admins and employees)
- **employees**: Employee profiles with work schedules
- **locations**: Attendance locations with coordinates
- **attendances**: Daily attendance records
- **attendance_logs**: Detailed attendance activity logs

## API Integration

### Biznet Face Recognition API

The system integrates with Biznet Face API for:

- Face enrollment and management
- Face verification for attendance
- Face identification for automated check-in
- Image processing and validation

### Key API Endpoints Used

- Client management and quota monitoring
- Face gallery operations
- Employee face enrollment/verification
- Image comparison and processing

## Security Features

- **Role-based Access Control**: Admin and Employee roles
- **Location-based Validation**: Attendance restricted to assigned locations
- **Secure Face Data Storage**: Encrypted face recognition data
- **API Rate Limiting**: Protection against abuse
- **Audit Trail**: Complete activity logging

## Development

### Running Tests

```bash
php artisan test
```

### Code Quality

```bash
./vendor/bin/pint # Code formatting
./vendor/bin/phpstan analyse # Static analysis
```

### Development Server

```bash
composer run dev # Starts all services (server, queue, logs, vite)
```

## Commands

The system includes several Artisan commands:

- `php artisan face:init-gallery` - Initialize face gallery
- `php artisan face:sync-employees` - Sync employee faces
- `php artisan face:test-api` - Test Face API connection
- `php artisan face:manage-employee` - Manage employee faces
- `php artisan face:gallery-status` - Check gallery status

## Usage

### Admin Access

1. Login with admin credentials
2. Navigate to Employee Management to add employees
3. Set up locations with coordinates
4. Enroll employee faces through Face Enrollment
5. Monitor attendance through the dashboard

### Employee Access

1. Login with employee credentials
2. Navigate to attendance page
3. Allow camera access for face recognition
4. Check-in/out at assigned locations
5. View attendance history in dashboard

## Configuration

### Working Hours

Configure employee working hours in the employee profile:

- Start time and end time
- Work days (Monday-Sunday)
- Late tolerance minutes
- Location assignments

### Face Recognition Settings

Adjust face recognition parameters:

- Similarity threshold
- API timeout settings
- Image quality requirements

## Documentation

Additional documentation available in the `/docs` folder:

- [Project Brief](docs/PROJECT-BRIEF.md)
- [Biznet Face API Guide](docs/BIZNET-FACE-API.md)
- [API Tutorial](docs/TUTORIAL-BIZNET-API.md)

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## Support

For support and questions, please contact the development team or create an issue in the repository.

## Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Add tests for new functionality
5. Submit a pull request

## Changelog

See the commit history for detailed changes and updates to the system.
