-- Test Data for Olmo's Got Talent Manager
-- This script inserts sample data for testing the application

-- Insert test screens (talenti already exist)
INSERT INTO screens (nome, tipo) VALUES
('Schermo Principale', 'indipendente'),
('Schermo Laterale', 'indipendente'),
('Schermo Retro', 'indipendente'),
('Schermo Gobbo', 'indipendente');

-- Insert test media for each slot
INSERT INTO media_performance (talento_id, tipo_output, file_path, screen_id, tipo_media, durata_totale_sec) VALUES
(1, 'proiettore', '/media/video1.mp4', 1, 'VIDEO', 180),
(1, 'proiettore', '/media/audio1.mp3', 1, 'AUDIO', 240),
(2, 'proiettore', '/media/video2.mp4', 1, 'VIDEO', 150),
(2, 'gobbo', '/media/foto1.jpg', 2, 'FOTO', 10),
(3, 'proiettore', '/media/video3.mp4', 1, 'VIDEO', 300),
(4, 'proiettore', '/media/video4.mp4', 1, 'VIDEO', 200),
(5, 'proiettore', '/media/video5.mp4', 1, 'VIDEO', 210);

-- Insert test transitions
INSERT INTO transizioni (media_id, tipo_dissolvenza, durata_sec) VALUES
(1, 'fade_to_black', 1.0),
(2, 'crossfade', 0.5),
(3, 'fade_from_black', 1.5),
(4, 'cut', 0),
(5, 'fade_to_black', 1.0),
(6, 'crossfade', 0.8),
(7, 'fade_from_black', 1.2);

-- Insert test notes
INSERT INTO note_tecniche (talento_id, tipo, contenuto) VALUES
(1, 'materiale_palco', 'Posizionare il microfono al centro del palco'),
(1, 'luci', 'Inizia con luci calde, poi passa a spotlight'),
(2, 'materiale_palco', 'Tappeto già posizionato, controllare scarpe'),
(2, 'luci', 'Stroboscope attivo solo durante il ritornello'),
(3, 'generiche', 'Tempo di preparazione: 2 minuti'),
(3, 'pause', 'Pausa di 30 secondi dopo la scena 2'),
(4, 'materiale_palco', 'Tavolo magico con scomparto segreto'),
(4, 'luci', 'Luci blu durante il trucco delle carte'),
(5, 'materiale_palco', 'Chitarra già accordata, leggio con spartiti'),
(NULL, 'generiche', 'Nota generale per tutti: controllare audio prima dell''inizio');

-- Insert test queue items
INSERT INTO media_queue (talento_id, media_id, stato, ordine_coda) VALUES
(1, 1, 'pending', 1),
(1, 2, 'pending', 2),
(2, 3, 'pending', 3),
(2, 4, 'pending', 4),
(3, 5, 'pending', 5);
