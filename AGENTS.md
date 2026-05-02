# Repository Guidelines

## Project Structure & Module Organization

StageMaster is a PHP 8.2 custom MVC application with browser-facing assets under `public/`. Core backend code lives in `app/`: `Controllers/` handle requests, `Models/` contain data logic, `Views/` hold PHP templates, and `Router.php` plus `bootstrap.php` wire the app together. Public entry points and routes are in `public/index.php` and `public/routes.php`; JavaScript components are in `public/components/`, styles in `public/css/`, PWA assets in `public/pwa/`, SVGs in `public/svg/`, and media in `public/media/`. Tests are split into `tests/Unit/`, `tests/js/`, and `tests/e2e/`. Database schema files are at the repo root.

## Build, Test, and Development Commands

- `composer install` installs PHP dependencies and configures PSR-4 autoloading for `App\\` and `Tests\\`.
- `npm install` installs JavaScript test tooling.
- `docker compose up -d` starts the local app stack; Playwright expects `http://localhost:31415`.
- `composer test` runs PHPUnit unit tests from `tests/Unit`.
- `composer test-coverage` writes PHP coverage to `coverage/` and prints a text report.
- `npm test` runs Jest tests matching `tests/js/**/*.test.js`.
- `npm run test:coverage` collects JavaScript coverage for `public/**/*.js`.
- `npm run test:e2e` runs Playwright browser tests in Chromium, Firefox, and WebKit.

## Coding Style & Naming Conventions

Use PSR-4 namespaces and keep PHP classes aligned with file names, for example `App\Controllers\MediaController` in `app/Controllers/MediaController.php`. Follow the existing 4-space PHP indentation and concise controller/model method names. JavaScript in `public/components/` uses ES modules/classes; name component files in PascalCase such as `StopCard.js`. Keep asset names descriptive.

## Testing Guidelines

Add PHPUnit tests beside the relevant domain area under `tests/Unit/Controllers/` or `tests/Unit/Models/`, using `*Test.php` names. Add Jest tests as `*.test.js` in `tests/js/`. E2E specs belong in `tests/e2e/` as `*.spec.ts`. Run the narrow test first, then the full relevant suite before submitting. PHPUnit is strict about risky tests and unexpected output, so avoid debug prints.

## Commit & Pull Request Guidelines

Recent commits use short imperative summaries with prefixes such as `Fix:`, `Refactoring:`, and `Implement`. Keep commits focused and mention the affected subsystem when useful. Pull requests should include a brief problem/solution summary, test commands run, linked issues if any, and screenshots or recordings for visible UI changes. Note database or Docker changes explicitly.

## Security & Configuration Tips

Do not commit local credentials or generated reports. Keep database settings in environment-specific configuration; `phpunit.xml` uses test database defaults on port `3309`. Treat `public/media/` uploads as untrusted input when adding file handling.
