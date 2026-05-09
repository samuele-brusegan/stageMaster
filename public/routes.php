<?php
/*
 * Copyright (c) 2025. Brusegan Samuele
 * Questo file fa parte di StageMaster ed è rilasciato
 * sotto la licenza MIT. Vedere il file LICENSE per i dettagli.
 *
 * Routes are registered with explicit HTTP verbs. Use 'ANY' to keep the old
 * permissive matching for legacy callers.
 */

declare(strict_types=1);

use App\Router;

/** @var Router $router */
global $router;

// ========= Pages =========
$router->add('GET', '/',          'Controller', 'index');
$router->add('GET', '/dashboard', 'Controller', 'dashboard');
$router->add('GET', '/projector', 'Controller', 'projector');
$router->add('GET', '/admin',     'Controller', 'admin');
$router->add('GET', '/timeline',  'Controller', 'timeline');
$router->add('GET', '/editor',    'Controller', 'editor');

// ========= Talenti API =========
$router->add('GET',           '/api/talenti',          'TalentoController', 'list');
$router->add('GET',           '/api/talento',          'TalentoController', 'show');
$router->add(['POST', 'PUT'], '/api/talento/update',   'TalentoController', 'update');
$router->add('POST',          '/api/talento/reorder',  'TalentoController', 'reorder');
$router->add('POST',          '/api/talenti/aggiungi', 'ApiController',     'addTalento');
$router->add('DELETE',        '/api/talenti/elimina',  'ApiController',     'deleteTalento');
$router->add('POST',          '/api/talenti/riordina', 'ApiController',     'reorderTalento');

// ========= Media API =========
$router->add('GET',    '/api/media',                  'MediaController', 'index');
$router->add('POST',   '/api/media',                  'MediaController', 'create');
$router->add('DELETE', '/api/media',                  'MediaController', 'delete');
$router->add('GET',    '/api/media/talento',          'MediaController', 'getByTalento');
$router->add('POST',   '/api/media/timeline/update',  'MediaController', 'updateTimeline');
$router->add('POST',   '/api/media/timeline/reorder', 'MediaController', 'reorderTimeline');
$router->add('POST',   '/api/slot-media/add',         'MediaController', 'addToSlot');

// ========= Player State API =========
$router->add('GET',  '/api/state',        'PlayerStateController', 'index');
$router->add('GET',  '/api/state/show',   'PlayerStateController', 'show');
$router->add('POST', '/api/state/update', 'PlayerStateController', 'update');

// ========= Screens API =========
$router->add('GET',    '/api/screens',         'ScreenController', 'index');
$router->add('GET',    '/api/screens/show',    'ScreenController', 'show');
$router->add('POST',   '/api/screens/create',  'ScreenController', 'create');
$router->add('POST',   '/api/screens/update',  'ScreenController', 'update');
$router->add('DELETE', '/api/screens/delete',  'ScreenController', 'delete');

// ========= Notes API =========
$router->add('GET',    '/api/notes',         'NoteController', 'index');
$router->add('GET',    '/api/notes/show',    'NoteController', 'show');
$router->add('GET',    '/api/notes/grouped', 'NoteController', 'grouped');
$router->add('POST',   '/api/notes/create',  'NoteController', 'create');
$router->add('POST',   '/api/notes/update',  'NoteController', 'update');
$router->add('DELETE', '/api/notes/delete',  'NoteController', 'delete');

// ========= Transitions API =========
$router->add('GET',    '/api/transizioni/show',          'TransizioneController', 'show');
$router->add('POST',   '/api/transizioni/create',        'TransizioneController', 'create');
$router->add('POST',   '/api/transizioni/update',        'TransizioneController', 'update');
$router->add('DELETE', '/api/transizioni/delete',        'TransizioneController', 'delete');
$router->add('GET',    '/api/transizioni/get-or-create', 'TransizioneController', 'getOrCreate');

// ========= Media Library API =========
$router->add('GET',    '/api/media-library',          'MediaLibraryController', 'index');
$router->add('POST',   '/api/media-library/upload',   'MediaLibraryController', 'upload');
$router->add('DELETE', '/api/media-library/delete',   'MediaLibraryController', 'delete');
$router->add('GET',    '/api/media-library/scan',     'MediaLibraryController', 'scan');
$router->add('POST',   '/api/media-library/register', 'MediaLibraryController', 'register');
