<?php

declare(strict_types=1);

/**
 * Legacy include kept for backward compatibility. PSR-4 autoloading is now
 * configured in composer.json (App\\ => app/) so explicit requires are no
 * longer necessary. Anything still referencing this file will simply load
 * Composer's autoloader and the legacy DatabaseConnector adapter.
 */

require_once BASE_PATH . '/app/bootstrap.php';
