

































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

-- =============================================================
-- v2 Overhaul: slot folders, segments editor, auth, messaging
-- =============================================================

-- Table: slot_folders - tree of folders containing talenti slots
CREATE TABLE IF NOT EXISTS slot_folders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    parent_id INT NULL,
    nome VARCHAR(120) NOT NULL,
    ordine INT NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (parent_id) REFERENCES slot_folders(id) ON DELETE CASCADE
);

SET @sql = IF((SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'slot_folders' AND INDEX_NAME = 'idx_folders_parent') = 0, 'CREATE INDEX idx_folders_parent ON slot_folders(parent_id, ordine)', 'DO 0');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Add folder_id to talenti so each slot can live in a folder.
SET @sql = IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'talenti' AND COLUMN_NAME = 'folder_id') = 0,
    'ALTER TABLE talenti ADD COLUMN folder_id INT NULL AFTER ordine_scaletta',
    'DO 0'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql = IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'talenti' AND COLUMN_NAME = 'folder_id' AND REFERENCED_TABLE_NAME = 'slot_folders') = 0,
    'ALTER TABLE talenti ADD CONSTRAINT fk_talenti_folder FOREIGN KEY (folder_id) REFERENCES slot_folders(id) ON DELETE SET NULL',
    'DO 0'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql = IF((SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'talenti' AND INDEX_NAME = 'idx_talenti_folder') = 0, 'CREATE INDEX idx_talenti_folder ON talenti(folder_id)', 'DO 0');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Table: media_segments - editor cut/sew metadata (no re-encode)
CREATE TABLE IF NOT EXISTS media_segments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    media_performance_id INT NOT NULL,
    start_sec DECIMAL(8,3) NOT NULL DEFAULT 0,
    end_sec DECIMAL(8,3) NOT NULL,
    ordine INT NOT NULL DEFAULT 0,
    label VARCHAR(120) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (media_performance_id) REFERENCES media_performance(id) ON DELETE CASCADE
);

SET @sql = IF((SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'media_segments' AND INDEX_NAME = 'idx_segments_media_order') = 0, 'CREATE INDEX idx_segments_media_order ON media_segments(media_performance_id, ordine)', 'DO 0');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Table: users - auth for the companion PWA and admin
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(60) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    display_name VARCHAR(120) NOT NULL,
    role ENUM('admin','scene_manager','stage_hand','lights') NOT NULL DEFAULT 'stage_hand',
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS auth_tokens (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    token CHAR(64) NOT NULL UNIQUE,
    user_agent VARCHAR(255) NULL,
    expires_at DATETIME NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_seen_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

SET @sql = IF((SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'auth_tokens' AND INDEX_NAME = 'idx_auth_user') = 0, 'CREATE INDEX idx_auth_user ON auth_tokens(user_id)', 'DO 0');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Table: scripts - PDF copioni uploaded by users
CREATE TABLE IF NOT EXISTS scripts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(180) NOT NULL,
    file_path VARCHAR(255) NOT NULL,
    page_count INT NULL,
    uploaded_by INT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (uploaded_by) REFERENCES users(id) ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS script_annotations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    script_id INT NOT NULL,
    page INT NOT NULL,
    pos_x DECIMAL(6,3) NOT NULL,
    pos_y DECIMAL(6,3) NOT NULL,
    width DECIMAL(6,3) NOT NULL DEFAULT 0,
    height DECIMAL(6,3) NOT NULL DEFAULT 0,
    color VARCHAR(20) NULL,
    content TEXT NULL,
    slot_id INT NULL,
    created_by INT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (script_id) REFERENCES scripts(id) ON DELETE CASCADE,
    FOREIGN KEY (slot_id) REFERENCES talenti(id) ON DELETE SET NULL,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
);

SET @sql = IF((SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'script_annotations' AND INDEX_NAME = 'idx_annot_script_page') = 0, 'CREATE INDEX idx_annot_script_page ON script_annotations(script_id, page)', 'DO 0');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Tables: messaging
CREATE TABLE IF NOT EXISTS messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    from_user_id INT NOT NULL,
    to_user_id INT NULL,
    role_target ENUM('admin','scene_manager','stage_hand','lights','all') NULL,
    content TEXT NOT NULL,
    kind ENUM('text','quick','system') NOT NULL DEFAULT 'text',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    read_at DATETIME NULL,
    FOREIGN KEY (from_user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (to_user_id) REFERENCES users(id) ON DELETE CASCADE
);

SET @sql = IF((SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'messages' AND INDEX_NAME = 'idx_msg_to_user') = 0, 'CREATE INDEX idx_msg_to_user ON messages(to_user_id, created_at)', 'DO 0');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql = IF((SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'messages' AND INDEX_NAME = 'idx_msg_role') = 0, 'CREATE INDEX idx_msg_role ON messages(role_target, created_at)', 'DO 0');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

CREATE TABLE IF NOT EXISTS message_templates (
    id INT AUTO_INCREMENT PRIMARY KEY,
    owner_id INT NULL,
    scope ENUM('private','shared') NOT NULL DEFAULT 'private',
    label VARCHAR(120) NOT NULL,
    content TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (owner_id) REFERENCES users(id) ON DELETE CASCADE
);
