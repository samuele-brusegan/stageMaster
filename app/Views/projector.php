<!DOCTYPE html>
<html lang="it" class="h-full bg-black">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Proiettore - Olmo's Got Talent</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { cursor: none; }
    </style>
</head>
<body class="h-full flex items-center justify-center overflow-hidden">
    <img id="main-image" class="hidden w-full h-full object-contain" alt="">
    <video id="main-video" class="hidden w-full h-full object-contain" playsinline></video>
    <audio id="main-audio" class="hidden"></audio>
    <div id="audio-label" class="hidden text-white text-5xl font-bold text-center px-12"></div>
    <div id="empty-label" class="text-slate-600 text-3xl font-mono text-center px-12">Nessun media assegnato</div>
    
    <!-- Fade Overlay -->
    <div id="fade-overlay" class="fixed inset-0 bg-black pointer-events-none opacity-100 transition-opacity duration-1000"></div>

    <!-- Click to Sync Overlay -->
    <div id="sync-overlay" class="fixed top-4 right-4 bg-slate-900/90 border border-slate-700 rounded-2xl p-3 z-50 transition-opacity duration-500">
        <button onclick="activateSync()" class="px-4 py-2 bg-blue-600 hover:bg-blue-500 text-white rounded-xl font-bold text-sm shadow-2xl transition-all active:scale-95">
            Attiva audio
        </button>
        <p class="mt-2 text-slate-400 text-xs">Solo se serve audio/autoplay.</p>
    </div>

    <script>
        const video = document.getElementById('main-video');
        const image = document.getElementById('main-image');
        const audio = document.getElementById('main-audio');
        const audioLabel = document.getElementById('audio-label');
        const emptyLabel = document.getElementById('empty-label');
        const overlay = document.getElementById('fade-overlay');
        const syncOverlay = document.getElementById('sync-overlay');
        
        // Get screen_id from URL
        const urlParams = new URLSearchParams(window.location.search);
        const screenId = urlParams.get('screen_id') || 1;
        
        console.log("Proiettore: Inizializzazione per screen_id:", screenId);
        let currentMediaPath = null;
        let currentMediaType = null;
        let slotTimeline = [];
        let activeTimelineMediaId = null;
        let timelineState = {
            slotId: null,
            startedAt: null,
            pausedAt: null,
            playing: false
        };

        function activateSync() {
            initAudio();
            syncOverlay.style.opacity = '0';
            setTimeout(() => syncOverlay.style.display = 'none', 500);
            console.log("Proiettore: Sincronizzazione attivata dall'utente.");
            
            // Prova a riprodurre un breve suono silenzioso per sbloccare l'audio
            const buffer = audioCtx.createBuffer(1, 1, 22050);
            const node = audioCtx.createBufferSource();
            node.buffer = buffer;
            node.connect(audioCtx.destination);
            node.start();

            if (currentMediaType === 'VIDEO' && video.src) {
                video.play().catch(err => console.warn("Proiettore: autoplay video non avviato:", err));
            } else if (currentMediaType === 'AUDIO' && audio.src) {
                audio.play().catch(err => console.warn("Proiettore: autoplay audio non avviato:", err));
            }
        }

        // Web Audio Setup for Fades
        let audioCtx;
        let gainNode;
        let source;

        function initAudio() {
            if (audioCtx) return;
            console.log("Proiettore: Inizializzazione Web Audio API...");
            audioCtx = new (window.AudioContext || window.webkitAudioContext)();
            gainNode = audioCtx.createGain();
            source = audioCtx.createMediaElementSource(video);
            source.connect(gainNode);
            gainNode.connect(audioCtx.destination);
        }

        function hideAllMedia() {
            image.classList.add('hidden');
            video.classList.add('hidden');
            audio.classList.add('hidden');
            audioLabel.classList.add('hidden');
            emptyLabel.classList.add('hidden');
        }

        function showEmpty(message = 'Nessun media assegnato') {
            hideAllMedia();
            emptyLabel.textContent = message;
            emptyLabel.classList.remove('hidden');
            overlay.style.opacity = '0';
            currentMediaPath = null;
            currentMediaType = null;
        }

        function showBlack() {
            hideAllMedia();
            overlay.style.opacity = '0';
            currentMediaPath = null;
            currentMediaType = null;
            activeTimelineMediaId = null;
        }

        function displayMedia(media, shouldPlay = false, mediaOffset = 0) {
            if (!media || !media.file_path) {
                showEmpty();
                return;
            }

            const mediaId = media.id ?? null;
            const path = media.file_path;
            const type = media.tipo_media || inferMediaType(path);
            if (String(activeTimelineMediaId) === String(mediaId) && currentMediaPath === path && currentMediaType === type) {
                if (type === 'VIDEO' && mediaOffset > 0 && Number.isFinite(video.duration)) {
                    const targetTime = Math.min(mediaOffset, video.duration || mediaOffset);
                    if (Math.abs(video.currentTime - targetTime) > 0.25) {
                        video.currentTime = targetTime;
                    }
                }
                if (shouldPlay) playCurrentMedia();
                return;
            }

            currentMediaPath = path;
            currentMediaType = type;
            activeTimelineMediaId = mediaId;
            hideAllMedia();
            overlay.style.opacity = '0';

            if (type === 'FOTO') {
                image.src = path;
                image.classList.remove('hidden');
            } else if (type === 'AUDIO') {
                audio.src = path;
                audio.load();
                audioLabel.textContent = path.split('/').pop();
                audioLabel.classList.remove('hidden');
                if (shouldPlay) playCurrentMedia();
            } else {
                video.src = path;
                video.load();
                video.classList.remove('hidden');
                if (shouldPlay) playCurrentMedia();
            }

            if (type === 'VIDEO' && mediaOffset > 0) {
                video.addEventListener('loadedmetadata', () => {
                    video.currentTime = Math.min(mediaOffset, video.duration || mediaOffset);
                }, { once: true });
            }

            console.log("Proiettore: Media visualizzato:", { path, type });
        }

        function inferMediaType(path) {
            const ext = path.split('.').pop().toLowerCase();
            if (['jpg', 'jpeg', 'png', 'gif', 'webp'].includes(ext)) return 'FOTO';
            if (['mp3', 'wav', 'ogg', 'flac'].includes(ext)) return 'AUDIO';
            return 'VIDEO';
        }

        function playCurrentMedia() {
            if (currentMediaType === 'VIDEO' && video.src) {
                video.play().catch(err => console.error("Proiettore: Errore play video:", err));
            } else if (currentMediaType === 'AUDIO' && audio.src) {
                audio.play().catch(err => console.error("Proiettore: Errore play audio:", err));
            }
        }

        async function loadScreenMedia() {
            try {
                const response = await fetch(`/api/screens/show?id=${screenId}`);
                const result = await response.json();
                if (result.status !== 'ok') {
                    showEmpty(result.message || 'Schermo non disponibile');
                    return;
                }
                const media = Array.isArray(result.data.media) ? result.data.media[0] : null;
                displayMedia(media, false);
            } catch (error) {
                console.error("Proiettore: Errore caricamento media schermo:", error);
                showEmpty('Errore caricamento media');
            }
        }

        function timelineSeconds(value) {
            if (!value) return 0;
            if (/^\d+$/.test(String(value))) return Number(value);
            const parts = String(value).split(':').map(Number);
            if (parts.length === 3) return parts[0] * 3600 + parts[1] * 60 + parts[2];
            if (parts.length === 2) return parts[0] * 60 + parts[1];
            return Number(value) || 0;
        }

        function timelineDuration(media) {
            const explicit = Number(media.durata_totale_sec);
            if (explicit > 0) return explicit;
            const start = timelineSeconds(media.timestamp_inizio);
            const end = timelineSeconds(media.timestamp_fine);
            return Math.max(1, end - start || 1);
        }

        async function loadSlotTimeline(slotId) {
            const response = await fetch(`/api/media/talento?talento_id=${slotId}`);
            const media = await response.json();
            slotTimeline = (Array.isArray(media) ? media : [])
                .filter(item => String(item.screen_id) === String(screenId))
                .sort((a, b) => timelineSeconds(a.timestamp_inizio) - timelineSeconds(b.timestamp_inizio));
            activeTimelineMediaId = null;
        }

        async function startSlot(slotId) {
            timelineState.slotId = slotId;
            timelineState.startedAt = Date.now();
            timelineState.pausedAt = null;
            timelineState.playing = true;
            await loadSlotTimeline(slotId);
            if (slotTimeline.length === 0) {
                showBlack();
                return;
            }
            tickTimeline();
        }

        function pauseSlot() {
            if (!timelineState.playing) return;
            timelineState.playing = false;
            timelineState.pausedAt = Date.now();
            if (currentMediaType === 'VIDEO') video.pause();
            if (currentMediaType === 'AUDIO') audio.pause();
        }

        function resumeSlot() {
            if (!timelineState.slotId || timelineState.playing) return;
            if (timelineState.pausedAt) {
                timelineState.startedAt += Date.now() - timelineState.pausedAt;
            }
            timelineState.pausedAt = null;
            timelineState.playing = true;
            tickTimeline();
        }

        function stopSlot() {
            timelineState = { slotId: null, startedAt: null, pausedAt: null, playing: false };
            slotTimeline = [];
            if (currentMediaType === 'VIDEO') {
                video.pause();
                video.currentTime = 0;
            }
            if (currentMediaType === 'AUDIO') {
                audio.pause();
                audio.currentTime = 0;
            }
            showBlack();
        }

        function tickTimeline() {
            if (!timelineState.playing || !timelineState.startedAt) return;
            const elapsed = (Date.now() - timelineState.startedAt) / 1000;
            const active = slotTimeline.find(media => {
                const start = timelineSeconds(media.timestamp_inizio);
                return elapsed >= start && elapsed < start + timelineDuration(media);
            });

            if (!active) {
                showBlack();
                return;
            }

            const start = timelineSeconds(active.timestamp_inizio);
            const offset = Math.max(0, elapsed - start);
            if (String(activeTimelineMediaId) !== String(active.id) || currentMediaPath !== active.file_path || currentMediaType !== (active.tipo_media || inferMediaType(active.file_path))) {
                displayMedia(active, true, offset);
            } else if (currentMediaType === 'VIDEO' && Math.abs(video.currentTime - offset) > 0.25) {
                video.currentTime = offset;
            }
            playCurrentMedia();
        }

        // Fades
        function fadeIn(duration = 1) {
            if (!audioCtx) initAudio();
            if (audioCtx.state === 'suspended') audioCtx.resume();

            overlay.style.opacity = '0';
            gainNode.gain.cancelScheduledValues(audioCtx.currentTime);
            gainNode.gain.setValueAtTime(0, audioCtx.currentTime);
            gainNode.gain.linearRampToValueAtTime(1, audioCtx.currentTime + duration);
            console.log(`Proiettore: Fade In (${duration}s)`);
        }

        function fadeOut(duration = 1) {
            if (!audioCtx) initAudio();
            if (audioCtx.state === 'suspended') audioCtx.resume();

            overlay.style.opacity = '1';
            gainNode.gain.cancelScheduledValues(audioCtx.currentTime);
            gainNode.gain.setValueAtTime(gainNode.gain.value, audioCtx.currentTime);
            gainNode.gain.linearRampToValueAtTime(0, audioCtx.currentTime + duration);
            console.log(`Proiettore: Fade Out (${duration}s)`);
        }

        // Synchronization
        window.addEventListener('storage', (e) => {
            if (e.key === 'regia_command') {
                const command = JSON.parse(e.newValue);
                // Only handle commands for this screen or global commands
                if (!command.screenId || command.screenId == screenId) {
                    console.log("Proiettore: Comando ricevuto:", command);
                    handleCommand(command);
                }
            } else if (e.key === `screen_sync_${screenId}`) {
                const state = JSON.parse(e.newValue);
                console.log("Proiettore: Sync ricevuto dalla dashboard:", state);
                syncFromDashboard(state);
            }
        });

        function handleCommand(cmd) {
            switch(cmd.action) {
                case 'play':
                    if (cmd.data.slot_id) {
                        startSlot(cmd.data.slot_id);
                        break;
                    }
                    if (timelineState.slotId) {
                        resumeSlot();
                        break;
                    }
                    console.log("Proiettore: Esecuzione PLAY per", cmd.data.file);
                    if (cmd.data.file) {
                        displayMedia({
                            file_path: cmd.data.file,
                            tipo_media: cmd.data.tipo_media || inferMediaType(cmd.data.file)
                        }, false);
                        if (cmd.data.start && currentMediaType === 'VIDEO') video.currentTime = cmd.data.start;
                    }
                    playCurrentMedia();
                    fadeIn(cmd.data.fadeIn || 1);
                    break;
                case 'pause':
                    pauseSlot();
                    video.pause();
                    audio.pause();
                    console.log("Proiettore: PAUSE");
                    break;
                case 'stop':
                    console.log("Proiettore: STOP");
                    stopSlot();
                    fadeOut(1);
                    setTimeout(() => {
                        video.pause();
                        video.currentTime = 0;
                    }, 1000);
                    break;
                case 'fade-in':
                    fadeIn();
                    break;
                case 'fade-out':
                    fadeOut();
                    break;
            }
        }

        function syncFromDashboard(state) {
            if (state.src) {
                displayMedia({
                    id: state.media_id || null,
                    file_path: state.src,
                    tipo_media: state.tipo_media || inferMediaType(state.src)
                }, false);
            }
            if (state.currentTime !== undefined && currentMediaType === 'VIDEO') {
                video.currentTime = state.currentTime;
            }
            if (state.playing) {
                playCurrentMedia();
            } else if (!state.playing && currentMediaType === 'VIDEO' && !video.paused) {
                video.pause();
            } else if (!state.playing && currentMediaType === 'AUDIO' && !audio.paused) {
                audio.pause();
            }
        }

        // Status Reporting
        setInterval(() => {
            const status = {
                screenId: screenId,
                src: currentMediaPath,
                tipo_media: currentMediaType,
                currentTime: currentMediaType === 'VIDEO' ? video.currentTime : audio.currentTime,
                duration: currentMediaType === 'VIDEO' ? video.duration : audio.duration,
                mediaName: currentMediaPath ? currentMediaPath.split('/').pop() : 'Nessuno',
                playing: currentMediaType === 'VIDEO' ? !video.paused : currentMediaType === 'AUDIO' ? !audio.paused : currentMediaType === 'FOTO',
                active: Boolean(currentMediaPath),
                timestamp: Date.now()
            };
            localStorage.setItem('screen_state', JSON.stringify(status));
        }, 500);

        // Auto-end monitoring
        video.addEventListener('timeupdate', () => {
            const end = video.dataset.endTime;
            if (end && video.currentTime >= end) {
                console.log("Proiettore: Fine video raggiunta (taglio).");
                video.pause();
                fadeOut();
            }
        });

        // Initialize with black screen
        overlay.style.opacity = '0';
        loadScreenMedia();
        setInterval(() => {
            if (timelineState.playing) {
                tickTimeline();
            } else if (!timelineState.slotId) {
                loadScreenMedia();
            }
        }, 250);
    </script>
</body>
</html>
