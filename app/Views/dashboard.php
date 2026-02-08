<!DOCTYPE html>
<html lang="it" class="h-full bg-slate-900 border-slate-800">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Regia - Olmo's Got Talent</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Outfit:wght@600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        h1, h2, h3 { font-family: 'Outfit', sans-serif; }
        .glass { background: rgba(30, 41, 59, 0.7); backdrop-filter: blur(12px); border: 1px solid rgba(255, 255, 255, 0.1); }
        .gradient-bg { background: radial-gradient(circle at top right, #3b82f6, transparent), radial-gradient(circle at bottom left, #8b5cf6, transparent); }
    </style>
</head>
<body class="h-full text-slate-200 overflow-hidden">
    <div class="flex h-full">
        <!-- Sidebar - Scaletta -->
        <aside class="w-80 glass border-r border-slate-700/50 flex flex-col">
            <div class="p-6 border-b border-slate-700/50">
                <h2 class="text-2xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-blue-400 to-purple-400">Scaletta</h2>
            </div>
            <div id="setlist" class="flex-1 overflow-y-auto p-4 space-y-3">
                <!-- Mock Data - Will be loaded via JS -->
                <div onclick="sendCommand('play', {file: 'https://www.w3schools.com/html/mov_bbb.mp4', talent: 'Mario Rossi'}); sendToGobbo('Mario Rossi', 'Sapore di Sale');" class="p-4 rounded-xl bg-slate-800/50 border border-slate-700 hover:border-blue-400 transition-all cursor-pointer group">
                    <div class="flex justify-between items-center">
                        <span class="text-xs font-mono text-slate-400">#1</span>
                        <span class="px-2 py-0.5 rounded text-[10px] bg-blue-500/20 text-blue-400 border border-blue-500/30">CANTO</span>
                    </div>
                    <h3 class="font-bold text-lg group-hover:text-blue-400 transition-colors">Mario Rossi</h3>
                    <p class="text-sm text-slate-400 truncate">Sapore di Sale.mp4</p>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 flex flex-col relative overflow-hidden">
            <div class="absolute inset-0 gradient-bg opacity-10 pointer-events-none"></div>
            
            <!-- Header -->
            <header class="p-6 flex justify-between items-center glass border-b border-slate-700/50">
                <div>
                    <h1 class="text-3xl font-bold">Direzione Regia</h1>
                    <p class="text-slate-400 text-sm">Controlla la trasmissione e i contenuti live</p>
                </div>
                <div class="flex gap-4">
                    <a href="/admin" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 rounded-lg font-semibold transition-all border border-slate-700 flex items-center gap-2">
                        <span>Amministrazione</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    </a>
                    <button onclick="openWindow('projector')" class="px-4 py-2 bg-blue-600 hover:bg-blue-500 rounded-lg font-semibold transition-all shadow-lg shadow-blue-900/20 flex items-center gap-2">
                        <span>Proiettore</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                    </button>
                    <button onclick="openWindow('gobbo')" class="px-4 py-2 bg-purple-600 hover:bg-purple-500 rounded-lg font-semibold transition-all shadow-lg shadow-purple-900/20 flex items-center gap-2">
                        <span>Gobbo</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                    </button>
                </div>
            </header>

            <!-- Control Areas -->
            <div class="flex-1 grid grid-cols-2 gap-6 p-6 overflow-hidden">
                <!-- Preview / Monitor -->
                <div class="flex flex-col gap-6">
                    <div class="glass rounded-2xl overflow-hidden flex-1 relative flex flex-col">
                        <div class="p-4 border-b border-slate-700/50 flex justify-between items-center">
                            <span class="font-bold uppercase tracking-wider text-xs text-blue-400">Monitor Proiettore</span>
                            <div class="flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></span>
                                <span class="text-[10px] text-slate-400 uppercase">Live</span>
                            </div>
                        </div>
                        <div class="flex-1 bg-black flex items-center justify-center relative group">
                            <video id="preview-video" class="w-full h-full object-contain pointer-events-none opacity-50" muted></video>
                            <div class="absolute inset-0 flex items-center justify-center">
                                <span class="text-slate-600 font-mono italic">Preview Off</span>
                            </div>
                        </div>
                        <div class="p-4 bg-slate-800/80 border-t border-slate-700/50">
                            <div class="flex justify-between mb-2">
                                <span id="current-media-name" class="font-bold">Nessun media</span>
                                <span id="current-time-display" class="font-mono text-sm">00:00 / 00:00</span>
                            </div>
                            <div class="h-1.5 bg-slate-700 rounded-full overflow-hidden">
                                <div id="progress-bar" class="h-full bg-blue-500 transition-all duration-100" style="width: 0%"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Controls -->
                <div class="flex flex-col gap-6">
                    <div class="glass rounded-2xl p-6 flex flex-col gap-8">
                        <div>
                            <h3 class="text-lg font-bold mb-4 flex items-center gap-2">
                                <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                Controlli di Riproduzione
                            </h3>
                            <div class="flex gap-4">
                                <button onclick="sendCommand('play')" class="p-6 bg-green-600 hover:bg-green-500 rounded-2xl flex-1 transition-all flex flex-col items-center gap-2 group">
                                    <svg class="w-10 h-10 group-hover:scale-110 transition-transform" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"></path></svg>
                                    <span class="font-bold uppercase tracking-tighter">PLAY</span>
                                </button>
                                <button onclick="sendCommand('pause')" class="p-6 bg-yellow-600 hover:bg-yellow-500 rounded-2xl flex-1 transition-all flex flex-col items-center gap-2 group">
                                    <svg class="w-10 h-10 group-hover:scale-110 transition-transform" fill="currentColor" viewBox="0 0 24 24"><path d="M6 19h4V5H6v14zm8-14v14h4V5h-4z"></path></svg>
                                    <span class="font-bold uppercase tracking-tighter">PAUSE</span>
                                </button>
                                <button onclick="sendCommand('stop')" class="p-6 bg-red-600 hover:bg-red-500 rounded-2xl flex-1 transition-all flex flex-col items-center gap-2 group">
                                    <svg class="w-10 h-10 group-hover:scale-110 transition-transform" fill="currentColor" viewBox="0 0 24 24"><path d="M6 6h12v12H6z"></path></svg>
                                    <span class="font-bold uppercase tracking-tighter">STOP</span>
                                </button>
                            </div>
                        </div>

                        <div>
                            <h3 class="text-lg font-bold mb-4">Effetti Transizione</h3>
                            <div class="grid grid-cols-2 gap-4">
                                <button onclick="sendCommand('fade-in')" class="p-4 border border-slate-700 hover:border-blue-500 rounded-xl bg-slate-800/50 transition-all flex items-center justify-center gap-2">
                                    <span>Fade In</span>
                                </button>
                                <button onclick="sendCommand('fade-out')" class="p-4 border border-slate-700 hover:border-blue-500 rounded-xl bg-slate-800/50 transition-all flex items-center justify-center gap-2">
                                    <span>Fade Out</span>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Status Console -->
                    <div class="glass rounded-2xl p-6 flex-1 flex flex-col">
                        <h3 class="text-xs font-bold uppercase text-slate-500 mb-2">Logs di Sistema</h3>
                        <div id="logs" class="flex-1 font-mono text-[10px] text-slate-400 bg-black/30 p-4 rounded-xl overflow-y-auto space-y-1">
                            <div>[13:37:00] Dashboard inizializzata.</div>
                            <div>[13:37:05] Sincronizzazione localStorage attiva.</div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
        // Sincronizzazione Finestre (localStorage)
        async function fetchSetlist() {
            try {
                const response = await fetch('/api/talenti');
                const result = await response.json();
                if (result.status === 'ok') {
                    renderSetlist(result.data);
                }
            } catch (error) {
                console.error("Dashboard: Errore nel recupero scaletta:", error);
            }
        }

        function renderSetlist(talenti) {
            const container = document.getElementById('setlist');
            container.innerHTML = '';
            talenti.forEach((talent, index) => {
                const item = document.createElement('div');
                item.onclick = () => {
                    sendCommand('play', {file: 'https://www.w3schools.com/html/mov_bbb.mp4', talent: talent.nome}); 
                    sendToGobbo(talent.nome, talent.categoria); 
                };
                item.className = 'p-4 rounded-xl bg-slate-800/50 border border-slate-700 hover:border-blue-400 transition-all cursor-pointer group';
                item.innerHTML = `
                    <div class="flex justify-between items-center">
                        <span class="text-xs font-mono text-slate-400">#${index + 1}</span>
                        <span class="px-2 py-0.5 rounded text-[10px] bg-blue-500/20 text-blue-400 border border-blue-500/30">${talent.categoria}</span>
                    </div>
                    <h3 class="font-bold text-lg group-hover:text-blue-400 transition-colors">${talent.nome}</h3>
                    <p class="text-sm text-slate-400 truncate">${talent.note_luci || 'Nessuna nota'}</p>
                `;
                container.appendChild(item);
            });
        }

        // Caricamento iniziale
        fetchSetlist();

        function sendCommand(action, data = {}) {
            const command = {
                action,
                data,
                timestamp: Date.now()
            };
            localStorage.setItem('regia_command', JSON.stringify(command));
            log(`Comando inviato: ${action}`);
        }

        function openWindow(type) {
            window.open(type, '_blank', 'width=1280,height=720');
            log(`Finestra ${type} aperta.`);
        }

        function sendToGobbo(talent, text) {
            localStorage.setItem('gobbo_content', JSON.stringify({ talent, text, timestamp: Date.now() }));
            log(`Gobbo aggiornato: ${talent}`);
        }

        function log(msg) {
            console.log(`Dashboard: ${msg}`);
            const time = new Date().toLocaleTimeString();
            const logEntry = document.createElement('div');
            logEntry.innerHTML = `<span class="text-blue-500">[${time}]</span> ${msg}`;
            const container = document.getElementById('logs');
            container.appendChild(logEntry);
            container.scrollTop = container.scrollHeight;
        }

        // Monitoring dello stato delle altre finestre
        window.addEventListener('storage', (e) => {
            if (e.key === 'projector_status') {
                const status = JSON.parse(e.newValue);
                updateUI(status);
            }
        });

        function updateUI(status) {
            document.getElementById('current-media-name').textContent = status.mediaName || 'Nessuno';
            document.getElementById('current-time-display').textContent = `${formatTime(status.currentTime)} / ${formatTime(status.duration)}`;
            const progress = (status.currentTime / status.duration) * 100 || 0;
            document.getElementById('progress-bar').style.width = `${progress}%`;
        }

        function formatTime(seconds) {
            if (!seconds) return "00:00";
            const m = Math.floor(seconds / 60);
            const s = Math.floor(seconds % 60);
            return `${m.toString().padStart(2, '0')}:${s.toString().padStart(2, '0')}`;
        }
    </script>
</body>
</html>
