<!DOCTYPE html>
<html lang="it" class="h-full bg-slate-900 border-slate-800">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Timeline - Olmo's Got Talent</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Outfit:wght@600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        h1, h2, h3 { font-family: 'Outfit', sans-serif; }
        .glass { background: rgba(30, 41, 59, 0.7); backdrop-filter: blur(12px); border: 1px solid rgba(255, 255, 255, 0.1); }
        .gradient-bg { background: radial-gradient(circle at top right, #3b82f6, transparent), radial-gradient(circle at bottom left, #8b5cf6, transparent); }
        .timeline-track { background: linear-gradient(90deg, #1e293b 0%, #334155 100%); }
        .media-slot { transition: all 0.2s ease; cursor: move; }
        .media-slot:hover { transform: scale(1.02); }
        .media-slot.selected { border-color: #3b82f6; box-shadow: 0 0 15px rgba(59, 130, 246, 0.4); }
    </style>
</head>
<body class="h-full text-slate-200 overflow-hidden">
    <div class="flex h-full">
        <!-- Left Panel - Media Library -->
        <aside class="w-80 glass border-r border-slate-700/50 flex flex-col">
            <div class="p-4 border-b border-slate-700/50">
                <h2 class="text-xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-blue-400 to-purple-400">Media Library</h2>
            </div>
            
            <div class="p-4 border-b border-slate-700/50">
                <input type="text" id="media-search" placeholder="Cerca media..." class="w-full bg-slate-800 border border-slate-700 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-blue-500">
            </div>

            <div id="media-library" class="flex-1 overflow-y-auto p-3 space-y-2">
                <!-- Loaded via JS -->
            </div>
        </aside>

        <!-- Main Content - Timeline -->
        <main class="flex-1 flex flex-col relative overflow-hidden">
            <div class="absolute inset-0 gradient-bg opacity-10 pointer-events-none"></div>
            
            <!-- Header -->
            <header class="p-4 flex justify-between items-center glass border-b border-slate-700/50">
                <div>
                    <h1 class="text-2xl font-bold">Timeline</h1>
                    <p id="slot-name" class="text-slate-400 text-xs">Slot: Caricamento...</p>
                </div>
                <div class="flex gap-3">
                    <button onclick="saveTimeline()" class="px-3 py-1.5 bg-green-600 hover:bg-green-500 rounded-lg font-semibold transition-all text-sm">Salva</button>
                    <button onclick="window.close()" class="px-3 py-1.5 bg-slate-800 hover:bg-slate-700 rounded-lg font-semibold transition-all text-sm">Chiudi</button>
                </div>
            </header>

            <!-- Timeline Controls -->
            <div class="p-4">
                <div class="glass rounded-xl p-4">
                    <div class="flex justify-between items-center mb-3">
                        <h3 class="text-sm font-bold text-blue-400">Timeline Editor</h3>
                        <div class="flex gap-2">
                            <button onclick="zoomIn()" class="text-xs bg-slate-700 hover:bg-slate-600 px-2 py-1 rounded">Zoom In</button>
                            <button onclick="zoomOut()" class="text-xs bg-slate-700 hover:bg-slate-600 px-2 py-1 rounded">Zoom Out</button>
                            <button onclick="addMediaSlot()" class="text-xs bg-blue-600 hover:bg-blue-500 px-2 py-1 rounded">+ Aggiungi</button>
                        </div>
                    </div>
                    <div id="timeline" class="timeline-track h-32 rounded-lg overflow-x-auto flex items-center px-2 gap-1">
                        <!-- Timeline media slots -->
                        <div class="media-slot min-w-[100px] h-24 bg-blue-500/20 border border-blue-500/30 rounded flex flex-col items-center justify-center text-xs selected" data-id="1">
                            <span class="font-bold">m1</span>
                            <span class="text-[10px] text-slate-400">0:00 - 0:30</span>
                        </div>
                        <div class="media-slot min-w-[100px] h-24 bg-blue-500/20 border border-blue-500/30 rounded flex flex-col items-center justify-center text-xs" data-id="2">
                            <span class="font-bold">m2</span>
                            <span class="text-[10px] text-slate-400">0:30 - 1:00</span>
                        </div>
                        <div class="media-slot min-w-[100px] h-24 bg-blue-500/20 border border-blue-500/30 rounded flex flex-col items-center justify-center text-xs" data-id="3">
                            <span class="font-bold">m3</span>
                            <span class="text-[10px] text-slate-400">1:00 - 1:30</span>
                        </div>
                        <div class="media-slot min-w-[100px] h-24 bg-purple-500/20 border border-purple-500/30 rounded flex flex-col items-center justify-center text-xs" data-id="4">
                            <span class="font-bold">mB</span>
                            <span class="text-[10px] text-slate-400">1:30 - 2:00</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Selected Media Properties -->
            <div class="flex-1 p-4 overflow-hidden">
                <div class="glass rounded-xl p-6 h-full overflow-y-auto">
                    <h3 class="text-sm font-bold mb-4 text-purple-400">Proprietà Media Selezionato</h3>
                    
                    <div id="media-properties" class="grid grid-cols-2 gap-4 text-sm">
                        <div class="space-y-2">
                            <label class="text-xs font-bold uppercase text-slate-500">Nome</label>
                            <input type="text" id="prop-name" class="w-full bg-slate-800 border border-slate-700 rounded px-3 py-2" value="m1">
                        </div>
                        <div class="space-y-2">
                            <label class="text-xs font-bold uppercase text-slate-500">Tipo</label>
                            <select id="prop-type" class="w-full bg-slate-800 border border-slate-700 rounded px-3 py-2">
                                <option value="VIDEO">Video</option>
                                <option value="AUDIO">Audio</option>
                                <option value="FOTO">Foto</option>
                            </select>
                        </div>
                        <div class="space-y-2">
                            <label class="text-xs font-bold uppercase text-slate-500">Start Time</label>
                            <input type="text" id="prop-start" class="w-full bg-slate-800 border border-slate-700 rounded px-3 py-2" value="00:00:00">
                        </div>
                        <div class="space-y-2">
                            <label class="text-xs font-bold uppercase text-slate-500">End Time</label>
                            <input type="text" id="prop-end" class="w-full bg-slate-800 border border-slate-700 rounded px-3 py-2" value="00:00:30">
                        </div>
                        <div class="space-y-2">
                            <label class="text-xs font-bold uppercase text-slate-500">Screen</label>
                            <select id="prop-screen" class="w-full bg-slate-800 border border-slate-700 rounded px-3 py-2">
                                <option value="1">Screen 1</option>
                                <option value="2">Screen 2</option>
                                <option value="3">Screen 3</option>
                                <option value="4">Screen 4</option>
                            </select>
                        </div>
                        <div class="space-y-2">
                            <label class="text-xs font-bold uppercase text-slate-500">Tipo Dissolvenza</label>
                            <select id="prop-fade" class="w-full bg-slate-800 border border-slate-700 rounded px-3 py-2">
                                <option value="fade_to_black">Fade to Black</option>
                                <option value="fade_from_black">Fade from Black</option>
                                <option value="crossfade">Crossfade</option>
                                <option value="cut">Cut</option>
                            </select>
                        </div>
                        <div class="space-y-2 col-span-2">
                            <label class="text-xs font-bold uppercase text-slate-500">Durata Transizione (sec)</label>
                            <input type="number" id="prop-fade-duration" class="w-full bg-slate-800 border border-slate-700 rounded px-3 py-2" value="0.5" step="0.1">
                        </div>
                        <div class="col-span-2 flex gap-2 pt-4">
                            <button onclick="applyProperties()" class="flex-1 px-4 py-2 bg-blue-600 hover:bg-blue-500 rounded-lg font-semibold">Applica</button>
                            <button onclick="deleteSelected()" class="px-4 py-2 bg-red-600 hover:bg-red-500 rounded-lg font-semibold">Elimina</button>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
        let currentSlotId = null;
        let selectedMediaId = null;
        let zoomLevel = 1;

        // Get slot_id from URL
        const urlParams = new URLSearchParams(window.location.search);
        currentSlotId = urlParams.get('slot_id');

        async function fetchSlotInfo() {
            if (!currentSlotId) return;
            try {
                const response = await fetch(`/api/talenti`);
                const result = await response.json();
                if (result.status === 'ok') {
                    const slot = result.data.find(t => t.id == currentSlotId);
                    if (slot) {
                        document.getElementById('slot-name').textContent = `Slot: ${slot.nome}`;
                    }
                }
            } catch (error) {
                console.error("Timeline: Errore nel recupero slot:", error);
            }
        }

        async function fetchMediaLibrary() {
            try {
                const response = await fetch('/api/media');
                const result = await response.json();
                if (result.status === 'ok') {
                    renderMediaLibrary(result.data);
                }
            } catch (error) {
                console.error("Timeline: Errore nel recupero media:", error);
            }
        }

        function renderMediaLibrary(media) {
            const container = document.getElementById('media-library');
            container.innerHTML = '';
            media.forEach(m => {
                const item = document.createElement('div');
                item.className = 'p-3 rounded-lg bg-slate-800/50 border border-slate-700 hover:border-blue-400 cursor-pointer transition-all';
                item.draggable = true;
                item.innerHTML = `
                    <div class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full ${m.tipo_media === 'VIDEO' ? 'bg-blue-400' : m.tipo_media === 'AUDIO' ? 'bg-green-400' : 'bg-yellow-400'}"></span>
                        <span class="text-sm font-bold truncate">${m.file_path.split('/').pop()}</span>
                    </div>
                    <span class="text-xs text-slate-500">${m.tipo_media}</span>
                `;
                item.addEventListener('dragstart', (e) => {
                    e.dataTransfer.setData('mediaId', m.id);
                });
                container.appendChild(item);
            });
        }

        // Timeline slot selection
        document.querySelectorAll('.media-slot').forEach(slot => {
            slot.addEventListener('click', function() {
                document.querySelectorAll('.media-slot').forEach(s => s.classList.remove('selected'));
                this.classList.add('selected');
                selectedMediaId = this.dataset.id;
                loadMediaProperties(this.dataset.id);
            });
        });

        function loadMediaProperties(mediaId) {
            // Load properties for selected media slot
            document.getElementById('prop-name').value = `m${mediaId}`;
        }

        function applyProperties() {
            if (!selectedMediaId) return;
            // Apply properties to selected media slot
            console.log('Applying properties to media', selectedMediaId);
        }

        function deleteSelected() {
            if (!selectedMediaId) return;
            if (!confirm('Eliminare questo media dalla timeline?')) return;
            const slot = document.querySelector(`.media-slot[data-id="${selectedMediaId}"]`);
            if (slot) slot.remove();
            selectedMediaId = null;
        }

        function addMediaSlot() {
            const timeline = document.getElementById('timeline');
            const newId = timeline.children.length + 1;
            const newSlot = document.createElement('div');
            newSlot.className = 'media-slot min-w-[100px] h-24 bg-blue-500/20 border border-blue-500/30 rounded flex flex-col items-center justify-center text-xs';
            newSlot.dataset.id = newId;
            newSlot.innerHTML = `
                <span class="font-bold">m${newId}</span>
                <span class="text-[10px] text-slate-400">--:-- - --:--</span>
            `;
            newSlot.addEventListener('click', function() {
                document.querySelectorAll('.media-slot').forEach(s => s.classList.remove('selected'));
                this.classList.add('selected');
                selectedMediaId = this.dataset.id;
                loadMediaProperties(this.dataset.id);
            });
            timeline.appendChild(newSlot);
        }

        function zoomIn() {
            zoomLevel = Math.min(zoomLevel + 0.2, 2);
            applyZoom();
        }

        function zoomOut() {
            zoomLevel = Math.max(zoomLevel - 0.2, 0.5);
            applyZoom();
        }

        function applyZoom() {
            document.querySelectorAll('.media-slot').forEach(slot => {
                slot.style.minWidth = `${100 * zoomLevel}px`;
            });
        }

        function saveTimeline() {
            // Save timeline configuration
            console.log('Saving timeline for slot', currentSlotId);
            alert('Timeline salvata!');
        }

        // Initialize
        fetchSlotInfo();
        fetchMediaLibrary();
    </script>
</body>
</html>
