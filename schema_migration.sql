

































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
INSERT INTO screens (nome, tipo)
SELECT 'Screen 1', 'indipendente'
WHERE NOT EXISTS (SELECT 1 FROM screens WHERE nome = 'Screen 1');

INSERT INTO screens (nome, tipo)
SELECT 'Screen 2', 'indipendente'
WHERE NOT EXISTS (SELECT 1 FROM screens WHERE nome = 'Screen 2');

INSERT INTO screens (nome, tipo)
SELECT 'Screen 3', 'indipendente'
WHERE NOT EXISTS (SELECT 1 FROM screens WHERE nome = 'Screen 3');

INSERT INTO screens (nome, tipo)
SELECT 'Screen 4', 'indipendente'
WHERE NOT EXISTS (SELECT 1 FROM screens WHERE nome = 'Screen 4');

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

-- Table: media - Libreria file disponibili
CREATE TABLE IF NOT EXISTS media (
    id INT AUTO_INCREMENT PRIMARY KEY,
    file_name VARCHAR(255) NOT NULL,
    file_path VARCHAR(255) NOT NULL UNIQUE,
    file_type ENUM('VIDEO', 'AUDIO', 'FOTO') NOT NULL,
    file_size BIGINT NULL,
    duration_sec INT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
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
SET @sql = IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'media_performance' AND COLUMN_NAME = 'screen_id') = 0,
    'ALTER TABLE media_performance ADD COLUMN screen_id INT NULL AFTER file_path',
    'DO 0'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql = IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'media_performance' AND COLUMN_NAME = 'tipo_media') = 0,
    'ALTER TABLE media_performance ADD COLUMN tipo_media ENUM(''VIDEO'', ''AUDIO'', ''FOTO'') DEFAULT ''VIDEO'' AFTER file_path',
    'DO 0'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql = IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'media_performance' AND COLUMN_NAME = 'durata_totale_sec') = 0,
    'ALTER TABLE media_performance ADD COLUMN durata_totale_sec INT NULL AFTER timestamp_fine',
    'DO 0'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql = IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'media_performance' AND COLUMN_NAME = 'friendly_name') = 0,
    'ALTER TABLE media_performance ADD COLUMN friendly_name VARCHAR(100) NULL AFTER file_path',
    'DO 0'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql = IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'media_performance' AND COLUMN_NAME = 'screen_id' AND REFERENCED_TABLE_NAME = 'screens') = 0,
    'ALTER TABLE media_performance ADD CONSTRAINT fk_media_performance_screen FOREIGN KEY (screen_id) REFERENCES screens(id) ON DELETE SET NULL',
    'DO 0'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Create indexes for performance
SET @sql = IF((SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'media_queue' AND INDEX_NAME = 'idx_queue_ordine') = 0, 'CREATE INDEX idx_queue_ordine ON media_queue(ordine_coda)', 'DO 0');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql = IF((SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'media_queue' AND INDEX_NAME = 'idx_queue_stato') = 0, 'CREATE INDEX idx_queue_stato ON media_queue(stato)', 'DO 0');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql = IF((SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'note_tecniche' AND INDEX_NAME = 'idx_note_tipo') = 0, 'CREATE INDEX idx_note_tipo ON note_tecniche(tipo)', 'DO 0');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql = IF((SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'note_tecniche' AND INDEX_NAME = 'idx_note_talento') = 0, 'CREATE INDEX idx_note_talento ON note_tecniche(talento_id)', 'DO 0');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql = IF((SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'media_performance' AND INDEX_NAME = 'idx_media_screen') = 0, 'CREATE INDEX idx_media_screen ON media_performance(screen_id)', 'DO 0');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;
