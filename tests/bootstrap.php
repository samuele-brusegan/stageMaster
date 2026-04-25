<?php

// Test bootstrap file
require_once __DIR__ . '/../vendor/autoload.php';

// Define base path for the application
define('BASE_PATH', dirname(__DIR__));

// Load the application's imports for class loading (manual autoloader)
require_once BASE_PATH . '/app/Router.php';
require_once BASE_PATH . '/public/functions.php';

// Load Controllers
require_once BASE_PATH . '/app/Controllers/Controller.php';
require_once BASE_PATH . '/app/Controllers/ApiController.php';
require_once BASE_PATH . '/app/Controllers/TalentoController.php';
require_once BASE_PATH . '/app/Controllers/MediaController.php';
require_once BASE_PATH . '/app/Controllers/PlayerStateController.php';
require_once BASE_PATH . '/app/Controllers/ScreenController.php';
require_once BASE_PATH . '/app/Controllers/QueueController.php';
require_once BASE_PATH . '/app/Controllers/NoteController.php';
require_once BASE_PATH . '/app/Controllers/TransizioneController.php';
require_once BASE_PATH . '/app/Controllers/MediaLibraryController.php';

// Load Models
require_once BASE_PATH . '/app/Models/databaseConnector.php';
require_once BASE_PATH . '/app/Models/Talento.php';
require_once BASE_PATH . '/app/Models/Media.php';
require_once BASE_PATH . '/app/Models/PlayerState.php';
require_once BASE_PATH . '/app/Models/Screen.php';
require_once BASE_PATH . '/app/Models/MediaQueue.php';
require_once BASE_PATH . '/app/Models/NoteTecniche.php';
require_once BASE_PATH . '/app/Models/Transizione.php';
require_once BASE_PATH . '/app/Models/MediaLibrary.php';
