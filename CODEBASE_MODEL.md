# Olmo's Got Talent Manager - Modello Codebase

## Panoramica del Progetto

**Nome**: Olmo's Got Talent Manager  
**Tipo**: Applicazione Web per Gestione Talent Show  
**Stack Tecnologico**: PHP 8.2, MySQL 8.0, Apache, JavaScript, TailwindCSS  
**Licenza**: MIT  
**Autori**: Brusegan Samuele, Davanzo Andrea

Applicazione per la gestione completa di talent show, con funzionalità di:
- Gestione scaletta talenti
- Controllo multi-schermo (proiettore, gobbo)
- Timeline media con transizioni
- Sistema di coda esecuzione
- Note tecniche per palco e luci
- Sincronizzazione finestre via localStorage

---

## Struttura delle Directory

```
olmosgottalent-manager/
├── app/
│   ├── Controllers/        # Gestione richieste HTTP
│   ├── Models/            # Logica dati e database
│   ├── Views/             # Template HTML
│   ├── Router.php         # Routing custom
│   └── bootstrap.php      # Configurazione iniziale
├── config/
│   └── database.php       # Connessione database PDO
├── public/
│   ├── components/        # Componenti JavaScript riutilizzabili
│   ├── commons/           # Template condivisi (head.php)
│   ├── css/               # Fogli di stile
│   ├── pwa/               # Risorse Progressive Web App
│   ├── functions.php      # Funzioni helper
│   ├── imports.php        # Autoloading classi
│   ├── index.php          # Entry point applicazione
│   ├── routes.php         # Definizione rotte
│   └── .htaccess          # Configurazione Apache
├── scripts/
│   ├── deploy.sh          # Script deploy tradizionale
│   └── deploy_docker.sh   # Script deploy Docker
├── docs/                  # Documentazione progetto
├── ai_notes/              # Note AI su struttura
├── Dockerfile             # Configurazione container Docker
├── docker-compose.yml     # Orchestrazione servizi Docker
├── schema.sql             # Schema database iniziale
├── schema_migration.sql   # Migration database
├── migrate_data.php       # Script migrazione dati
├── run_migration.php      # Esecuzione migration
└── test_data.sql          # Dati di test
```

---

## Architettura MVC

### Pattern Architetturale

L'applicazione segue un pattern MVC custom con:

1. **Router**: Dispatch basato su URL mappati a controller/metodi
2. **Controllers**: Gestiscono logica business e richieste HTTP
3. **Models**: Astrazione database con PDO
4. **Views**: Template PHP con HTML inline e JavaScript

### Flusso Richiesta

```
Request → .htaccess → index.php → Router → Controller → Model → View → Response
```

---

## Database Schema

### Tabelle Principali

#### `talenti`
Gestione partecipanti talent show
- `id` (PK, AUTO_INCREMENT)
- `nome` (VARCHAR 100, NOT NULL)
- `categoria` (VARCHAR 50)
- `materiale_palco` (TEXT)
- `note_luci` (TEXT)
- `ordine_scaletta` (INT, UNIQUE)

#### `media_performance`
File media associati ai talenti
- `id` (PK, AUTO_INCREMENT)
- `talento_id` (FK → talenti.id)
- `tipo_output` (ENUM: 'proiettore', 'gobbo')
- `file_path` (VARCHAR 255, NOT NULL)
- `screen_id` (FK → screens.id)
- `tipo_media` (ENUM: 'VIDEO', 'AUDIO', 'FOTO')
- `timestamp_inizio` (TIME, DEFAULT '00:00:00')
- `timestamp_fine` (TIME)
- `durata_totale_sec` (INT)
- `fade_in_sec` (INT, DEFAULT 0)
- `fade_out_sec` (INT, DEFAULT 0)
- `ordine_esecuzione` (INT)

#### `screens`
Configurazione schermi multipli
- `id` (PK, AUTO_INCREMENT)
- `nome` (VARCHAR 50, NOT NULL)
- `tipo` (ENUM: 'indipendente', 'mirror', DEFAULT 'indipendente')
- `screen_riferimento_id` (FK → screens.id, NULL)

#### `media_queue`
Sistema di coda esecuzione
- `id` (PK, AUTO_INCREMENT)
- `talento_id` (FK → talenti.id)
- `media_id` (FK → media_performance.id)
- `ordine_coda` (INT, NOT NULL)
- `stato` (ENUM: 'pending', 'playing', 'completed', 'skipped', DEFAULT 'pending')
- `created_at` (TIMESTAMP)
- `updated_at` (TIMESTAMP)

#### `note_tecniche`
Note organizzative per categoria
- `id` (PK, AUTO_INCREMENT)
- `talento_id` (FK → talenti.id, NULL)
- `tipo` (ENUM: 'materiale_palco', 'luci', 'generiche', 'pause')
- `contenuto` (TEXT, NOT NULL)
- `created_at` (TIMESTAMP)
- `updated_at` (TIMESTAMP)

#### `transizioni`
Configurazione effetti transizione
- `id` (PK, AUTO_INCREMENT)
- `media_id` (FK → media_performance.id)
- `tipo_dissolvenza` (ENUM: 'fade_to_black', 'fade_from_black', 'crossfade', 'cut', 'dissolve')
- `durata_sec` (DECIMAL 5,2)
- `offset_prima_sec` (DECIMAL 5,2)
- `offset_dopo_sec` (DECIMAL 5,2)
- `created_at` (TIMESTAMP)

#### `player_state`
Stato player per sincronizzazione
- `component` (PK, ENUM: 'proiettore', 'gobbo')
- `current_talento_id` (FK → talenti.id)
- `current_media_id` (FK → media_performance.id)
- `status` (ENUM: 'playing', 'paused', 'stopped', DEFAULT 'stopped')
- `last_update` (TIMESTAMP)

---

## Controllers

### Controller Base
**File**: `app/Controllers/Controller.php`
- Metodo `render($view, $data)`: Rendering view con estrazione dati
- Metodi per pagine: `index()`, `dashboard()`, `projector()`, `gobbo()`, `admin()`, `timeline()`

### ApiController
**File**: `app/Controllers/ApiController.php`
- Estende `Controller`
- Metodo `json($data, $status)`: Response JSON
- Metodo `error($message, $status)`: Error response
- Metodo `getJsonInput()`: Parsing JSON body
- Metodo `validate($data, $rules)`: Validazione semplice
- API Talenti: `getTalenti()`, `addTalento()`, `deleteTalento()`, `reorderTalento()`

### TalentoController
**File**: `app/Controllers/TalentoController.php`
- Estende `ApiController`
- `list()`: Lista talenti ordinati per scaletta
- `show()`: Dettagli talento con media
- `reorder()`: Riordinamento drag & drop

### MediaController
**File**: `app/Controllers/MediaController.php`
- Estende `ApiController`
- `getByTalento()`: Media per talento specifico
- `show()`: Dettagli singolo media

### ScreenController
**File**: `app/Controllers/ScreenController.php`
- Estende `Controller`
- CRUD completo: `index()`, `show()`, `create()`, `update()`, `delete()`

### QueueController
**File**: `app/Controllers/QueueController.php`
- Estende `Controller`
- `index()`: Tutta la coda
- `show()`: Coda per talento
- `add()`: Aggiungi elemento
- `updateStatus()`: Aggiorna stato
- `reorder()`: Riordina coda
- `remove()`: Rimuovi elemento
- `playing()`: Elemento in esecuzione
- `next()`: Prossimo pending

### NoteController
**File**: `app/Controllers/NoteController.php`
- Estende `Controller`
- `index()`: Tutte le note
- `show()`: Note per talento
- `grouped()`: Note raggruppate per tipo
- CRUD: `create()`, `update()`, `delete()`

### TransizioneController
**File**: `app/Controllers/TransizioneController.php`
- Estende `Controller`
- `show()`: Transizione per media
- `create()`: Crea transizione
- `update()`: Aggiorna transizione
- `delete()`: Elimina transizione
- `getOrCreate()`: Get o create automatica

### PlayerStateController
**File**: `app/Controllers/PlayerStateController.php`
- Estende `ApiController`
- `index()`: Stato tutti componenti
- `show()`: Stato componente specifico
- `update()`: Aggiorna stato

---

## Models

### databaseConnector
**File**: `app/Models/databaseConnector.php`
- Singleton pattern per connessione PDO
- Carica configurazione da `.env`

### Talento
**File**: `app/Models/Talento.php`
- `create($data)`: Crea nuovo talento
- `find($id)`: Trova per ID
- `update($id, $data)`: Aggiorna
- `delete($id)`: Elimina
- `getScaletta()`: Lista ordinata
- `getWithMedia($id)`: Talento con media
- `reorder($orderedIds)`: Riordina con transazione

### Media
**File**: `app/Models/Media.php`
- `create($data)`: Crea media
- `find($id)`: Trova per ID
- `update($id, $data)`: Aggiorna
- `delete($id)`: Elimina
- `getByTalento($talento_id)`: Media per talento

### Screen
**File**: `app/Models/Screen.php`
- `getAll()`: Tutti gli schermi con riferimenti
- `find($id)`: Trova per ID
- `create($data)`: Crea schermo
- `update($id, $data)`: Aggiorna
- `delete($id)`: Elimina
- `getMedia($screen_id)`: Media assegnati

### MediaQueue
**File**: `app/Models/MediaQueue.php`
- `getAll()`: Tutta la coda con join
- `getByTalento($talento_id)`: Coda per talento
- `add($data)`: Aggiungi con auto-increment ordine
- `updateStatus($id, $status)`: Aggiorna stato
- `reorder($orderedIds)`: Riordina con transazione
- `remove($id)`: Rimuovi
- `getPlaying()`: Elemento in esecuzione
- `getNextPending()`: Prossimo pending

### NoteTecniche
**File**: `app/Models/NoteTecniche.php`
- `getAll()`: Tutte le note con join talenti
- `getByTalento($talento_id)`: Note per talento
- `getByType($tipo)`: Note per tipo
- `create($data)`: Crea nota
- `update($id, $data)`: Aggiorna
- `delete($id)`: Elimina
- `getGroupedByType($talento_id)`: Raggruppate per tipo

### Transizione
**File**: `app/Models/Transizione.php`
- `getByMedia($media_id)`: Transizione per media
- `create($data)`: Crea transizione
- `update($media_id, $data)`: Aggiorna
- `delete($media_id)`: Elimina
- `getOrCreate($media_id)`: Get o create

### PlayerState
**File**: `app/Models/PlayerState.php`
- `getState($component)`: Stato componente
- `getAllStates()`: Tutti gli stati
- `updateState($component, $data)`: Upsert stato

---

## Views

### dashboard.php
**Percorso**: `/dashboard`
- Interfaccia principale regia
- Layout 3 colonne:
  - **Sinistra**: Lista slot + coda esecuzione
  - **Centro**: Timeline + griglia 4 schermi
  - **Destra**: Controlli riproduzione + proprietà media + note tecniche + logs
- JavaScript per:
  - Fetch API per dati
  - Sincronizzazione localStorage
  - Gestione comandi play/pause/stop
  - Monitoraggio stato schermi

### admin.php
**Percorso**: `/admin`
- Pannello amministrativo tab-based
- Tabs:
  - **Slot**: CRUD talenti, riordinamento
  - **Media**: Gestione file media, assegnazione schermi
  - **Schermi**: Configurazione indipendente/mirror
  - **Note**: Gestione note tecniche
- TailwindCSS per UI moderna

### projector.php
**Percorso**: `/projector`
- Display fullscreen per proiezione
- Web Audio API per fade in/out
- Sincronizzazione via localStorage
- Overlay nero per transizioni
- Click-to-sync per autoplay
- Reporting stato periodico

### gobbo.php
**Percorso**: `/gobbo`
- Display testo grande per pubblico
- Ricezione contenuti via localStorage
- Clock integrato
- Click-to-sync per ricezione

### timeline.php
**Percorso**: `/timeline`
- (Non analizzato nel dettaglio, probabilmente timeline dettagliata per singolo talento)

---

## Routing

### File: `public/routes.php`

Rotte definite:

**Pagine**:
- `/` → `Controller::index()`
- `/dashboard` → `Controller::dashboard()`
- `/projector` → `Controller::projector()`
- `/gobbo` → `Controller::gobbo()`
- `/admin` → `Controller::admin()`
- `/timeline` → `Controller::timeline()`

**API Talenti**:
- `/api/talenti` → `TalentoController::list()`
- `/api/talento` → `TalentoController::show()`
- `/api/talento/reorder` → `TalentoController::reorder()`
- `/api/talenti/aggiungi` → `ApiController::addTalento()`
- `/api/talenti/elimina` → `ApiController::deleteTalento()`
- `/api/talenti/riordina` → `ApiController::reorderTalento()`

**API Media**:
- `/api/media/talento` → `MediaController::getByTalento()`
- `/api/media` → `MediaController::show()`

**API Player State**:
- `/api/state` → `PlayerStateController::index()`
- `/api/state/show` → `PlayerStateController::show()`
- `/api/state/update` → `PlayerStateController::update()`

**API Screens**:
- `/api/screens` → `ScreenController::index()`
- `/api/screens/show` → `ScreenController::show()`
- `/api/screens/create` → `ScreenController::create()`
- `/api/screens/update` → `ScreenController::update()`
- `/api/screens/delete` → `ScreenController::delete()`

**API Queue**:
- `/api/queue` → `QueueController::index()`
- `/api/queue/show` → `QueueController::show()`
- `/api/queue/add` → `QueueController::add()`
- `/api/queue/status` → `QueueController::updateStatus()`
- `/api/queue/reorder` → `QueueController::reorder()`
- `/api/queue/remove` → `QueueController::remove()`
- `/api/queue/playing` → `QueueController::playing()`
- `/api/queue/next` → `QueueController::next()`

**API Notes**:
- `/api/notes` → `NoteController::index()`
- `/api/notes/show` → `NoteController::show()`
- `/api/notes/grouped` → `NoteController::grouped()`
- `/api/notes/create` → `NoteController::create()`
- `/api/notes/update` → `NoteController::update()`
- `/api/notes/delete` → `NoteController::delete()`

**API Transizioni**:
- `/api/transizioni/show` → `TransizioneController::show()`
- `/api/transizioni/create` → `TransizioneController::create()`
- `/api/transizioni/update` → `TransizioneController::update()`
- `/api/transizioni/delete` → `TransizioneController::delete()`
- `/api/transizioni/get-or-create` → `TransizioneController::getOrCreate()`

---

## Frontend

### JavaScript Components

#### StopCard.js
Componente riutilizzabile per card (probabilmente legacy o non utilizzato in questo progetto specifico)

#### StopListItem.js
Web Component custom (probabilmente legacy)

### CSS
- TailwindCSS via CDN per styling rapido
- Stili custom inline nelle views per effetti glass, gradienti, transitions

### PWA
Risorse Progressive Web App in `/public/pwa/`:
- `manifest.json`
- `service-worker.js`
- Icone varie dimensioni
- `site.webmanifest`

---

## Configurazione

### Bootstrap
**File**: `app/bootstrap.php`
- Costanti: `BASE_PATH`, `URL_PATH`, `URL`, `THEME`
- Autoloading imports
- Funzione `checkSessionExpiration()` (placeholder)

### Database
**File**: `config/database.php`
- Funzione `getDatabaseConnection()`
- Loading variabili `.env`
- PDO con opzioni:
  - ERRMODE_EXCEPTION
  - FETCH_ASSOC
  - EMULATE_PREPARES false

### Apache
**File**: `public/.htaccess`
- Rewrite rules per routing

---

## Docker

### Dockerfile
**Base**: `php:8.2-apache`
- Estensioni PHP: pdo_mysql, mbstring, exif, pcntl, bcmath, gd
- mod_rewrite abilitato
- DocumentRoot: `/var/www/html/public`
- Permissions: www-data:www-data

### docker-compose.yml
**Servizi**:
1. **app**: Container PHP-Apache
   - Port: 8082:80
   - Volume mount progetto
   - Network: olmos-network
   - Depends on: db

2. **db**: MySQL 8.0
   - Port: 3307:3306
   - Database: olmos_talent
   - Volume: dbdata
   - Network: olmos-network

3. **phpmyadmin**: phpMyAdmin 5.2
   - Port: 8081:80
   - Depends on: db
   - Network: olmos-network

---

## Script Deploy

### deploy.sh
Deploy tradizionale su server:
1. Git pull
2. Reset permissions
3. (Opzionale) Cache clear
4. (Opzionale) Service restart

### deploy_docker.sh
Deploy Docker:
1. Git pull
2. Docker-compose rebuild
3. Image prune

---

## Sincronizzazione Finestre

### Meccanismo
Uso di `localStorage` events per sincronizzazione tra finestre:

**Chiavi localStorage**:
- `regia_command`: Comandi da dashboard a player/gobbo
  - `{ action, data, timestamp }`
- `screen_state`: Stato player → dashboard
  - `{ screenId, currentTime, duration, mediaName, playing, active, timestamp }`
- `gobbo_content`: Contenuto per gobbo
  - `{ talent, text, timestamp }`

### Flow
1. Dashboard invia comando via `localStorage.setItem()`
2. Player/Gobbo ascolta con `window.addEventListener('storage')`
3. Player esegue comando e reporta stato
4. Dashboard aggiorna UI in base allo stato

---

## Funzionalità Chiave

### 1. Gestione Scaletta
- CRUD talenti
- Drag & drop riordinamento
- Categorie (Canto, Ballo, Recitazione, Magia, Altro)

### 2. Multi-Screen
- 4 schermi configurabili
- Modalità indipendente o mirror
- Assegnazione media per schermo

### 3. Timeline Media
- Video, Audio, Foto
- Timestamp inizio/fine
- Fade in/out
- Transizioni (fade, crossfade, cut, dissolve)

### 4. Coda Esecuzione
- Sistema di coda automatico
- Stati: pending, playing, completed, skipped
- Auto-advance

### 5. Note Tecniche
- Materiale palco
- Gestione luci
- Note generali
- Pause (fixed time o manual)

### 6. Player Sincronizzato
- Proiettore con Web Audio API
- Gobbo per testo pubblico
- Sincronizzazione real-time

---

## Tecnologie Utilizzate

### Backend
- PHP 8.2
- MySQL 8.0
- PDO per database
- Apache HTTP Server

### Frontend
- HTML5
- JavaScript (ES6+)
- TailwindCSS (CDN)
- Web Audio API
- localStorage API

### DevOps
- Docker
- Docker Compose
- Git

### Licenza
- MIT License

---

## Note Tecniche

### Autoloading
Autoloading manuale via `public/imports.php` (non PSR-4)

### Session Management
Session PHP basic con placeholder expiration check

### Error Handling
Try-catch nei controller con JSON error response

### Security
- Prepared statements PDO
- Basic validation in ApiController
- No authentication system implementato

### Performance
- No caching system
- No lazy loading
- Direct SQL queries

---

## Possibili Miglioramenti

1. **Autoloading PSR-4**: Implementare Composer autoloader
2. **Authentication**: Sistema login/permessi
3. **Validation**: Libreria validation robusta
4. **Caching**: Redis o file cache
5. **Testing**: Unit/Integration tests
6. **API Documentation**: Swagger/OpenAPI
7. **Frontend Framework**: React/Vue per SPA
8. **WebSocket**: Sostituire localStorage con WebSocket per real-time
9. **File Upload**: Sistema upload media
10. **Backup**: Sistema backup automatico database
