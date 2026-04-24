<?php
/*
 * Copyright (c) 2025. Brusegan Samuele, Davanzo Andrea
 * Questo file fa parte di GradeCraft ed è rilasciato
 * sotto la licenza MIT. Vedere il file LICENSE per i dettagli.
 */
global $router;

// === Pagine ===
$router->add('/'                        , 'Controller', 'index');

// === API Talenti ===
$router->add('/api/talenti'             , 'TalentoController', 'list');
$router->add('/api/talento'              , 'TalentoController', 'show');
$router->add('/api/talento/reorder'      , 'TalentoController', 'reorder');

// === API Media ===
$router->add('/api/media/talento'       , 'MediaController', 'getByTalento');
$router->add('/api/media'               , 'MediaController', 'show');

// === API Player State ===
$router->add('/api/state'               , 'PlayerStateController', 'index');
$router->add('/api/state/show'          , 'PlayerStateController', 'show');
$router->add('/api/state/update'        , 'PlayerStateController', 'update');
$router->add('/dashboard'               , 'Controller', 'dashboard');
$router->add('/projector'               , 'Controller', 'projector');
$router->add('/gobbo'                   , 'Controller', 'gobbo');
$router->add('/admin'                   , 'Controller', 'admin');
$router->add('/timeline'                , 'Controller', 'timeline');

// === API ===
$router->add('/api/talenti'             , 'ApiController', 'getTalenti');
$router->add('/api/talenti/aggiungi'    , 'ApiController', 'addTalento');
$router->add('/api/talenti/elimina'     , 'ApiController', 'deleteTalento');
$router->add('/api/talenti/riordina'    , 'ApiController', 'reorderTalento');

// === API Screens ===
$router->add('/api/screens'              , 'ScreenController', 'index');
$router->add('/api/screens/show'         , 'ScreenController', 'show');
$router->add('/api/screens/create'       , 'ScreenController', 'create');
$router->add('/api/screens/update'       , 'ScreenController', 'update');
$router->add('/api/screens/delete'       , 'ScreenController', 'delete');

// === API Queue ===
$router->add('/api/queue'                , 'QueueController', 'index');
$router->add('/api/queue/show'           , 'QueueController', 'show');
$router->add('/api/queue/add'            , 'QueueController', 'add');
$router->add('/api/queue/status'         , 'QueueController', 'updateStatus');
$router->add('/api/queue/reorder'        , 'QueueController', 'reorder');
$router->add('/api/queue/remove'         , 'QueueController', 'remove');
$router->add('/api/queue/playing'       , 'QueueController', 'playing');
$router->add('/api/queue/next'           , 'QueueController', 'next');

// === API Notes ===
$router->add('/api/notes'                , 'NoteController', 'index');
$router->add('/api/notes/show'           , 'NoteController', 'show');
$router->add('/api/notes/grouped'        , 'NoteController', 'grouped');
$router->add('/api/notes/create'        , 'NoteController', 'create');
$router->add('/api/notes/update'        , 'NoteController', 'update');
$router->add('/api/notes/delete'        , 'NoteController', 'delete');

// === API Transizioni ===
$router->add('/api/transizioni/show'     , 'TransizioneController', 'show');
$router->add('/api/transizioni/create'   , 'TransizioneController', 'create');
$router->add('/api/transizioni/update'   , 'TransizioneController', 'update');
$router->add('/api/transizioni/delete'   , 'TransizioneController', 'delete');
$router->add('/api/transizioni/get-or-create', 'TransizioneController', 'getOrCreate');

// === API Media Library ===
$router->add('/api/media-library'        , 'MediaLibraryController', 'index');
$router->add('/api/media-library/upload' , 'MediaLibraryController', 'upload');
$router->add('/api/media-library/delete' , 'MediaLibraryController', 'delete');
$router->add('/api/media-library/scan'   , 'MediaLibraryController', 'scan');
$router->add('/api/media-library/register', 'MediaLibraryController', 'register');

