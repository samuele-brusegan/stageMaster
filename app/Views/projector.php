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
    <video id="main-video" class="w-full h-full object-contain" playsinline></video>
    
    <!-- Fade Overlay -->
    <div id="fade-overlay" class="fixed inset-0 bg-black pointer-events-none opacity-100 transition-opacity duration-1000"></div>

    <!-- Click to Sync Overlay -->
    <div id="sync-overlay" class="fixed inset-0 bg-slate-900 flex flex-col items-center justify-center z-50 transition-opacity duration-500">
        <button onclick="activateSync()" class="px-8 py-4 bg-blue-600 hover:bg-blue-500 text-white rounded-2xl font-bold text-2xl shadow-2xl transition-all hover:scale-105 active:scale-95">
            CLICCA PER ATTIVARE SINCRONIZZAZIONE
        </button>
        <p class="mt-4 text-slate-400 text-sm">Necessario per permettere la riproduzione automatica e l'audio.</p>
    </div>

    <script>
        const video = document.getElementById('main-video');
        const overlay = document.getElementById('fade-overlay');
        const syncOverlay = document.getElementById('sync-overlay');
        
        console.log("Proiettore: Inizializzazione...");

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
                console.log("Proiettore: Comando ricevuto:", command);
                handleCommand(command);
            }
        });

        function handleCommand(cmd) {
            switch(cmd.action) {
                case 'play':
                    console.log("Proiettore: Esecuzione PLAY per", cmd.data.file);
                    if (cmd.data.file) {
                        video.src = cmd.data.file;
                        video.load();
                        if (cmd.data.start) video.currentTime = cmd.data.start;
                    }
                    video.play().then(() => {
                        console.log("Proiettore: Play avviato con successo.");
                        fadeIn(cmd.data.fadeIn || 1);
                    }).catch(err => {
                        console.error("Proiettore: Errore durante il play:", err);
                    });
                    break;
                case 'pause':
                    video.pause();
                    console.log("Proiettore: PAUSE");
                    break;
                case 'stop':
                    console.log("Proiettore: STOP");
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

        // Status Reporting
        setInterval(() => {
            const status = {
                currentTime: video.currentTime,
                duration: video.duration,
                mediaName: video.src ? video.src.split('/').pop() : 'Nessuno',
                playing: !video.paused,
                timestamp: Date.now()
            };
            localStorage.setItem('projector_status', JSON.stringify(status));
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
        overlay.style.opacity = '1';
    </script>
</body>
</html>
