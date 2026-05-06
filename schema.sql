-- Creazione Database
CREATE DATABASE IF NOT EXISTS olmos_talent;
USE olmos_talent;

-- Tabella Talenti e Performance
CREATE TABLE talenti (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    categoria VARCHAR(50),
    materiale_palco TEXT,
    note_luci TEXT,
    ordine_scaletta INT UNIQUE
);

-- Tabella Screens - Configurazioni schermo
CREATE TABLE screens (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(50) NOT NULL,
    tipo ENUM('indipendente', 'mirror') DEFAULT 'indipendente',
    screen_riferimento_id INT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (screen_riferimento_id) REFERENCES screens(id) ON DELETE SET NULL
);

-- Tabella Media (Video/Audio) associati ai talenti
CREATE TABLE media_performance (
    id INT AUTO_INCREMENT PRIMARY KEY,
    talento_id INT,
    tipo_output ENUM('proiettore', 'gobbo') NOT NULL,
    file_path VARCHAR(255) NOT NULL,
    screen_id INT NULL,
    tipo_media ENUM('VIDEO', 'AUDIO', 'FOTO') DEFAULT 'VIDEO',
    friendly_name VARCHAR(100) NULL,
    timestamp_inizio TIME DEFAULT '00:00:00',
    timestamp_fine TIME,
    fade_in_sec INT DEFAULT 0,
    fade_out_sec INT DEFAULT 0,
    ordine_esecuzione INT,
    durata_totale_sec INT NULL,
    FOREIGN KEY (talento_id) REFERENCES talenti(id) ON DELETE CASCADE,
    FOREIGN KEY (screen_id) REFERENCES screens(id) ON DELETE SET NULL
);

-- Tabella Stato Player (Proiettore/Gobbo)
CREATE TABLE player_state (
    component ENUM('proiettore', 'gobbo') PRIMARY KEY,
    current_talento_id INT,
    current_media_id INT,
    status ENUM('playing', 'paused', 'stopped') DEFAULT 'stopped',
    last_update TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (current_talento_id) REFERENCES talenti(id) ON DELETE SET NULL,
    FOREIGN KEY (current_media_id) REFERENCES media_performance(id) ON DELETE SET NULL
);

-- Tabella Media Queue - Sistema di coda
CREATE TABLE media_queue (
    id INT AUTO_INCREMENT PRIMARY KEY,
    talento_id INT,
    media_id INT,
    ordine_coda INT NOT NULL,
    stato ENUM('pending', 'playing', 'completed', 'skipped') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (talento_id) REFERENCES talenti(id) ON DELETE CASCADE,
    FOREIGN KEY (media_id) REFERENCES media_performance(id) ON DELETE CASCADE
);

-- Tabella Media - Libreria file disponibili
CREATE TABLE media (
    id INT AUTO_INCREMENT PRIMARY KEY,
    file_name VARCHAR(255) NOT NULL,
    file_path VARCHAR(255) NOT NULL UNIQUE,
    file_type ENUM('VIDEO', 'AUDIO', 'FOTO') NOT NULL,
    file_size BIGINT NULL,
    duration_sec INT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabella note_tecniche - Note generali separate
CREATE TABLE note_tecniche (
    id INT AUTO_INCREMENT PRIMARY KEY,
    talento_id INT NULL,
    tipo ENUM('materiale_palco', 'luci', 'generiche', 'pause') NOT NULL,
    contenuto TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (talento_id) REFERENCES talenti(id) ON DELETE CASCADE
);

-- Tabella transizioni - Proprietà transizione dettagliate
CREATE TABLE transizioni (
    id INT AUTO_INCREMENT PRIMARY KEY,
    media_id INT NOT NULL,
    tipo_dissolvenza ENUM('fade_to_black', 'fade_from_black', 'crossfade', 'cut', 'dissolve') DEFAULT 'fade_to_black',
    durata_sec DECIMAL(5,2) DEFAULT 0.00,
    offset_prima_sec DECIMAL(5,2) DEFAULT 0.00,
    offset_dopo_sec DECIMAL(5,2) DEFAULT 0.00,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (media_id) REFERENCES media_performance(id) ON DELETE CASCADE
);
