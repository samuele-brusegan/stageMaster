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

// === API ===
$router->add('/api/talenti'             , 'ApiController', 'getTalenti');
$router->add('/api/talenti/aggiungi'    , 'ApiController', 'addTalento');
$router->add('/api/talenti/elimina'     , 'ApiController', 'deleteTalento');
$router->add('/api/talenti/riordina'    , 'ApiController', 'reorderTalento');

