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

-- Tabella Media (Video/Audio) associati ai talenti
CREATE TABLE media_performance (
    id INT AUTO_INCREMENT PRIMARY KEY,
    talento_id INT,
    tipo_output ENUM('proiettore', 'gobbo') NOT NULL,
    file_path VARCHAR(255) NOT NULL,
    timestamp_inizio TIME DEFAULT '00:00:00',
    timestamp_fine TIME,
    fade_in_sec INT DEFAULT 0,
    fade_out_sec INT DEFAULT 0,
    ordine_esecuzione INT,
    FOREIGN KEY (talento_id) REFERENCES talenti(id) ON DELETE CASCADE
);

-- Stato del Player (Dashboard/Proiettore/Gobbo sync)
CREATE TABLE player_state (
    component ENUM('proiettore', 'gobbo') PRIMARY KEY,
    current_talento_id INT,
    current_media_id INT,
    status ENUM('playing', 'paused', 'stopped') DEFAULT 'stopped',
    last_update TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (current_talento_id) REFERENCES talenti(id) ON DELETE SET NULL,
    FOREIGN KEY (current_media_id) REFERENCES media_performance(id) ON DELETE SET NULL
);