<!DOCTYPE html>
<html lang="it" class="h-full bg-slate-900 border-slate-800">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Amministrazione - Olmo's Got Talent</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Outfit:wght@600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        h1, h2, h3 { font-family: 'Outfit', sans-serif; }
        .glass { background: rgba(30, 41, 59, 0.7); backdrop-filter: blur(12px); border: 1px solid rgba(255, 255, 255, 0.1); }
    </style>
</head>
<body class="h-full text-slate-200">
    <div class="min-h-full flex flex-col">
        <!-- Header -->
        <header class="p-6 glass border-b border-slate-700/50 sticky top-0 z-10">
            <div class="max-w-7xl mx-auto flex justify-between items-center">
                <div>
                    <h1 class="text-3xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-blue-400 to-purple-400">Pannello Amministrativo</h1>
                    <p class="text-slate-400 text-sm">Gestione talenti, media, schermi e note tecniche</p>
                </div>
                <a href="/dashboard" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 rounded-lg font-semibold transition-all border border-slate-700 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                    <span>Dashboard</span>
                </a>
            </div>
        </header>

        <!-- Main Content -->
        <main class="flex-1 max-w-7xl mx-auto w-full p-6 space-y-8">
            <!-- Tabs -->
            <div class="flex gap-2 border-b border-slate-700/50 pb-4">
                <button onclick="showTab('slot')" class="tab-btn px-4 py-2 rounded-lg bg-blue-600 text-white font-semibold text-sm" data-tab="slot">Slot</button>
                <button onclick="showTab('media')" class="tab-btn px-4 py-2 rounded-lg bg-slate-800 hover:bg-slate-700 font-semibold text-sm" data-tab="media">Media</button>
                <button onclick="showTab('media-library')" class="tab-btn px-4 py-2 rounded-lg bg-slate-800 hover:bg-slate-700 font-semibold text-sm" data-tab="media-library">Media Library</button>
                <button onclick="showTab('schermi')" class="tab-btn px-4 py-2 rounded-lg bg-slate-800 hover:bg-slate-700 font-semibold text-sm" data-tab="schermi">Schermi</button>
                <button onclick="showTab('note')" class="tab-btn px-4 py-2 rounded-lg bg-slate-800 hover:bg-slate-700 font-semibold text-sm" data-tab="note">Note Tecniche</button>
            </div>

            <!-- Slot Tab -->
            <div id="tab-slot" class="tab-content space-y-6">
                <!-- Form Aggiunta Slot -->
                <section class="glass rounded-2xl p-6">
                    <h2 class="text-xl font-bold mb-6 flex items-center gap-2 text-blue-400">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
                        Aggiungi Nuovo Slot
                    </h2>
                    <form id="talent-form" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="text-xs font-bold uppercase text-slate-500">Nome e Cognome</label>
                            <input type="text" name="nome" required class="w-full bg-slate-800 border border-slate-700 rounded-xl px-4 py-3 focus:outline-none focus:border-blue-500 transition-colors">
                        </div>
                        <div class="space-y-2">
                            <label class="text-xs font-bold uppercase text-slate-500">Categoria</label>
                            <select name="categoria" class="w-full bg-slate-800 border border-slate-700 rounded-xl px-4 py-3 focus:outline-none focus:border-blue-500 transition-colors">
                                <option value="CANTO">Canto</option>
                                <option value="BALLO">Ballo</option>
                                <option value="RECITAZIONE">Recitazione</option>
                                <option value="MAGIA">Magia</option>
                                <option value="ALTRO">Altro</option>
                            </select>
                        </div>
                        <div class="md:col-span-2 flex justify-end">
                            <button type="submit" class="px-8 py-3 bg-blue-600 hover:bg-blue-500 rounded-xl font-bold transition-all shadow-lg shadow-blue-900/20">
                                Salva Slot
                            </button>
                        </div>
                    </form>
                </section>

                <!-- Slot Manager -->
                <section class="glass rounded-2xl p-6">
                    <h2 class="text-xl font-bold mb-6 flex items-center gap-2 text-purple-400">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                        Slot Attuali
                    </h2>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead>
                                <tr class="text-slate-500 text-xs uppercase tracking-wider border-b border-slate-700/50">
                                    <th class="pb-4 pl-4">Pos</th>
                                    <th class="pb-4">Slot</th>
                                    <th class="pb-4">Categoria</th>
                                    <th class="pb-4 text-right pr-4">Azioni</th>
                                </tr>
                            </thead>
                            <tbody id="talent-list" class="divide-y divide-slate-700/50">
                                <!-- Loaded via JS -->
                            </tbody>
                        </table>
                    </div>
                </section>
            </div>

            <!-- Media Tab -->
            <div id="tab-media" class="tab-content space-y-6 hidden">
                <section class="glass rounded-2xl p-6">
                    <h2 class="text-xl font-bold mb-6 flex items-center gap-2 text-green-400">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                        Gestione Media
                    </h2>
                    <form id="media-form" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="text-xs font-bold uppercase text-slate-500">Talento</label>
                            <select name="talento_id" id="media-talento-select" class="w-full bg-slate-800 border border-slate-700 rounded-xl px-4 py-3 focus:outline-none focus:border-blue-500 transition-colors">
                                <!-- Loaded via JS -->
                            </select>
                        </div>
                        <div class="space-y-2">
                            <label class="text-xs font-bold uppercase text-slate-500">Tipo Media</label>
                            <select name="tipo_media" class="w-full bg-slate-800 border border-slate-700 rounded-xl px-4 py-3 focus:outline-none focus:border-blue-500 transition-colors">
                                <option value="VIDEO">Video</option>
                                <option value="AUDIO">Audio</option>
                                <option value="FOTO">Foto</option>
                            </select>
                        </div>
                        <div class="space-y-2">
                            <label class="text-xs font-bold uppercase text-slate-500">Seleziona Media</label>
                            <div class="relative">
                                <button type="button" onclick="toggleMediaSelector()" id="media-selector-btn" class="w-full bg-slate-800 border border-slate-700 rounded-xl px-4 py-3 text-left focus:outline-none focus:border-blue-500 transition-colors flex items-center justify-between">
                                    <span id="selected-media-name" class="text-slate-400">Seleziona un media dalla library</span>
                                    <svg class="w-5 h-5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                </button>
                                <input type="hidden" name="media_library_id" id="media-library-id">
                                <div id="media-selector-dropdown" class="absolute z-50 w-full mt-2 bg-slate-800 border border-slate-700 rounded-xl shadow-2xl hidden max-h-96 overflow-y-auto">
                                    <div class="p-4">
                                        <input type="text" id="media-search" placeholder="Cerca media..." class="w-full bg-slate-900 border border-slate-700 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-blue-500" oninput="filterMediaLibrary()">
                                    </div>
                                    <div id="media-selector-list" class="divide-y divide-slate-700">
                                        <!-- Loaded via JS -->
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="space-y-2">
                            <label class="text-xs font-bold uppercase text-slate-500">Screen</label>
                            <select name="screen_id" id="media-screen-select" class="w-full bg-slate-800 border border-slate-700 rounded-xl px-4 py-3 focus:outline-none focus:border-blue-500 transition-colors">
                                <!-- Loaded via JS -->
                            </select>
                        </div>
                        <div class="space-y-2">
                            <label class="text-xs font-bold uppercase text-slate-500">Tipo Output</label>
                            <select name="tipo_output" class="w-full bg-slate-800 border border-slate-700 rounded-xl px-4 py-3 focus:outline-none focus:border-blue-500 transition-colors">
                                <option value="proiettore">Proiettore</option>
                                <option value="gobbo">Gobbo</option>
                            </select>
                        </div>
                        <div class="space-y-2">
                            <label class="text-xs font-bold uppercase text-slate-500">Tipo Dissolvenza</label>
                            <select name="tipo_dissolvenza" class="w-full bg-slate-800 border border-slate-700 rounded-xl px-4 py-3 focus:outline-none focus:border-blue-500 transition-colors">
                                <option value="fade_to_black">Fade to Black</option>
                                <option value="fade_from_black">Fade from Black</option>
                                <option value="crossfade">Crossfade</option>
                                <option value="cut">Cut</option>
                                <option value="dissolve">Dissolve</option>
                            </select>
                        </div>
                        <div class="space-y-2">
                            <label class="text-xs font-bold uppercase text-slate-500">Durata Transizione (sec)</label>
                            <input type="number" name="durata_sec" step="0.1" class="w-full bg-slate-800 border border-slate-700 rounded-xl px-4 py-3 focus:outline-none focus:border-blue-500 transition-colors" value="0">
                        </div>
                        <div class="md:col-span-2 flex justify-end">
                            <button type="submit" class="px-8 py-3 bg-green-600 hover:bg-green-500 rounded-xl font-bold transition-all shadow-lg shadow-green-900/20">
                                Aggiungi Media
                            </button>
                        </div>
                    </form>
                </section>

                <!-- Media List -->
                <section class="glass rounded-2xl p-6">
                    <h2 class="text-xl font-bold mb-6 text-green-400">Media Registrati</h2>
                    <div id="media-list" class="space-y-2">
                        <!-- Loaded via JS -->
                    </div>
                </section>
            </div>

            <!-- Media Library Tab -->
            <div id="tab-media-library" class="tab-content space-y-6 hidden">
                <!-- Upload Section -->
                <section class="glass rounded-2xl p-6">
                    <h2 class="text-xl font-bold mb-6 flex items-center gap-2 text-purple-400">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg>
                        Carica Nuovo Media
                    </h2>
                    <form id="upload-form" class="space-y-4">
                        <div class="border-2 border-dashed border-slate-700 rounded-xl p-8 text-center hover:border-purple-500 transition-colors">
                            <input type="file" id="media-file" accept="image/*,video/*,audio/*" class="hidden" onchange="handleFileSelect(this)">
                            <label for="media-file" class="cursor-pointer">
                                <svg class="w-12 h-12 mx-auto text-slate-500 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg>
                                <p class="text-slate-400 font-semibold">Clicca per selezionare un file</p>
                                <p class="text-slate-600 text-sm mt-2">Formati supportati: JPG, PNG, GIF, MP4, WEBM, MP3, WAV</p>
                            </label>
                            <div id="file-info" class="mt-4 hidden">
                                <p class="text-purple-400 font-semibold" id="selected-file-name"></p>
                            </div>
                        </div>
                        <div class="flex justify-end gap-3">
                            <button type="button" onclick="resetUploadForm()" class="px-6 py-3 bg-slate-800 hover:bg-slate-700 rounded-xl font-semibold transition-all">Annulla</button>
                            <button type="submit" class="px-6 py-3 bg-purple-600 hover:bg-purple-500 rounded-xl font-bold transition-all shadow-lg shadow-purple-900/20">Carica Media</button>
                        </div>
                    </form>
                </section>

                <!-- Scan Existing Files -->
                <section class="glass rounded-2xl p-6">
                    <h2 class="text-xl font-bold mb-6 flex items-center gap-2 text-yellow-400">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        Scansiona File Esistenti
                    </h2>
                    <p class="text-slate-400 text-sm mb-4">Cerca file nella cartella media che non sono ancora registrati nel database</p>
                    <div class="flex gap-3">
                        <button onclick="scanMediaDirectory()" class="px-6 py-3 bg-yellow-600 hover:bg-yellow-500 rounded-xl font-bold transition-all shadow-lg shadow-yellow-900/20">Scansiona</button>
                        <button onclick="registerScannedFiles()" id="register-btn" class="px-6 py-3 bg-green-600 hover:bg-green-500 rounded-xl font-bold transition-all shadow-lg shadow-green-900/20 hidden">Registra Selezionati</button>
                    </div>
                    <div id="scanned-files" class="mt-4 space-y-2 hidden">
                        <!-- Scanned files will appear here -->
                    </div>
                </section>

                <!-- Media Library List -->
                <section class="glass rounded-2xl p-6">
                    <h2 class="text-xl font-bold mb-6 text-purple-400">Media Library</h2>
                    <div id="media-library-list" class="space-y-2">
                        <!-- Loaded via JS -->
                    </div>
                </section>
            </div>

            <!-- Schermi Tab -->
            <div id="tab-schermi" class="tab-content space-y-6 hidden">
                <section class="glass rounded-2xl p-6">
                    <h2 class="text-xl font-bold mb-6 flex items-center gap-2 text-yellow-400">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                        Configurazione Schermi
                    </h2>
                    <div id="screens-list" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Loaded via JS -->
                    </div>
                </section>
            </div>

            <!-- Note Tab -->
            <div id="tab-note" class="tab-content space-y-6 hidden">
                <section class="glass rounded-2xl p-6">
                    <h2 class="text-xl font-bold mb-6 flex items-center gap-2 text-red-400">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                        Note Tecniche
                    </h2>
                    <form id="note-form" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="text-xs font-bold uppercase text-slate-500">Talento</label>
                            <select name="talento_id" id="note-talento-select" class="w-full bg-slate-800 border border-slate-700 rounded-xl px-4 py-3 focus:outline-none focus:border-blue-500 transition-colors">
                                <!-- Loaded via JS -->
                            </select>
                        </div>
                        <div class="space-y-2">
                            <label class="text-xs font-bold uppercase text-slate-500">Tipo Nota</label>
                            <select name="tipo" class="w-full bg-slate-800 border border-slate-700 rounded-xl px-4 py-3 focus:outline-none focus:border-blue-500 transition-colors">
                                <option value="materiale_palco">Materiale Palco</option>
                                <option value="luci">Gestione Luci</option>
                                <option value="generiche">Note Generali</option>
                                <option value="pause">PAUSE</option>
                            </select>
                        </div>
                        <div class="md:col-span-2 space-y-2">
                            <label class="text-xs font-bold uppercase text-slate-500">Contenuto</label>
                            <textarea name="contenuto" rows="4" required class="w-full bg-slate-800 border border-slate-700 rounded-xl px-4 py-3 focus:outline-none focus:border-blue-500 transition-colors"></textarea>
                        </div>
                        <div class="md:col-span-2 flex justify-end">
                            <button type="submit" class="px-8 py-3 bg-red-600 hover:bg-red-500 rounded-xl font-bold transition-all shadow-lg shadow-red-900/20">
                                Salva Nota
                            </button>
                        </div>
                    </form>
                </section>

                <!-- Notes List -->
                <section class="glass rounded-2xl p-6">
                    <h2 class="text-xl font-bold mb-6 text-red-400">Note Registrate</h2>
                    <div id="notes-list" class="space-y-2">
                        <!-- Loaded via JS -->
                    </div>
                </section>
            </div>
        </main>
    </div>

    <script>
        // Tab Navigation
        function showTab(tabName) {
            document.querySelectorAll('.tab-content').forEach(tab => tab.classList.add('hidden'));
            document.querySelectorAll('.tab-btn').forEach(btn => {
                btn.classList.remove('bg-blue-600', 'text-white');
                btn.classList.add('bg-slate-800');
            });
            
            document.getElementById(`tab-${tabName}`).classList.remove('hidden');
            document.querySelector(`[data-tab="${tabName}"]`).classList.remove('bg-slate-800');
            document.querySelector(`[data-tab="${tabName}"]`).classList.add('bg-blue-600', 'text-white');
        }

        // Slot
        const API_BASE = '/api/talenti';

        async function fetchTalenti() {
            try {
                const response = await fetch(API_BASE);
                const result = await response.json();
                if (result.status === 'ok') {
                    renderSlotList(result.data);
                    populateSlotSelects(result.data);
                }
            } catch (error) {
                console.error("Admin: Errore nel recupero slot:", error);
            }
        }

        function renderSlotList(slots) {
            const list = document.getElementById('talent-list');
            list.innerHTML = '';
            slots.forEach((slot, index) => {
                const row = document.createElement('tr');
                row.className = 'group hover:bg-slate-800/30 transition-colors';
                row.innerHTML = `
                    <td class="py-4 pl-4 font-mono text-blue-400">${index + 1}</td>
                    <td class="py-4 font-bold">${slot.nome}</td>
                    <td class="py-4"><span class="px-2 py-0.5 rounded text-[10px] bg-blue-500/20 text-blue-400 border border-blue-500/30">${slot.categoria}</span></td>
                    <td class="py-4 text-right pr-4 flex justify-end gap-2">
                        <button onclick="moveSlot(${index}, 'up')" class="p-2 hover:bg-slate-700 rounded-lg text-slate-400 hover:text-white transition-colors" ${index === 0 ? 'disabled opacity-20' : ''}>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path></svg>
                        </button>
                        <button onclick="moveSlot(${index}, 'down')" class="p-2 hover:bg-slate-700 rounded-lg text-slate-400 hover:text-white transition-colors" ${index === slots.length - 1 ? 'disabled opacity-20' : ''}>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                        <button onclick="deleteSlot(${slot.id})" class="p-2 hover:bg-red-900/30 rounded-lg text-slate-400 hover:text-red-400 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                        </button>
                    </td>
                `;
                list.appendChild(row);
            });
            window.currentSlots = slots;
        }

        function populateSlotSelects(slots) {
            const selects = ['media-talento-select', 'note-talento-select'];
            selects.forEach(selectId => {
                const select = document.getElementById(selectId);
                if (select) {
                    select.innerHTML = '<option value="">Seleziona slot</option>';
                    slots.forEach(s => {
                        select.innerHTML += `<option value="${s.id}">${s.nome}</option>`;
                    });
                }
            });
        }

        function populateMediaLibrarySelect(media) {
            window.mediaLibraryData = media;
            renderMediaSelector(media);
        }

        function toggleMediaSelector() {
            const dropdown = document.getElementById('media-selector-dropdown');
            dropdown.classList.toggle('hidden');
        }

        function renderMediaSelector(media) {
            const list = document.getElementById('media-selector-list');
            list.innerHTML = '';

            media.forEach(m => {
                const item = document.createElement('div');
                item.className = 'flex items-center gap-4 p-3 hover:bg-slate-700/50 cursor-pointer transition-colors';
                item.onclick = () => selectMedia(m);

                let preview = '';
                if (m.file_type === 'FOTO') {
                    preview = `<img src="${m.file_path}" class="w-16 h-12 object-cover rounded-lg bg-slate-900" alt="">`;
                } else if (m.file_type === 'VIDEO') {
                    preview = `<video src="${m.file_path}" class="w-16 h-12 object-cover rounded-lg bg-slate-900" muted preload="metadata"></video>`;
                } else if (m.file_type === 'AUDIO') {
                    preview = `<div class="w-16 h-12 flex items-center justify-center rounded-lg bg-slate-900">
                        <svg class="w-8 h-8 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.536 8.464a5 5 0 010 7.072m2.828-9.9a9 9 0 010 12.728M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z"></path></svg>
                    </div>`;
                }

                item.innerHTML = `
                    ${preview}
                    <div class="flex-1 min-w-0">
                        <p class="font-semibold text-sm truncate">${m.file_name}</p>
                        <p class="text-xs text-slate-500">${m.file_type} • ${formatFileSize(m.file_size)}</p>
                    </div>
                `;
                list.appendChild(item);
            });
        }

        function selectMedia(media) {
            document.getElementById('media-library-id').value = media.id;
            document.getElementById('selected-media-name').textContent = media.file_name;
            document.getElementById('selected-media-name').classList.remove('text-slate-400');
            document.getElementById('selected-media-name').classList.add('text-white');
            document.getElementById('media-selector-dropdown').classList.add('hidden');
        }

        function filterMediaLibrary() {
            const search = document.getElementById('media-search').value.toLowerCase();
            const filtered = window.mediaLibraryData.filter(m =>
                m.file_name.toLowerCase().includes(search) ||
                m.file_type.toLowerCase().includes(search)
            );
            renderMediaSelector(filtered);
        }

        // Close dropdown when clicking outside
        document.addEventListener('click', (e) => {
            const selector = document.getElementById('media-selector-dropdown');
            const btn = document.getElementById('media-selector-btn');
            if (!selector.contains(e.target) && !btn.contains(e.target)) {
                selector.classList.add('hidden');
            }
        });

        function populateScreenSelect(screens) {
            const select = document.getElementById('media-screen-select');
            if (select) {
                select.innerHTML = '<option value="">Seleziona screen</option>';
                screens.forEach(s => {
                    select.innerHTML += `<option value="${s.id}">${s.nome}</option>`;
                });
            }
        }

        document.getElementById('talent-form').addEventListener('submit', async (e) => {
            e.preventDefault();
            const formData = new FormData(e.target);
            const data = Object.fromEntries(formData.entries());
            
            try {
                const response = await fetch(`${API_BASE}/aggiungi`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(data)
                });
                const result = await response.json();
                if (result.status === 'ok') {
                    fetchTalenti();
                    e.target.reset();
                }
            } catch (error) {
                console.error("Admin: Errore nel salvataggio:", error);
            }
        });

        async function moveSlot(index, direction) {
            const slots = window.currentSlots;
            if (direction === 'up' && index > 0) {
                [slots[index], slots[index - 1]] = [slots[index - 1], slots[index]];
            } else if (direction === 'down' && index < slots.length - 1) {
                [slots[index], slots[index + 1]] = [slots[index + 1], slots[index]];
            }
            
            const ids = slots.map(s => s.id);
            try {
                await fetch(`${API_BASE}/riordina`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ ids })
                });
                fetchTalenti();
            } catch (error) {
                console.error("Admin: Errore nel riordino:", error);
            }
        }

        async function deleteSlot(id) {
            if (!confirm('Sei sicuro di voler eliminare questo slot?')) return;
            try {
                const response = await fetch(`${API_BASE}/elimina?id=${id}`, { method: 'DELETE' });
                const result = await response.json();
                if (result.status === 'ok') {
                    fetchTalenti();
                }
            } catch (error) {
                console.error("Admin: Errore nell'eliminazione:", error);
            }
        }

        // Media
        document.getElementById('media-form').addEventListener('submit', async (e) => {
            e.preventDefault();
            const formData = new FormData(e.target);
            const data = Object.fromEntries(formData.entries());

            if (!data.media_library_id) {
                alert('Seleziona un media dalla library');
                return;
            }

            try {
                // Get media library file path
                const libraryResponse = await fetch('/api/media-library');
                const libraryResult = await libraryResponse.json();
                const selectedMedia = libraryResult.data.find(m => m.id == data.media_library_id);

                if (!selectedMedia) {
                    alert('Media non trovato');
                    return;
                }

                // Create media
                const mediaResponse = await fetch('/api/media', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        talento_id: data.talento_id,
                        tipo_output: data.tipo_output,
                        file_path: selectedMedia.file_path,
                        screen_id: data.screen_id,
                        tipo_media: data.tipo_media
                    })
                });
                const mediaResult = await mediaResponse.json();

                if (mediaResult.status === 'ok') {
                    // Create transition
                    await fetch('/api/transizioni/create', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({
                            media_id: mediaResult.id,
                            tipo_dissolvenza: data.tipo_dissolvenza,
                            durata_sec: data.durata_sec
                        })
                    });

                    fetchMedia();
                    e.target.reset();
                }
            } catch (error) {
                console.error("Admin: Errore nel salvataggio media:", error);
            }
        });

        async function fetchMedia() {
            try {
                const response = await fetch('/api/media');
                const result = await response.json();
                if (result.status === 'ok') {
                    renderMediaList(result.data);
                }
            } catch (error) {
                console.error("Admin: Errore nel recupero media:", error);
            }
        }

        function renderMediaList(media) {
            const list = document.getElementById('media-list');
            list.innerHTML = '';
            media.forEach(m => {
                const item = document.createElement('div');
                item.className = 'p-3 rounded-lg bg-slate-800/50 border border-slate-700 flex justify-between items-center';
                item.innerHTML = `
                    <div>
                        <span class="text-sm font-bold">${m.file_path}</span>
                        <span class="text-xs text-slate-500 ml-2">${m.tipo_media}</span>
                    </div>
                    <button onclick="deleteMedia(${m.id})" class="p-2 hover:bg-red-900/30 rounded-lg text-slate-400 hover:text-red-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                    </button>
                `;
                list.appendChild(item);
            });
        }

        async function deleteMedia(id) {
            if (!confirm('Sei sicuro di voler eliminare questo media?')) return;
            try {
                await fetch(`/api/media?id=${id}`, { method: 'DELETE' });
                fetchMedia();
            } catch (error) {
                console.error("Admin: Errore nell'eliminazione media:", error);
            }
        }

        // Screens
        async function fetchScreens() {
            try {
                const response = await fetch('/api/screens');
                const result = await response.json();
                if (result.status === 'ok') {
                    renderScreensList(result.data);
                    populateScreenSelect(result.data);
                }
            } catch (error) {
                console.error("Admin: Errore nel recupero schermi:", error);
            }
        }

        function renderScreensList(screens) {
            const list = document.getElementById('screens-list');
            list.innerHTML = '';
            screens.forEach(screen => {
                const item = document.createElement('div');
                item.className = 'p-4 rounded-xl bg-slate-800/50 border border-slate-700';
                item.innerHTML = `
                    <div class="flex justify-between items-center mb-3">
                        <h3 class="font-bold text-yellow-400">${screen.nome}</h3>
                        <div class="flex items-center gap-2">
                            <span class="text-xs bg-yellow-500/20 text-yellow-400 px-2 py-0.5 rounded">${screen.tipo}</span>
                            <button onclick="deleteScreen(${screen.id})" class="p-2 hover:bg-red-900/30 rounded-lg text-slate-400 hover:text-red-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            </button>
                        </div>
                    </div>
                    <select onchange="updateScreen(${screen.id}, this.value)" class="w-full bg-slate-700 border border-slate-600 rounded px-3 py-2 text-sm">
                        <option value="indipendente" ${screen.tipo === 'indipendente' ? 'selected' : ''}>Indipendente</option>
                        <option value="mirror" ${screen.tipo === 'mirror' ? 'selected' : ''}>Mirror</option>
                    </select>
                `;
                list.appendChild(item);
            });
        }

        async function updateScreen(id, tipo) {
            try {
                await fetch(`/api/screens/update?id=${id}`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ tipo })
                });
            } catch (error) {
                console.error("Admin: Errore nell'aggiornamento schermo:", error);
            }
        }

        async function deleteScreen(id) {
            if (!confirm('Sei sicuro di voler eliminare questo schermo?')) return;
            try {
                await fetch(`/api/screens/delete?id=${id}`, { method: 'DELETE' });
                fetchScreens();
            } catch (error) {
                console.error("Admin: Errore nell'eliminazione schermo:", error);
            }
        }

        // Notes
        document.getElementById('note-form').addEventListener('submit', async (e) => {
            e.preventDefault();
            const formData = new FormData(e.target);
            const data = Object.fromEntries(formData.entries());
            
            try {
                const response = await fetch('/api/notes/create', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(data)
                });
                const result = await response.json();
                if (result.status === 'ok') {
                    fetchNotes();
                    e.target.reset();
                }
            } catch (error) {
                console.error("Admin: Errore nel salvataggio nota:", error);
            }
        });

        async function fetchNotes() {
            try {
                const response = await fetch('/api/notes');
                const result = await response.json();
                if (result.status === 'ok') {
                    renderNotesList(result.data);
                }
            } catch (error) {
                console.error("Admin: Errore nel recupero note:", error);
            }
        }

        function renderNotesList(notes) {
            const list = document.getElementById('notes-list');
            list.innerHTML = '';
            notes.forEach(note => {
                const item = document.createElement('div');
                item.className = 'p-3 rounded-lg bg-slate-800/50 border border-slate-700';
                const tipoColors = {
                    'materiale_palco': 'text-blue-400',
                    'luci': 'text-green-400',
                    'generiche': 'text-purple-400',
                    'pause': 'text-yellow-400'
                };
                item.innerHTML = `
                    <div class="flex justify-between items-start">
                        <div>
                            <span class="text-xs font-bold ${tipoColors[note.tipo] || 'text-slate-400'} uppercase">${note.tipo}</span>
                            <span class="text-xs text-slate-500 ml-2">${note.talento_nome || 'Generale'}</span>
                            <p class="text-sm mt-1">${note.contenuto}</p>
                        </div>
                        <button onclick="deleteNote(${note.id})" class="p-2 hover:bg-red-900/30 rounded-lg text-slate-400 hover:text-red-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                        </button>
                    </div>
                `;
                list.appendChild(item);
            });
        }

        async function deleteNote(id) {
            if (!confirm('Sei sicuro di voler eliminare questa nota?')) return;
            try {
                await fetch(`/api/notes/delete?id=${id}`, { method: 'DELETE' });
                fetchNotes();
            } catch (error) {
                console.error("Admin: Errore nell'eliminazione nota:", error);
            }
        }

        // Media Library
        let scannedFilesData = [];

        function handleFileSelect(input) {
            const fileInfo = document.getElementById('file-info');
            const fileName = document.getElementById('selected-file-name');
            if (input.files && input.files[0]) {
                fileName.textContent = input.files[0].name;
                fileInfo.classList.remove('hidden');
            }
        }

        function resetUploadForm() {
            document.getElementById('upload-form').reset();
            document.getElementById('file-info').classList.add('hidden');
        }

        document.getElementById('upload-form').addEventListener('submit', async (e) => {
            e.preventDefault();
            const fileInput = document.getElementById('media-file');
            if (!fileInput.files || !fileInput.files[0]) {
                alert('Seleziona un file da caricare');
                return;
            }

            const formData = new FormData();
            formData.append('file', fileInput.files[0]);

            try {
                const response = await fetch('/api/media-library/upload', {
                    method: 'POST',
                    body: formData
                });
                const result = await response.json();
                if (result.status === 'ok') {
                    fetchMediaLibrary();
                    resetUploadForm();
                    alert('Media caricato con successo!');
                } else {
                    alert('Errore nel caricamento: ' + result.message);
                }
            } catch (error) {
                console.error("Admin: Errore nel caricamento media:", error);
                alert('Errore nel caricamento media');
            }
        });

        async function fetchMediaLibrary() {
            try {
                const response = await fetch('/api/media-library');
                const result = await response.json();
                if (result.status === 'ok') {
                    renderMediaLibrary(result.data);
                    populateMediaLibrarySelect(result.data);
                }
            } catch (error) {
                console.error("Admin: Errore nel recupero media library:", error);
            }
        }

        function renderMediaLibrary(media) {
            const list = document.getElementById('media-library-list');
            list.innerHTML = '';
            media.forEach(m => {
                const item = document.createElement('div');
                item.className = 'flex items-center justify-between p-4 bg-slate-800/50 rounded-xl group hover:bg-slate-800 transition-colors';
                item.innerHTML = `
                    <div class="flex items-center gap-4">
                        <span class="px-2 py-0.5 rounded text-[10px] ${m.file_type === 'VIDEO' ? 'bg-red-500/20 text-red-400' : m.file_type === 'FOTO' ? 'bg-green-500/20 text-green-400' : 'bg-blue-500/20 text-blue-400'} border border-current">${m.file_type}</span>
                        <div>
                            <p class="font-semibold">${m.file_name}</p>
                            <p class="text-xs text-slate-500">${m.file_path}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="text-xs text-slate-500">${formatFileSize(m.file_size)}</span>
                        <button onclick="deleteMediaLibrary(${m.id})" class="p-2 hover:bg-red-900/30 rounded-lg text-slate-400 hover:text-red-400 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                        </button>
                    </div>
                `;
                list.appendChild(item);
            });
        }

        function formatFileSize(bytes) {
            if (!bytes) return 'N/A';
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(1024));
            return Math.round(bytes / Math.pow(1024, i) * 100) / 100 + ' ' + sizes[i];
        }

        async function deleteMediaLibrary(id) {
            if (!confirm('Sei sicuro di voler eliminare questo media?')) return;
            try {
                await fetch(`/api/media-library/delete?id=${id}`, { method: 'DELETE' });
                fetchMediaLibrary();
            } catch (error) {
                console.error("Admin: Errore nell'eliminazione media:", error);
            }
        }

        async function scanMediaDirectory() {
            try {
                const response = await fetch('/api/media-library/scan');
                const result = await response.json();
                if (result.status === 'ok') {
                    scannedFilesData = result.data;
                    renderScannedFiles(result.data);
                }
            } catch (error) {
                console.error("Admin: Errore nella scansione:", error);
            }
        }

        function renderScannedFiles(files) {
            const container = document.getElementById('scanned-files');
            const registerBtn = document.getElementById('register-btn');
            
            if (files.length === 0) {
                container.innerHTML = '<p class="text-slate-500 text-sm">Nessun file non registrato trovato</p>';
                container.classList.remove('hidden');
                registerBtn.classList.add('hidden');
                return;
            }

            container.classList.remove('hidden');
            registerBtn.classList.remove('hidden');
            container.innerHTML = '';

            files.forEach((file, index) => {
                const item = document.createElement('div');
                item.className = 'flex items-center justify-between p-3 bg-slate-800/50 rounded-lg';
                item.innerHTML = `
                    <div class="flex items-center gap-3">
                        <input type="checkbox" id="scan-${index}" class="w-4 h-4 rounded" checked>
                        <span class="px-2 py-0.5 rounded text-[10px] ${file.file_type === 'VIDEO' ? 'bg-red-500/20 text-red-400' : file.file_type === 'FOTO' ? 'bg-green-500/20 text-green-400' : 'bg-blue-500/20 text-blue-400'} border border-current">${file.file_type}</span>
                        <span class="text-sm">${file.file_name}</span>
                    </div>
                    <span class="text-xs text-slate-500">${formatFileSize(file.file_size)}</span>
                `;
                container.appendChild(item);
            });
        }

        async function registerScannedFiles() {
            const selectedFiles = [];
            scannedFilesData.forEach((file, index) => {
                const checkbox = document.getElementById(`scan-${index}`);
                if (checkbox && checkbox.checked) {
                    selectedFiles.push(file);
                }
            });

            if (selectedFiles.length === 0) {
                alert('Seleziona almeno un file da registrare');
                return;
            }

            try {
                const response = await fetch('/api/media-library/register', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ files: selectedFiles })
                });
                const result = await response.json();
                if (result.status === 'ok') {
                    fetchMediaLibrary();
                    document.getElementById('scanned-files').classList.add('hidden');
                    document.getElementById('register-btn').classList.add('hidden');
                    alert(`${result.data.length} file registrati con successo!`);
                }
            } catch (error) {
                console.error("Admin: Errore nella registrazione:", error);
            }
        }

        // Initial load
        fetchTalenti();
        fetchScreens();
        fetchNotes();
        fetchMediaLibrary();
    </script>
</body>
</html>
