# Database Schema

This document describes the database schema for the Olmo's Got Talent Manager.

## Tables

### `talenti`
Stores information about the talents and their performances.

| Column | Type | Description |
|---|---|---|
| `id` | INT | Primary Key, Auto-increment |
| `nome` | VARCHAR(100) | Name of the talent |
| `categoria` | VARCHAR(50) | Category of the performance |
| `materiale_palco` | TEXT | List of stage materials needed |
| `note_luci` | TEXT | Lighting notes |
| `ordine_scaletta` | INT | Order in the ceremony (Unique) |

### `media_performance`
Stores media assets (audio/video) associated withEach talent.

| Column | Type | Description |
|---|---|---|
| `id` | INT | Primary Key, Auto-increment |
| `talento_id` | INT | Foreign Key to `talenti(id)` |
| `tipo_output` | ENUM('proiettore', 'gobbo') | Where the media is displayed |
| `file_path` | VARCHAR(255) | Path to the media file |
| `timestamp_inizio` | TIME | Start time offset |
| `timestamp_fine` | TIME | End time offset |
| `fade_in_sec` | INT | Fade in duration in seconds |
| `fade_out_sec` | INT | Fade out duration in seconds |
| `ordine_esecuzione` | INT | Order of execution for the same talent |

### `player_state`
Tracks the current state of the projector and teleprompter (gobbo).

| Column | Type | Description |
|---|---|---|
| `component` | ENUM('proiettore', 'gobbo') | Component identifier (Primary Key) |
| `current_talento_id` | INT | Current talent being played/viewed |
| `current_media_id` | INT | Current media active |
| `status` | ENUM('playing', 'paused', 'stopped') | Current playback status |
| `last_update` | TIMESTAMP | Last time the state was updated |

## [WORK_LOG] - Notes and Optimizations

- **Optimization for Sync Queries**:
    - The `ordine_scaletta` is indexed and unique, which is good for fast retrieval of the lineup.
    - If the number of talents grows significantly, we might consider indexing `talento_id` explicitly in `media_performance` (it is already indexed by being a Foreign Key).
    - For real-time synchronization, we should consider a "last_modified" timestamp on both tables to poll only for changes.
- **Foreign Keys**: `ON DELETE CASCADE` is implemented to ensure data integrity when a talent is removed.
