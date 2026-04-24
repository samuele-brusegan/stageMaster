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
        .timeline-track { background: linear-gradient(90deg, #1e293b 0%, #334155 100%); }
        .media-slot { transition: all 0.2s ease; }
        .media-slot:hover { transform: scale(1.02); }
        .screen-active { border-color: #3b82f6; box-shadow: 0 0 20px rgba(59, 130, 246, 0.3); }
        #screens-grid { min-height: 400px; }

        /* Responsive sidebar toggle */
        @media (max-width: 1280px) {
            .sidebar-left { transform: translateX(-100%); position: absolute; z-index: 40; transition: transform 0.3s ease; }
            .sidebar-left.open { transform: translateX(0); }
            .sidebar-right { transform: translateX(100%); position: absolute; right: 0; z-index: 40; transition: transform 0.3s ease; }
            .sidebar-right.open { transform: translateX(0); }
        }

        @media (min-width: 1281px) {
            .sidebar-left { position: relative; }
            .sidebar-right { position: relative; }
        }
    </style>
</head>
<body class="h-full text-slate-200 overflow-hidden">
    <div class="flex h-full">
        <!-- Left Column - Slot & Coda (250px) -->
        <aside id="sidebar-left" class="sidebar-left w-[280px] glass border-r border-slate-700/50 flex flex-col h-full">
            <div class="p-4 border-b border-slate-700/50">
                <h2 class="text-xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-blue-400 to-purple-400">Slot</h2>
            </div>
            
            <!-- Slot List -->
            <div id="setlist" class="flex-1 overflow-y-auto p-3 space-y-2">
                <!-- Loaded via JS -->
            </div>

            <!-- Queue Section -->
            <div class="border-t border-slate-700/50">
                <div class="p-3 border-b border-slate-700/50 flex justify-between items-center">
                    <h3 class="text-sm font-bold text-purple-400">Coda Esecuzione</h3>
                    <span id="queue-count" class="text-xs bg-purple-500/20 text-purple-400 px-2 py-0.5 rounded">0</span>
                </div>
                <div id="queue-list" class="flex-1 overflow-y-auto p-3 space-y-2 max-h-48">
                    <!-- Queue items loaded via JS -->
                </div>
            </div>
        </aside>

        <!-- Center Column - Timeline & Schermi (flex-1) -->
        <main class="flex-1 flex flex-col relative overflow-hidden">
            <div class="absolute inset-0 gradient-bg opacity-10 pointer-events-none"></div>
            
            <!-- Header -->
            <div class="relative z-10 p-4 border-b border-slate-700/50 glass">
                <div class="flex justify-between items-center">
                    <div class="flex items-center gap-3">
                        <button onclick="toggleSidebar('left')" class="lg:hidden p-2 hover:bg-slate-700 rounded-lg text-slate-400 hover:text-white transition-colors" title="Menu Slot">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                            </svg>
                        </button>
                        <div>
                            <h1 class="text-2xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-blue-400 to-purple-400">Regia</h1>
                            <p class="text-xs text-slate-500">Controllo centralizzato</p>
                        </div>
                    </div>
                    <div class="flex gap-3">
                        <a href="/admin" class="px-3 py-1.5 bg-slate-800 hover:bg-slate-700 rounded-lg font-semibold transition-all border border-slate-700 flex items-center gap-2 text-sm">
                            <span>Admin</span>
                        </a>
                        <button onclick="createScreen('projector')" class="px-3 py-1.5 bg-blue-600 hover:bg-blue-500 rounded-lg font-semibold transition-all text-sm">Crea Schermo</button>
                        <button onclick="toggleSidebar('right')" class="lg:hidden p-2 hover:bg-slate-700 rounded-lg text-slate-400 hover:text-white transition-colors" title="Controlli">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Timeline Section -->
            <div class="p-4 h-48 shrink-0">
                <div class="glass rounded-xl p-4 h-full flex flex-col">
                    <div class="flex justify-between items-center mb-3 shrink-0">
                        <h3 class="text-sm font-bold text-blue-400">Timeline <span id="current-slot-name" class="text-slate-500 font-normal ml-2"></span></h3>
                        <div class="flex gap-2">
                            <button class="text-xs bg-slate-700 hover:bg-slate-600 px-2 py-1 rounded">Zoom In</button>
                            <button class="text-xs bg-slate-700 hover:bg-slate-600 px-2 py-1 rounded">Zoom Out</button>
                        </div>
                    </div>
                    <div id="timeline" class="timeline-track flex-1 rounded-lg overflow-x-auto flex items-center px-2 gap-1 min-h-0">
                        <div id="no-slot-message" class="w-full text-center text-slate-500 text-sm">Non è stato selezionato nessuno slot</div>
                        <!-- Timeline media slots loaded via JS -->
                    </div>
                </div>
            </div>

            <!-- Screens Grid -->
            <div class="flex-1 p-4 overflow-hidden min-h-0">
                <div id="screens-grid" class="grid grid-cols-2 gap-4 h-full min-h-0">
                    <!-- Screens will be rendered dynamically via JavaScript -->
                    <div class="text-slate-500 text-sm text-center col-span-2">Caricamento schermi...</div>
                </div>
            </div>
        </main>

        <!-- Right Column - Proprietà & Note (320px) -->
        <aside id="sidebar-right" class="sidebar-right w-[320px] glass border-l border-slate-700/50 flex flex-col overflow-hidden h-full">
            <!-- Playback Controls -->
            <div class="p-3 border-b border-slate-700/50 shrink-0">
                <h3 class="text-xs font-bold mb-2 text-blue-400">Controlli Riproduzione</h3>
                <div class="mb-2">
                    <label class="text-[10px] text-slate-500 block mb-1">Seleziona Schermo</label>
                    <select id="screen-select" class="w-full bg-slate-800 border border-slate-700 rounded px-2 py-1 text-xs">
                        <option value="">Tutti gli schermi</option>
                    </select>
                </div>
                <div class="flex gap-1">
                    <button onclick="sendPlaybackCommand('play')" class="flex-1 p-2 bg-green-600 hover:bg-green-500 rounded font-bold text-xs">PLAY</button>
                    <button onclick="sendPlaybackCommand('pause')" class="flex-1 p-2 bg-yellow-600 hover:bg-yellow-500 rounded font-bold text-xs">PAUSE</button>
                    <button onclick="sendPlaybackCommand('stop')" class="flex-1 p-2 bg-red-600 hover:bg-red-500 rounded font-bold text-xs">STOP</button>
                </div>
            </div>

            <!-- Gestore Audio/Video -->
            <div class="flex-1 overflow-y-auto p-3 space-y-3">
                <div class="glass rounded-lg p-3">
                    <h3 class="text-xs font-bold mb-2 text-purple-400">Gestore Audio/Video</h3>

                    <!-- VIDEO Section -->
                    <div class="mb-3">
                        <div class="flex items-center gap-2 mb-1">
                            <span class="text-[10px] font-bold bg-blue-500/20 text-blue-400 px-1.5 py-0.5 rounded">VIDEO</span>
                        </div>
                        <div class="grid grid-cols-2 gap-1.5 text-[10px]">
                            <div>
                                <label class="text-slate-500 block mb-0.5">Start</label>
                                <input type="text" class="w-full bg-slate-800 border border-slate-700 rounded px-1.5 py-0.5" placeholder="00:00:00">
                            </div>
                            <div>
                                <label class="text-slate-500 block mb-0.5">End</label>
                                <input type="text" class="w-full bg-slate-800 border border-slate-700 rounded px-1.5 py-0.5" placeholder="00:00:00">
                            </div>
                            <div>
                                <label class="text-slate-500 block mb-0.5">Screen</label>
                                <select class="w-full bg-slate-800 border border-slate-700 rounded px-1.5 py-0.5">
                                    <option>Screen 1</option>
                                    <option>Screen 2</option>
                                    <option>Screen 3</option>
                                    <option>Screen 4</option>
                                </select>
                            </div>
                            <div>
                                <label class="text-slate-500 block mb-0.5">Dissolvenza</label>
                                <select class="w-full bg-slate-800 border border-slate-700 rounded px-1.5 py-0.5">
                                    <option>Fade Black</option>
                                    <option>Crossfade</option>
                                    <option>Cut</option>
                                </select>
                            </div>
                            <div class="col-span-2">
                                <label class="text-slate-500 block mb-0.5">Offset (sec)</label>
                                <input type="number" class="w-full bg-slate-800 border border-slate-700 rounded px-1.5 py-0.5" placeholder="0">
                            </div>
                        </div>
                    </div>

                    <!-- AUDIO Section -->
                    <div class="mb-3">
                        <div class="flex items-center gap-2 mb-1">
                            <span class="text-[10px] font-bold bg-green-500/20 text-green-400 px-1.5 py-0.5 rounded">AUDIO</span>
                        </div>
                        <div class="grid grid-cols-2 gap-1.5 text-[10px]">
                            <div>
                                <label class="text-slate-500 block mb-0.5">Start</label>
                                <input type="text" class="w-full bg-slate-800 border border-slate-700 rounded px-1.5 py-0.5" placeholder="00:00:00">
                            </div>
                            <div>
                                <label class="text-slate-500 block mb-0.5">End</label>
                                <input type="text" class="w-full bg-slate-800 border border-slate-700 rounded px-1.5 py-0.5" placeholder="00:00:00">
                            </div>
                            <div>
                                <label class="text-slate-500 block mb-0.5">Duration</label>
                                <input type="text" class="w-full bg-slate-800 border border-slate-700 rounded px-1.5 py-0.5" placeholder="00:00:00">
                            </div>
                            <div>
                                <label class="text-slate-500 block mb-1">Tipo Dissolvenza</label>
                                <select class="w-full bg-slate-800 border border-slate-700 rounded px-2 py-1">
                                    <option>Fade In/Out</option>
                                    <option>Crossfade</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- FOTO Section -->
                    <div>
                        <div class="flex items-center gap-2 mb-1">
                            <span class="text-[10px] font-bold bg-yellow-500/20 text-yellow-400 px-1.5 py-0.5 rounded">FOTO</span>
                        </div>
                        <div class="grid grid-cols-2 gap-1.5 text-[10px]">
                            <div>
                                <label class="text-slate-500 block mb-0.5">Duration</label>
                                <input type="text" class="w-full bg-slate-800 border border-slate-700 rounded px-1.5 py-0.5" placeholder="00:00:00">
                            </div>
                            <div>
                                <label class="text-slate-500 block mb-0.5">Dissolvenza</label>
                                <select class="w-full bg-slate-800 border border-slate-700 rounded px-1.5 py-0.5">
                                    <option>Fade</option>
                                    <option>Crossfade</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Note Tecniche -->
                <div class="glass rounded-lg p-3">
                    <h3 class="text-xs font-bold mb-2 text-purple-400">Note Tecniche</h3>

                    <div class="space-y-2 text-[10px]">
                        <div>
                            <label class="text-slate-500 block mb-0.5">Materiale Palco</label>
                            <textarea class="w-full bg-slate-800 border border-slate-700 rounded px-1.5 py-0.5 h-12 resize-none" placeholder="Lista del materiale..."></textarea>
                        </div>
                        <div>
                            <label class="text-slate-500 block mb-0.5">Gestione Luci</label>
                            <textarea class="w-full bg-slate-800 border border-slate-700 rounded px-1.5 py-0.5 h-12 resize-none" placeholder="Note luci..."></textarea>
                        </div>
                        <div>
                            <label class="text-slate-500 block mb-0.5">Note Generali</label>
                            <textarea class="w-full bg-slate-800 border border-slate-700 rounded px-1.5 py-0.5 h-12 resize-none" placeholder="Note generiche..."></textarea>
                        </div>
                        <div>
                            <label class="text-slate-500 block mb-0.5">PAUSE</label>
                            <div class="flex gap-1">
                                <select class="flex-1 bg-slate-800 border border-slate-700 rounded px-1.5 py-0.5">
                                    <option>Fixed Time</option>
                                    <option>Riavvio Manuale</option>
                                </select>
                                <input type="text" class="w-16 bg-slate-800 border border-slate-700 rounded px-1.5 py-0.5" placeholder="00:00">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Status Console -->
            <div class="p-3 border-t border-slate-700/50 shrink-0">
                <h3 class="text-[10px] font-bold uppercase text-slate-500 mb-1">Logs</h3>
                <div id="logs" class="h-20 font-mono text-[9px] text-slate-400 bg-black/30 p-1.5 rounded-lg overflow-y-auto space-y-0.5">
                    <div>[System] Dashboard inizializzata</div>
                </div>
            </div>
        </aside>
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

        async function fetchQueue() {
            try {
                const response = await fetch('/api/queue');
                const result = await response.json();
                if (result.status === 'ok') {
                    renderQueue(result.data);
                }
            } catch (error) {
                console.error("Dashboard: Errore nel recupero coda:", error);
            }
        }

        async function fetchScreens() {
            try {
                const response = await fetch('/api/screens');
                const result = await response.json();
                if (result.status === 'ok') {
                    renderScreens(result.data);
                }
            } catch (error) {
                console.error("Dashboard: Errore nel recupero schermi:", error);
            }
        }

        function renderSetlist(talenti) {
            const container = document.getElementById('setlist');
            container.innerHTML = '';
            talenti.forEach((talent, index) => {
                const item = document.createElement('div');
                item.className = 'p-3 rounded-lg bg-slate-800/50 border border-slate-700 hover:border-blue-400 transition-all group';
                item.innerHTML = `
                    <div class="flex justify-between items-center">
                        <span class="text-xs font-mono text-slate-400">#${index + 1}</span>
                        <div class="relative">
                            <button onclick="toggleSlotMenu(${talent.id})" class="p-1 hover:bg-slate-700 rounded">
                                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"></path></svg>
                            </button>
                            <div id="slot-menu-${talent.id}" class="hidden absolute right-0 top-8 bg-slate-800 border border-slate-700 rounded-lg shadow-xl z-10 min-w-[120px]">
                                <button onclick="sendCommand('play', {slot_id: ${talent.id}})" class="w-full px-3 py-2 text-left text-sm hover:bg-slate-700 flex items-center gap-2">
                                    <svg class="w-3 h-3 text-green-400" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"></path></svg>
                                    Play
                                </button>
                                <button onclick="sendCommand('pause', {slot_id: ${talent.id}})" class="w-full px-3 py-2 text-left text-sm hover:bg-slate-700 flex items-center gap-2">
                                    <svg class="w-3 h-3 text-yellow-400" fill="currentColor" viewBox="0 0 24 24"><path d="M6 19h4V5H6v14zm8-14v14h4V5h-4z"></path></svg>
                                    Pause
                                </button>
                                <button onclick="sendCommand('stop', {slot_id: ${talent.id}})" class="w-full px-3 py-2 text-left text-sm hover:bg-slate-700 flex items-center gap-2">
                                    <svg class="w-3 h-3 text-red-400" fill="currentColor" viewBox="0 0 24 24"><path d="M6 6h12v12H6z"></path></svg>
                                    Stop
                                </button>
                                <div class="border-t border-slate-700"></div>
                                <button onclick="openTimeline(${talent.id})" class="w-full px-3 py-2 text-left text-sm hover:bg-slate-700 flex items-center gap-2">
                                    <svg class="w-3 h-3 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                                    Timeline
                                </button>
                            </div>
                        </div>
                    </div>
                    <h3 class="font-bold text-sm cursor-pointer hover:text-blue-400 transition-colors" onclick="selectSlot(${talent.id})">${talent.nome}</h3>
                `;
                container.appendChild(item);
            });
        }

        function toggleSlotMenu(slotId) {
            const menu = document.getElementById(`slot-menu-${slotId}`);
            document.querySelectorAll('[id^="slot-menu-"]').forEach(m => {
                if (m.id !== `slot-menu-${slotId}`) m.classList.add('hidden');
            });
            menu.classList.toggle('hidden');
        }

        let currentSlotId = null;

        function selectSlot(slotId) {
            currentSlotId = slotId;
            log(`Slot selezionato: ${slotId}`);
            fetchNotes(slotId);
            loadTimeline(slotId);
        }

        async function loadTimeline(slotId) {
            const slot = window.currentTalenti?.find(t => t.id == slotId);
            if (slot) {
                document.getElementById('current-slot-name').textContent = `- ${slot.nome}`;
                document.getElementById('no-slot-message').classList.add('hidden');
                
                // Mock timeline data
                const timeline = document.getElementById('timeline');
                const mockSlots = [
                    { id: 'm1', label: 'm1', type: 'blue' },
                    { id: 'm2', label: 'm2', type: 'blue' },
                    { id: 'm3', label: 'm3', type: 'blue' },
                    { id: 'm4', label: 'm4', type: 'blue' },
                    { id: 'mB', label: 'mB', type: 'purple' }
                ];
                
                timeline.innerHTML = '';
                mockSlots.forEach(slot => {
                    const slotEl = document.createElement('div');
                    slotEl.className = `media-slot min-w-[80px] h-16 bg-${slot.type}-500/20 border border-${slot.type}-500/30 rounded flex items-center justify-center text-xs cursor-pointer`;
                    slotEl.textContent = slot.label;
                    timeline.appendChild(slotEl);
                });
            }
        }

        async function createScreen(type) {
            const name = prompt('Inserisci il nome dello schermo:');
            if (!name) return;
            
            try {
                const response = await fetch('/api/screens/create', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ nome: name, tipo: 'indipendente' })
                });
                const result = await response.json();
                if (result.status === 'ok') {
                    log(`Schermo "${name}" creato con successo`);
                    fetchScreens();
                    // Open the screen in a new window
                    window.open(`/${type}?screen_id=${result.id}`, '_blank');
                }
            } catch (error) {
                console.error("Dashboard: Errore nella creazione schermo:", error);
            }
        }

        function openTimeline(slotId) {
            window.open(`/timeline?slot_id=${slotId}`, '_blank', 'width=1400,height=800');
            log(`Timeline aperta per slot ${slotId}`);
        }

        function renderQueue(queue) {
            const container = document.getElementById('queue-list');
            const countEl = document.getElementById('queue-count');
            container.innerHTML = '';
            countEl.textContent = queue.length;

            queue.forEach((item, index) => {
                const queueItem = document.createElement('div');
                const statusColor = item.stato === 'playing' ? 'text-green-400' :
                                   item.stato === 'completed' ? 'text-slate-500' : 'text-blue-400';
                const statusBg = item.stato === 'playing' ? 'bg-green-500/10 border-green-500/30' :
                                  item.stato === 'completed' ? 'bg-slate-700/50 border-slate-600' : 'bg-blue-500/10 border-blue-500/30';

                queueItem.className = `p-2 rounded border ${statusBg} text-xs flex justify-between items-center gap-2`;
                queueItem.innerHTML = `
                    <div class="flex items-center gap-2 flex-1 min-w-0">
                        <span class="text-slate-500 font-mono text-[10px]">#${index + 1}</span>
                        <span class="${statusColor} font-medium truncate">${item.talento_nome || 'Media ' + (index + 1)}</span>
                    </div>
                    <div class="flex items-center gap-2 shrink-0">
                        ${item.created_at ? `<span class="text-slate-600 text-[10px]">${new Date(item.created_at).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})}</span>` : ''}
                        <span class="text-[10px] uppercase ${statusColor} px-1.5 py-0.5 rounded bg-slate-800">${item.stato}</span>
                    </div>
                `;
                container.appendChild(queueItem);
            });
        }

        function renderScreens(screens) {
            const container = document.getElementById('screens-grid');
            container.innerHTML = '';

            // Also populate the screen select dropdown
            const screenSelect = document.getElementById('screen-select');
            if (screenSelect) {
                screenSelect.innerHTML = '<option value="">Tutti gli schermi</option>';
                screens.forEach(screen => {
                    screenSelect.innerHTML += `<option value="${screen.id}">${screen.nome}</option>`;
                });
            }

            screens.forEach(screen => {
                const screenDiv = document.createElement('div');
                screenDiv.className = 'glass rounded-xl overflow-hidden flex flex-col';
                screenDiv.id = `screen-${screen.id}`;

                // Generate mirror options based on other screens
                let mirrorOptions = '<option value="indipendente">Indipendente</option>';
                screens.forEach(s => {
                    if (s.id !== screen.id) {
                        mirrorOptions += `<option value="mirror-${s.id}">Mirror ${s.nome}</option>`;
                    }
                });

                screenDiv.innerHTML = `
                    <div class="p-2 border-b border-slate-700/50 flex justify-between items-center shrink-0">
                        <span class="font-bold text-xs text-blue-400">${screen.nome.toUpperCase()}</span>
                        <div class="flex gap-1 items-center">
                            <select onchange="updateScreenType(${screen.id}, this.value)" class="text-[10px] bg-slate-800 border border-slate-700 rounded px-1.5 py-0.5">
                                ${mirrorOptions}
                            </select>
                            <button onclick="openScreenWindow(${screen.id}, '${screen.nome}')" class="p-1 hover:bg-slate-700 rounded text-slate-400 hover:text-blue-400 transition-colors" title="Apri schermo in nuova finestra">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                    <div class="flex-1 bg-black flex items-center justify-center relative min-h-0">
                        <img id="screen-${screen.id}-image" class="w-full h-full object-contain pointer-events-none opacity-50 hidden" alt="">
                        <video id="screen-${screen.id}-video" class="w-full h-full object-contain pointer-events-none opacity-50 hidden" muted></video>
                        <div id="screen-${screen.id}-placeholder" class="w-full h-full flex items-center justify-center bg-slate-800 hidden">
                            <svg class="w-12 h-12 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <span id="screen-${screen.id}-label" class="text-slate-600 font-mono text-xs absolute">${screen.nome}</span>
                    </div>
                `;

                container.appendChild(screenDiv);
            });

            // Set current screen types and load media previews
            screens.forEach(screen => {
                const select = document.querySelector(`#screen-${screen.id} select`);
                if (select) {
                    select.value = screen.tipo;
                }
                // Load media for this screen as preview
                loadScreenPreview(screen.id);
            });
        }

        async function loadScreenPreview(screenId) {
            try {
                const response = await fetch(`/api/screens/show?id=${screenId}`);
                const result = await response.json();
                const video = document.getElementById(`screen-${screenId}-video`);
                const image = document.getElementById(`screen-${screenId}-image`);
                const placeholder = document.getElementById(`screen-${screenId}-placeholder`);
                const label = document.getElementById(`screen-${screenId}-label`);

                // Reset all elements
                if (video) {
                    video.classList.add('hidden');
                    video.removeAttribute('src');
                    video.load();
                }
                if (image) {
                    image.classList.add('hidden');
                    image.removeAttribute('src');
                }
                if (placeholder) {
                    placeholder.classList.add('hidden');
                }

                if (result.status === 'ok' && result.data.media && Array.isArray(result.data.media) && result.data.media.length > 0) {
                    const media = result.data.media[0];
                    if (!media || !media.file_path) {
                        console.log(`Invalid media data for screen ${screenId}`);
                        if (placeholder) placeholder.classList.remove('hidden');
                        if (label) label.style.display = 'none';
                        return;
                    }

                    // Normalize file path (remove escaped backslashes)
                    const filePath = media.file_path.replace(/\\/g, '/').toLowerCase();
                    const normalizedPath = media.file_path.replace(/\\/g, '/');
                    const isImage = filePath.match(/\.(jpg|jpeg|png|gif|webp)$/);
                    const isVideo = filePath.match(/\.(mp4|webm|ogg|mov)$/);

                    console.log(`Loading media for screen ${screenId}:`, normalizedPath, isImage ? '(image)' : isVideo ? '(video)' : '(unknown)');

                    if (isImage && image) {
                        // Load as image
                        image.src = normalizedPath;
                        image.onload = () => {
                            image.classList.remove('hidden', 'opacity-50');
                            if (label) label.style.display = 'none';
                        };
                        image.onerror = () => {
                            console.error(`Error loading image: ${normalizedPath}`);
                            // Show placeholder on error
                            if (placeholder) placeholder.classList.remove('hidden');
                            if (label) label.style.display = 'none';
                        };
                    } else if (isVideo && video) {
                        // Load as video
                        video.src = normalizedPath;
                        video.load();

                        video.onloadedmetadata = () => {
                            video.currentTime = 0.1;
                            video.classList.remove('hidden', 'opacity-50');
                            if (label) label.style.display = 'none';
                        };

                        video.onerror = () => {
                            console.error(`Error loading video: ${normalizedPath}`);
                            // Show placeholder on error
                            if (placeholder) placeholder.classList.remove('hidden');
                            if (label) label.style.display = 'none';
                        };
                    } else {
                        // Unknown format, show placeholder
                        console.log(`Unknown media format: ${normalizedPath}`);
                        if (placeholder) placeholder.classList.remove('hidden');
                        if (label) label.style.display = 'none';
                    }
                } else {
                    // No media, show placeholder
                    console.log(`No media for screen ${screenId}`);
                    if (placeholder) placeholder.classList.remove('hidden');
                    if (label) label.style.display = 'none';
                }
            } catch (error) {
                console.error(`Dashboard: Errore caricamento anteprima schermo ${screenId}:`, error);
                // Show placeholder on error
                const placeholder = document.getElementById(`screen-${screenId}-placeholder`);
                const label = document.getElementById(`screen-${screenId}-label`);
                if (placeholder) placeholder.classList.remove('hidden');
                if (label) label.style.display = 'none';
            }
        }

        // Store opened windows reference
        const openedWindows = {};

        // Sidebar toggle function
        function toggleSidebar(side) {
            const sidebar = document.getElementById(`sidebar-${side}`);
            if (sidebar) {
                sidebar.classList.toggle('open');
            }
        }

        // Close sidebar when clicking outside on small screens
        document.addEventListener('click', (e) => {
            if (window.innerWidth < 1280) {
                const leftSidebar = document.getElementById('sidebar-left');
                const rightSidebar = document.getElementById('sidebar-right');
                const leftButton = e.target.closest('button[onclick*="toggleSidebar(\'left\')"]');
                const rightButton = e.target.closest('button[onclick*="toggleSidebar(\'right\')"]');

                if (leftSidebar && leftSidebar.classList.contains('open') && !leftButton && !leftSidebar.contains(e.target)) {
                    leftSidebar.classList.remove('open');
                }
                if (rightSidebar && rightSidebar.classList.contains('open') && !rightButton && !rightSidebar.contains(e.target)) {
                    rightSidebar.classList.remove('open');
                }
            }
        });

        function openScreenWindow(screenId, screenName) {
            // Check if window already exists
            if (openedWindows[screenId] && !openedWindows[screenId].closed) {
                openedWindows[screenId].focus();
                log(`Finestra schermo ${screenName} già aperta, portata in primo piano`);
                return;
            }

            // Open new window for projector
            const windowFeatures = 'width=1280,height=720,menubar=no,toolbar=no,location=no,status=no,scrollbars=yes,resizable=yes';
            const newWindow = window.open(`/projector?screen_id=${screenId}`, `screen-${screenId}`, windowFeatures);

            if (newWindow) {
                openedWindows[screenId] = newWindow;
                log(`Finestra schermo ${screenName} aperta (ID: ${screenId})`);

                // Sync initial state
                syncScreenToWindow(screenId);

                // Monitor window close
                const checkClosed = setInterval(() => {
                    if (newWindow.closed) {
                        clearInterval(checkClosed);
                        delete openedWindows[screenId];
                        log(`Finestra schermo ${screenName} chiusa`);
                    }
                }, 1000);
            } else {
                log(`Errore: Impossibile aprire la finestra (popup blocker?)`);
            }
        }

        function updateScreenType(screenId, tipo) {
            log(`Tipo schermo ${screenId} cambiato in: ${tipo}`);
            // Update via API
            fetch(`/api/screens/update?id=${screenId}`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ tipo })
            }).catch(err => console.error('Errore aggiornamento tipo schermo:', err));
        }

        function syncScreenToWindow(screenId) {
            // Send current state to the opened window
            const video = document.getElementById(`screen-${screenId}-video`);
            if (video && video.src) {
                const state = {
                    screenId,
                    src: video.src,
                    currentTime: video.currentTime,
                    playing: !video.paused,
                    timestamp: Date.now()
                };
                localStorage.setItem(`screen_sync_${screenId}`, JSON.stringify(state));
            }
        }

        function selectTalent(talent) {
            log(`Talento selezionato: ${talent.nome}`);
            // Load talent notes and media
            fetchNotes(talent.id);
        }

        async function fetchNotes(talentId) {
            try {
                const response = await fetch(`/api/notes/grouped?talento_id=${talentId}`);
                const result = await response.json();
                if (result.status === 'ok') {
                    updateNotesPanel(result.data);
                }
            } catch (error) {
                console.error("Dashboard: Errore nel recupero note:", error);
            }
        }

        function updateNotesPanel(notes) {
            // Update note textareas with fetched data
            if (notes.materiale_palco.length > 0) {
                const textarea = document.querySelector('textarea[placeholder="Lista del materiale..."]');
                if (textarea) textarea.value = notes.materiale_palco[0].contenuto;
            }
            if (notes.luci.length > 0) {
                const textarea = document.querySelector('textarea[placeholder="Note luci..."]');
                if (textarea) textarea.value = notes.luci[0].contenuto;
            }
        }

        // Caricamento iniziale
        fetchSetlist();
        fetchQueue();
        fetchScreens();

        function sendCommand(action, data = {}, screenId = null) {
            const command = {
                action,
                data,
                screenId,
                timestamp: Date.now()
            };
            localStorage.setItem('regia_command', JSON.stringify(command));
            log(`Comando inviato: ${action}${screenId ? ` (screen: ${screenId})` : ''}`);
        }

        function sendPlaybackCommand(action) {
            const screenSelect = document.getElementById('screen-select');
            const screenId = screenSelect.value ? parseInt(screenSelect.value) : null;
            
            // Get current slot media if available
            const data = {};
            
            sendCommand(action, data, screenId);
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

        // Timeline slot click handlers
        document.querySelectorAll('.media-slot').forEach(slot => {
            slot.addEventListener('click', function() {
                const slotName = this.dataset.slot;
                log(`Slot timeline selezionato: ${slotName}`);
                this.classList.toggle('screen-active');
            });
        });

        // Screen configuration change handlers
        document.querySelectorAll('select').forEach(select => {
            select.addEventListener('change', function() {
                const screenId = this.closest('[id^="screen-"]')?.id;
                if (screenId) {
                    log(`Configurazione ${screenId} cambiata: ${this.value}`);
                }
            });
        });

        // Monitoring dello stato delle altre finestre
        window.addEventListener('storage', (e) => {
            if (e.key === 'screen_state') {
                const status = JSON.parse(e.newValue);
                updateScreenStatus(status);
            } else if (e.key === 'queue_state') {
                const queue = JSON.parse(e.newValue);
                renderQueue(queue);
            }
        });

        function updateScreenStatus(status) {
            // Update individual screen states
            if (status.screenId) {
                const screenEl = document.getElementById(`screen-${status.screenId}`);
                if (screenEl) {
                    // Update border for active state
                    if (status.active) {
                        screenEl.classList.add('screen-active');
                    } else {
                        screenEl.classList.remove('screen-active');
                    }

                    // Sync video thumbnail with actual player state
                    const video = document.getElementById(`screen-${status.screenId}-video`);
                    if (video) {
                        video.currentTime = status.currentTime || 0;
                        video.classList.toggle('opacity-50', !status.active);
                    }
                }
            }
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
