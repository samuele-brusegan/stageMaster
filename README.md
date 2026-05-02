# StageMaster

Talent Show Management System - A comprehensive web application for managing talent shows, including talent scheduling, media playback, screen management, and technical notes.

## Features

- **Talent Management**: Create, edit, and reorder talent performances
- **Media Management**: Organize and manage media files for each performance
- **Screen Control**: Manage multiple display screens (projectors, gobos)
- **Playback Queue**: Control media playback order and status
- **Technical Notes**: Store stage material, lighting notes, and other technical information
- **Transitions**: Configure media transitions and effects
- **Real-time Synchronization**: Multi-window communication using localStorage
- **PWA Support**: Progressive Web App with offline capabilities

## Tech Stack

- **Backend**: PHP 8.2+ with custom MVC architecture
- **Database**: MySQL 8.0
- **Frontend**: JavaScript (ES6+), TailwindCSS
- **Testing**:
  - PHPUnit 10.5 for PHP unit tests
  - Jest 29.x for JavaScript unit tests
  - Playwright for E2E browser testing
- **Containerization**: Docker & Docker Compose
- **CI/CD**: GitHub Actions
- **Debugging**: Xdebug 3.4

## Prerequisites

- PHP 8.2 or higher
- MySQL 8.0
- Composer
- Node.js 20+ (for JavaScript testing)
- Docker & Docker Compose (optional but recommended)

## Installation

### Using Docker (Recommended)

1. Clone the repository:
```bash
git clone https://github.com/samuele-brusegan/stageMaster.git
cd stageMaster
```

2. Start the containers:
```bash
docker compose up -d
```

3. Access the application:
- Application: http://localhost:31415

### Manual Installation

1. Clone the repository:
```bash
git clone https://github.com/samuele-brusegan/stageMaster.git
cd stageMaster
```

2. Install PHP dependencies:
```bash
composer install
```

3. Install JavaScript dependencies (for testing):
```bash
npm install
```

4. Configure the database:
```bash
mysql -u root -p < schema.sql
mysql -u root -p < schema_migration.sql
```

5. Configure web server to point to the `public` directory

## Database Setup

The application uses two SQL files for database initialization:

- `schema.sql`: Initial database schema with basic tables
- `schema_migration.sql`: Additional tables and migrations for advanced features

Run these files in order to set up the complete database structure.

## Testing

### PHP Tests (PHPUnit)

Run all PHP tests:
```bash
composer test
```

Run tests with coverage:
```bash
composer test-coverage
```

Run specific test file:
```bash
./vendor/bin/phpunit tests/Unit/Models/TalentoTest.php
```

### JavaScript Tests (Jest)

Run all JavaScript tests:
```bash
npm test
```

Run tests with coverage:
```bash
npm run test:coverage
```

Run tests in watch mode:
```bash
npm run test:watch
```

### E2E Tests (Playwright)

Run all E2E tests (requires running application):
```bash
npm run test:e2e
```

Run E2E tests with UI mode:
```bash
npm run test:e2e:ui
```

Run E2E tests in headed mode (visible browser):
```bash
npm run test:e2e:headed
```

Note: Playwright tests require the application to be running. Use `docker compose up -d` or `php -S localhost:31415 -t public` before running E2E tests.

### VS Code Debugging

The project includes a `.vscode/launch.json` configuration for debugging tests:

1. Install the "PHP Debug" extension in VS Code
2. Set breakpoints in your test files
3. Press F5 and select a configuration:
   - **Listen for Xdebug**: For debugging web requests
   - **Run All PHPUnit Tests**: Debug all PHP tests
   - **Run Specific PHPUnit Test**: Debug the current test file
   - **Run PHPUnit with Coverage**: Generate coverage report

## Project Structure

```
stageMaster/
├── app/
│   ├── Controllers/      # MVC Controllers
│   ├── Models/          # Data Models
│   ├── Router.php       # Custom Router
│   └── bootstrap.php    # Application bootstrap
├── public/
│   ├── components/      # JavaScript components
│   ├── pwa/            # Progressive Web App files
│   ├── css/            # Stylesheets
│   ├── js/             # Frontend JavaScript
│   ├── index.php       # Entry point
│   └── routes.php      # Route definitions
├── tests/
│   ├── Unit/           # PHP unit tests
│   │   ├── Controllers/
│   │   └── Models/
│   ├── js/             # JavaScript unit tests (Jest)
│   ├── e2e/            # End-to-end tests (Playwright)
│   ├── TestCase.php    # Base test class
│   └── bootstrap.php   # Test bootstrap
├── docker-compose.yml  # Docker configuration
├── composer.json       # PHP dependencies
├── package.json        # JavaScript dependencies
├── phpunit.xml         # PHPUnit configuration
└── playwright.config.ts # Playwright E2E test configuration
```

## .gitignore

The following files/directories are excluded from version control:
- node_modules/       # Node.js dependencies
- .phpunit.cache/     # PHPUnit cache
- playwright-report/  # Playwright test reports
- test-results/       # Playwright failure artifacts
- playwright-cache/   # Playwright browser cache
```

## API Endpoints

### Talent Management
- `GET /api/talenti` - List all talents (scaletta)
- `GET /api/talento` - Show single talent with media
- `POST /api/talento/reorder` - Reorder talent list
- `POST /api/talenti/aggiungi` - Add new talent
- `DELETE /api/talenti/elimina` - Delete talent

### Media Management
- `GET /api/media/talento` - Get media by talent
- `GET /api/media` - Show single media

### Screens
- `GET /api/screens` - List all screens
- `GET /api/screens/show` - Show screen details
- `POST /api/screens/create` - Create screen
- `PUT /api/screens/update` - Update screen
- `DELETE /api/screens/delete` - Delete screen

### Queue
- `GET /api/queue` - List queue
- `POST /api/queue/add` - Add to queue
- `PUT /api/queue/status` - Update queue status
- `DELETE /api/queue/remove` - Remove from queue

### Notes
- `GET /api/notes` - List all notes
- `GET /api/notes/grouped` - Get notes grouped by type
- `POST /api/notes/create` - Create note
- `PUT /api/notes/update` - Update note
- `DELETE /api/notes/delete` - Delete note

## Configuration

### Environment Variables

The application uses the following environment variables (can be set in `.env` or server configuration):

```php
DB_HOST=db              // Database host
DB_PORT=3306           // Database port
DB_NAME=olmos_talent    // Database name
DB_USERNAME=olmos_user  // Database username
DB_PASSWORD=user_password  // Database password
```

### Test Database Configuration

For testing, PHPUnit uses a separate database configured in `phpunit.xml`:

```xml
<env name="DB_HOST" value="127.0.0.1"/>
<env name="DB_PORT" value="3309"/>
<env name="DB_NAME" value="olmos_talent_test"/>
<env name="DB_USERNAME" value="root"/>
<env name="DB_PASSWORD" value="root_password"/>
```

## CI/CD

The project uses GitHub Actions for automated testing on push and pull requests:

- **test-php**: Runs PHPUnit tests with MySQL service
- **test-js**: Runs Jest tests with Node.js
- **test-e2e**: Runs Playwright E2E tests with PHP, MySQL, and browser automation

## Contributing

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

### Code Style

- Follow PSR-12 coding standards for PHP
- Use descriptive variable and function names
- Add comments for complex logic
- Write tests for new features

## License

This project is licensed under the MIT License - see the LICENSE file for details.

## Authors

- **Brusegan Samuele** - [samuele@example.com](mailto:samuele@example.com)

## Acknowledgments

- Built with custom MVC architecture
- Uses TailwindCSS for styling
- Progressive Web App capabilities
- Real-time multi-window synchronization
