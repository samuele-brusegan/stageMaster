

































-- Migration Script for Olmo's Got Talent Manager Redesign
-- This script extends the existing schema with new features

USE olmos_talent;

-- Table: screens - Configurazioni schermo
CREATE TABLE IF NOT EXISTS screens (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(50) NOT NULL,
    tipo ENUM('indipendente', 'mirror') DEFAULT 'indipendente',
    screen_riferimento_id INT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (screen_riferimento_id) REFERENCES screens(id) ON DELETE SET NULL
);

-- Insert default screens
INSERT IGNORE INTO screens (nome, tipo) VALUES 
('Screen 1', 'indipendente'),
('Screen 2', 'indipendente'),
('Screen 3', 'indipendente'),
('Screen 4', 'indipendente');

-- Table: media_queue - Sistema di coda
CREATE TABLE IF NOT EXISTS media_queue (
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

-- Table: note_tecniche - Note generali separate
CREATE TABLE IF NOT EXISTS note_tecniche (
    id INT AUTO_INCREMENT PRIMARY KEY,
    talento_id INT NULL,
    tipo ENUM('materiale_palco', 'luci', 'generiche', 'pause') NOT NULL,
    contenuto TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (talento_id) REFERENCES talenti(id) ON DELETE CASCADE
);

-- Table: transizioni - Proprietà transizione dettagliate
CREATE TABLE IF NOT EXISTS transizioni (
    id INT AUTO_INCREMENT PRIMARY KEY,
    media_id INT NOT NULL,
    tipo_dissolvenza ENUM('fade_to_black', 'fade_from_black', 'crossfade', 'cut', 'dissolve') DEFAULT 'fade_to_black',
    durata_sec DECIMAL(5,2) DEFAULT 0.00,
    offset_prima_sec DECIMAL(5,2) DEFAULT 0.00,
    offset_dopo_sec DECIMAL(5,2) DEFAULT 0.00,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (media_id) REFERENCES media_performance(id) ON DELETE CASCADE
);

-- Modify existing media_performance table
ALTER TABLE media_performance 
ADD COLUMN screen_id INT NULL AFTER tipo_output,
ADD COLUMN tipo_media ENUM('VIDEO', 'AUDIO', 'FOTO') DEFAULT 'VIDEO' AFTER file_path,
ADD COLUMN durata_totale_sec INT NULL AFTER timestamp_fine,
ADD FOREIGN KEY (screen_id) REFERENCES screens(id) ON DELETE SET NULL;

-- Create indexes for performance (MySQL doesn't support IF NOT EXISTS for indexes)
CREATE INDEX idx_queue_ordine ON media_queue(ordine_coda);
CREATE INDEX idx_queue_stato ON media_queue(stato);
CREATE INDEX idx_note_tipo ON note_tecniche(tipo);
CREATE INDEX idx_note_talento ON note_tecniche(talento_id);
CREATE INDEX idx_media_screen ON media_performance(screen_id);
