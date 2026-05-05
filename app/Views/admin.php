<!DOCTYPE html>
<html lang="it" class="h-full bg-slate-900 border-slate-800">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Amministrazione - Olmo's Got Talent</title>
    <?php require BASE_PATH . '/public/commons/favicon.php'; ?>
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
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                    <span>Dashboard</span>
                </a>
            </div>
        </header>

        <!-- Main Content -->
        <main class="flex-1 max-w-7xl mx-auto w-full p-6 space-y-8">
            <!-- Tabs -->
            <div class="flex gap-2 border-b border-slate-700/50 pb-4 flex-wrap">
                <button onclick="showTab('slot')" class="tab-btn px-4 py-2 rounded-lg bg-blue-600 text-white font-semibold text-sm card-hover" data-tab="slot" data-tooltip="Gestisci i talenti e la scaletta">
                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    Slot
                </button>
                <button onclick="showTab('media')" class="tab-btn px-4 py-2 rounded-lg bg-slate-800 hover:bg-slate-700 font-semibold text-sm card-hover" data-tab="media" data-tooltip="Associa media ai talenti">
                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M7 4v16M17 4v16M3 8h4m10 0h4M3 12h18M3 16h4m10 0h4M4 20h16a1 1 0 001-1V5a1 1 0 00-1-1H4a1 1 0 00-1 1v14a1 1 0 001 1z"></path></svg>
                    Media
                </button>
                <button onclick="showTab('media-library')" class="tab-btn px-4 py-2 rounded-lg bg-slate-800 hover:bg-slate-700 font-semibold text-sm card-hover" data-tab="media-library" data-tooltip="Carica e gestisci i file media">
                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                    Media Library
                </button>
                <button onclick="showTab('schermi')" class="tab-btn px-4 py-2 rounded-lg bg-slate-800 hover:bg-slate-700 font-semibold text-sm card-hover" data-tab="schermi" data-tooltip="Configura gli schermi di output">
                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                    Schermi
                </button>
                <button onclick="showTab('note')" class="tab-btn px-4 py-2 rounded-lg bg-slate-800 hover:bg-slate-700 font-semibold text-sm card-hover" data-tab="note" data-tooltip="Note tecniche e materiale palco">
                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    Note Tecniche
                </button>
            </div>

            <!-- Slot Tab -->
            <div id="tab-slot" class="tab-content space-y-6">
                <!-- Form Aggiunta Slot -->
                <section class="glass glass-blue rounded-2xl p-6">
                    <h2 class="text-xl font-bold mb-6 flex items-center gap-2 text-blue-400">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
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
                <section class="glass glass-purple rounded-2xl p-6">
                    <h2 class="text-xl font-bold mb-6 flex items-center gap-2 text-purple-400">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path></svg>
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
                <section class="glass glass-green rounded-2xl p-6">
                    <div class="flex items-center justify-between gap-4 mb-6">
                        <h2 class="text-xl font-bold flex items-center gap-2 text-green-400">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                            Gestione Media
                        </h2>
                        <button type="button" onclick="openMediaWizard()" class="px-5 py-2.5 bg-green-600 hover:bg-green-500 rounded-xl font-bold transition-all">
                            Aggiungi guidato
                        </button>
                    </div>
                    <details class="rounded-xl border border-slate-800 bg-slate-900/50 p-4">
                        <summary class="cursor-pointer text-sm font-bold text-slate-400">Opzioni avanzate manuali</summary>
                    <form id="media-form" class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-5">
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
                                    <svg class="w-5 h-5 text-slate-500" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"></path></svg>
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
                    </details>
                </section>

                <!-- Media List -->
                <section class="glass glass-green rounded-2xl p-6">
                    <h2 class="text-xl font-bold mb-6 text-green-400">Media Registrati</h2>
                    <div id="media-list" class="space-y-2">
                        <!-- Loaded via JS -->
                    </div>
                </section>
            </div>

            <!-- Media Library Tab -->
            <div id="tab-media-library" class="tab-content space-y-6 hidden">
                <!-- Upload Section -->
                <section class="glass glass-purple rounded-2xl p-6">
                    <h2 class="text-xl font-bold mb-6 flex items-center gap-2 text-purple-400">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                        Carica Nuovo Media
                    </h2>
                    <form id="upload-form" class="space-y-4">
                        <div class="border-2 border-dashed border-slate-700 rounded-xl p-8 text-center hover:border-purple-500 transition-colors">
                            <input type="file" id="media-file" accept="image/*,video/*,audio/*" class="hidden" onchange="handleFileSelect(this)">
                            <label for="media-file" class="cursor-pointer">
                                <svg class="w-12 h-12 mx-auto text-slate-500 mb-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg>
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
                <section class="glass glass-yellow rounded-2xl p-6">
                    <h2 class="text-xl font-bold mb-6 flex items-center gap-2 text-yellow-400">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
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
                <section class="glass glass-purple rounded-2xl p-6">
                    <h2 class="text-xl font-bold mb-6 text-purple-400">Media Library</h2>
                    <div id="media-library-list" class="space-y-2">
                        <!-- Loaded via JS -->
                    </div>
                </section>
            </div>

            <!-- Schermi Tab -->
            <div id="tab-schermi" class="tab-content space-y-6 hidden">
                <section class="glass glass-yellow rounded-2xl p-6">
                    <h2 class="text-xl font-bold mb-6 flex items-center gap-2 text-yellow-400">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                        Configurazione Schermi
                    </h2>
                    <div id="screens-list" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Loaded via JS -->
                    </div>
                </section>
            </div>

            <!-- Note Tab -->
            <div id="tab-note" class="tab-content space-y-6 hidden">
                <section class="glass glass-red rounded-2xl p-6">
                    <h2 class="text-xl font-bold mb-6 flex items-center gap-2 text-red-400">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
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
                <section class="glass glass-red rounded-2xl p-6">
                    <h2 class="text-xl font-bold mb-6 text-red-400">Note Registrate</h2>
                    <div id="notes-list" class="space-y-2">
                        <!-- Loaded via JS -->
                    </div>
                </section>
            </div>
        </main>
    </div>

    <div id="media-wizard-modal" class="fixed inset-0 z-[100] hidden">
        <div class="absolute inset-0 bg-black/70" onclick="closeMediaWizard()"></div>
        <div class="relative max-w-4xl mx-auto mt-8 bg-slate-950 border border-slate-700 rounded-2xl shadow-2xl overflow-hidden">
            <div class="p-5 border-b border-slate-800 flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-bold text-green-400">Aggiungi media allo slot</h2>
                    <p id="wizard-subtitle" class="text-sm text-slate-500 mt-1"></p>
                </div>
                <button onclick="closeMediaWizard()" class="p-2 hover:bg-slate-800 rounded-lg text-slate-400 hover:text-white">Chiudi</button>
            </div>
            <div class="p-5 border-b border-slate-800">
                <div class="grid grid-cols-4 gap-2 text-xs font-bold uppercase">
                    <div id="wizard-step-label-1" class="wizard-step-label rounded-lg px-3 py-2 bg-green-600 text-white">1 Slot</div>
                    <div id="wizard-step-label-2" class="wizard-step-label rounded-lg px-3 py-2 bg-slate-800 text-slate-400">2 Media</div>
                    <div id="wizard-step-label-3" class="wizard-step-label rounded-lg px-3 py-2 bg-slate-800 text-slate-400">3 Destinazione</div>
                    <div id="wizard-step-label-4" class="wizard-step-label rounded-lg px-3 py-2 bg-slate-800 text-slate-400">4 Conferma</div>
                </div>
            </div>
            <div class="p-5 min-h-[380px]">
                <section id="wizard-step-1" class="wizard-step space-y-4">
                    <label class="block text-xs font-bold uppercase text-slate-500">Slot</label>
                    <select id="wizard-slot-select" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-3 focus:outline-none focus:border-green-500"></select>
                </section>
                <section id="wizard-step-2" class="wizard-step hidden space-y-5">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <button type="button" onclick="setWizardSource('library')" id="wizard-source-library" class="wizard-source p-4 rounded-xl border border-green-500 bg-green-500/10 text-left">
                            <span class="block font-bold text-green-400">Scegli dalla library</span>
                            <span class="text-sm text-slate-500">Usa un file già registrato</span>
                        </button>
                        <button type="button" onclick="setWizardSource('upload')" id="wizard-source-upload" class="wizard-source p-4 rounded-xl border border-slate-700 bg-slate-900 text-left">
                            <span class="block font-bold text-purple-400">Carica nuovo file</span>
                            <span class="text-sm text-slate-500">Upload e uso immediato</span>
                        </button>
                    </div>
                    <div id="wizard-library-panel" class="space-y-3">
                        <input type="text" id="wizard-media-search" placeholder="Cerca nella media library..." class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-3 focus:outline-none focus:border-green-500" oninput="renderWizardMediaList()">
                        <div id="wizard-media-list" class="grid grid-cols-1 md:grid-cols-2 gap-3 max-h-56 overflow-y-auto"></div>
                    </div>
                    <div id="wizard-upload-panel" class="hidden space-y-3">
                        <input type="file" id="wizard-file-input" accept="image/*,video/*,audio/*" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-3">
                        <p class="text-sm text-slate-500">Il file verrà caricato nella media library e selezionato automaticamente.</p>
                    </div>
                </section>
                <section id="wizard-step-3" class="wizard-step hidden space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold uppercase text-slate-500 mb-2">Schermo</label>
                            <select id="wizard-screen-select" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-3 focus:outline-none focus:border-green-500"></select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase text-slate-500 mb-2">Tipo media</label>
                            <select id="wizard-type-select" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-3 focus:outline-none focus:border-green-500">
                                <option value="VIDEO">Video</option>
                                <option value="AUDIO">Audio</option>
                                <option value="FOTO">Foto</option>
                            </select>
                        </div>
                    </div>
                </section>
                <section id="wizard-step-4" class="wizard-step hidden space-y-4">
                    <div id="wizard-duplicate-warning" class="hidden p-4 rounded-xl border border-yellow-500/40 bg-yellow-500/10 text-yellow-200 text-sm">
                        Questo media è già associato allo slot. Confermando verrà aggiunta una seconda occorrenza.
                    </div>
                    <div id="wizard-summary" class="grid grid-cols-1 md:grid-cols-2 gap-3"></div>
                    <p class="text-sm text-slate-500">Salvando, il media verrà aggiunto allo slot, alla coda come pending e allo schermo selezionato.</p>
                </section>
            </div>
            <div class="p-5 border-t border-slate-800 flex items-center justify-between">
                <button onclick="prevWizardStep()" id="wizard-back-btn" class="px-5 py-2.5 bg-slate-800 hover:bg-slate-700 rounded-xl font-semibold hidden">Indietro</button>
                <div class="ml-auto flex gap-3">
                    <button onclick="closeMediaWizard()" class="px-5 py-2.5 bg-slate-800 hover:bg-slate-700 rounded-xl font-semibold">Annulla</button>
                    <button onclick="nextWizardStep()" id="wizard-next-btn" class="px-5 py-2.5 bg-green-600 hover:bg-green-500 rounded-xl font-bold">Avanti</button>
                    <button onclick="submitMediaWizard(false)" id="wizard-save-btn" class="px-5 py-2.5 bg-green-600 hover:bg-green-500 rounded-xl font-bold hidden">Salva</button>
                </div>
            </div>
        </div>
    </div>

    <div id="confirm-modal" class="fixed inset-0 z-[120] hidden">
        <div class="absolute inset-0 bg-black/70"></div>
        <div class="relative max-w-lg mx-auto mt-24 bg-slate-950 border border-slate-700 rounded-2xl shadow-2xl overflow-hidden">
            <div class="p-5 border-b border-slate-800">
                <h2 id="confirm-title" class="text-xl font-bold text-red-400">Conferma</h2>
                <p id="confirm-message" class="text-sm text-slate-400 mt-2"></p>
            </div>
            <div id="confirm-details" class="px-5 py-4 text-sm text-slate-300 max-h-48 overflow-y-auto"></div>
            <div class="p-5 border-t border-slate-800 flex justify-end gap-3">
                <button onclick="resolveConfirm(false)" class="px-5 py-2.5 bg-slate-800 hover:bg-slate-700 rounded-xl font-semibold">Annulla</button>
                <button id="confirm-action-btn" onclick="resolveConfirm(true)" class="px-5 py-2.5 bg-red-600 hover:bg-red-500 rounded-xl font-bold">Elimina</button>
            </div>
        </div>
    </div>

    <div id="toast" class="fixed right-5 bottom-5 z-[130] hidden rounded-xl border border-slate-700 bg-slate-950 px-4 py-3 text-sm shadow-2xl"></div>

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

        let confirmResolver = null;
        const selectedItems = {
            slots: new Set(),
            media: new Set(),
            screens: new Set(),
            notes: new Set(),
            library: new Set()
        };

        function showToast(message, type = 'info') {
            const toast = document.getElementById('toast');
            toast.textContent = message;
            toast.className = `fixed right-5 bottom-5 z-[130] rounded-xl border px-4 py-3 text-sm shadow-2xl ${
                type === 'error'
                    ? 'border-red-500/40 bg-red-950 text-red-100'
                    : type === 'success'
                        ? 'border-green-500/40 bg-green-950 text-green-100'
                        : 'border-slate-700 bg-slate-950 text-slate-100'
            }`;
            toast.classList.remove('hidden');
            clearTimeout(window.toastTimeout);
            window.toastTimeout = setTimeout(() => toast.classList.add('hidden'), 3500);
        }

        function showConfirm({ title, message, details = '', actionLabel = 'Elimina' }) {
            document.getElementById('confirm-title').textContent = title;
            document.getElementById('confirm-message').textContent = message;
            document.getElementById('confirm-details').innerHTML = details;
            document.getElementById('confirm-action-btn').textContent = actionLabel;
            document.getElementById('confirm-modal').classList.remove('hidden');
            return new Promise(resolve => {
                confirmResolver = resolve;
            });
        }

        function resolveConfirm(value) {
            document.getElementById('confirm-modal').classList.add('hidden');
            if (confirmResolver) confirmResolver(value);
            confirmResolver = null;
        }

        function setSelected(group, id, checked) {
            const set = selectedItems[group];
            const value = String(id);
            if (checked) {
                set.add(value);
            } else {
                set.delete(value);
            }
            updateBulkActions(group);
        }

        function clearSelection(group) {
            selectedItems[group].clear();
            updateBulkActions(group);
        }

        function updateBulkActions(group) {
            const bar = document.getElementById(`${group}-bulk-actions`);
            const count = document.getElementById(`${group}-selected-count`);
            if (!bar || !count) return;
            const size = selectedItems[group].size;
            count.textContent = `${size} selezionati`;
            bar.classList.toggle('hidden', size === 0);
        }

        function bulkActionBar(group, label) {
            return `<div id="${group}-bulk-actions" class="hidden mb-4 flex flex-wrap items-center justify-between gap-3 rounded-xl border border-red-500/30 bg-red-500/10 px-4 py-3">
                <span id="${group}-selected-count" class="text-sm font-semibold text-red-100">0 selezionati</span>
                <div class="flex gap-2">
                    <button type="button" onclick="clearSelection('${group}'); rerenderCurrentLists()" class="px-3 py-2 bg-slate-800 hover:bg-slate-700 rounded-lg text-sm font-semibold">Deseleziona</button>
                    <button type="button" onclick="deleteSelected('${group}')" class="px-3 py-2 bg-red-600 hover:bg-red-500 rounded-lg text-sm font-bold">Elimina ${label}</button>
                </div>
            </div>`;
        }

        function rerenderCurrentLists() {
            if (window.currentSlots) renderSlotList(window.currentSlots);
            if (window.currentMedia) renderMediaList(window.currentMedia);
            if (window.currentScreens) renderScreensList(window.currentScreens);
            if (window.currentNotes) renderNotesList(window.currentNotes);
            if (window.mediaLibraryData) renderMediaLibrary(window.mediaLibraryData);
        }

        function getSelectedRecords(group) {
            const selected = selectedItems[group];
            const sources = {
                slots: window.currentSlots || [],
                media: window.currentMedia || [],
                screens: window.currentScreens || [],
                notes: window.currentNotes || [],
                library: window.mediaLibraryData || []
            };
            return sources[group].filter(item => selected.has(String(item.id)));
        }

        function itemLabel(group, item) {
            if (group === 'slots') return item.nome;
            if (group === 'media') return item.file_path;
            if (group === 'screens') return item.nome;
            if (group === 'notes') return `${item.tipo}: ${item.contenuto}`;
            if (group === 'library') return item.file_name;
            return item.id;
        }

        async function deleteSelected(group) {
            const records = getSelectedRecords(group);
            if (records.length === 0) {
                showToast('Nessun elemento selezionato', 'error');
                return;
            }
            const details = `<ul class="space-y-1">${records.map(item => `<li>- ${escapeHtml(itemLabel(group, item))}</li>`).join('')}</ul>`;
            const confirmed = await showConfirm({
                title: `Elimina ${records.length} elementi`,
                message: 'Questa azione non puo essere annullata.',
                details
            });
            if (!confirmed) return;

            const endpoints = {
                slots: id => `${API_BASE}/elimina?id=${id}`,
                media: id => `/api/media?id=${id}`,
                screens: id => `/api/screens/delete?id=${id}`,
                notes: id => `/api/notes/delete?id=${id}`,
                library: id => `/api/media-library/delete?id=${id}`
            };
            try {
                await Promise.all(records.map(item => fetch(endpoints[group](item.id), { method: 'DELETE' })));
                clearSelection(group);
                await refreshGroup(group);
                showToast(`${records.length} elementi eliminati`, 'success');
            } catch (error) {
                console.error("Admin: Errore eliminazione multipla:", error);
                showToast('Errore durante eliminazione', 'error');
            }
        }

        async function refreshGroup(group) {
            if (group === 'slots') return fetchTalenti();
            if (group === 'media') return fetchMedia();
            if (group === 'screens') return fetchScreens();
            if (group === 'notes') return fetchNotes();
            if (group === 'library') return fetchMediaLibrary();
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
            if (!document.getElementById('slots-bulk-actions')) {
                list.closest('section').insertAdjacentHTML('afterbegin', bulkActionBar('slots', 'slot'));
            }
            slots.forEach((slot, index) => {
                const row = document.createElement('tr');
                row.className = 'group hover:bg-slate-800/30 transition-colors';
                row.innerHTML = `
                    <td class="py-4 pl-4 font-mono text-blue-400">
                        <input type="checkbox" class="mr-3" ${selectedItems.slots.has(String(slot.id)) ? 'checked' : ''} onchange="setSelected('slots', ${slot.id}, this.checked)">
                        ${index + 1}
                    </td>
                    <td class="py-4 font-bold">${slot.nome}</td>
                    <td class="py-4"><span class="px-2 py-0.5 rounded text-[10px] bg-blue-500/20 text-blue-400 border border-blue-500/30">${slot.categoria}</span></td>
                    <td class="py-4 text-right pr-4 flex justify-end gap-2">
                        <button onclick="moveSlot(${index}, 'up')" class="p-2 hover:bg-slate-700 rounded-lg text-slate-400 hover:text-white transition-colors" ${index === 0 ? 'disabled opacity-20' : ''}>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path></svg>
                        </button>
                        <button onclick="moveSlot(${index}, 'down')" class="p-2 hover:bg-slate-700 rounded-lg text-slate-400 hover:text-white transition-colors" ${index === slots.length - 1 ? 'disabled opacity-20' : ''}>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                        <button onclick="openMediaWizard(${slot.id})" class="px-3 py-2 bg-green-600 hover:bg-green-500 rounded-lg text-xs font-bold text-white transition-colors">
                            Media
                        </button>
                        <button onclick="deleteSlot(${slot.id})" class="p-2 hover:bg-red-900/30 rounded-lg text-slate-400 hover:text-red-400 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                        </button>
                    </td>
                `;
                list.appendChild(row);
            });
            window.currentSlots = slots;
            updateBulkActions('slots');
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
            window.currentScreens = screens;
            const select = document.getElementById('media-screen-select');
            if (select) {
                select.innerHTML = '<option value="">Seleziona screen</option>';
                screens.forEach(s => {
                    select.innerHTML += `<option value="${s.id}">${s.nome}</option>`;
                });
            }
        }

        const mediaWizard = {
            step: 1,
            source: 'library',
            selectedMedia: null,
            duplicate: false
        };

        function escapeHtml(value) {
            return String(value ?? '').replace(/[&<>"']/g, char => ({
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            }[char]));
        }

        async function openMediaWizard(slotId = null) {
            mediaWizard.step = 1;
            mediaWizard.source = 'library';
            mediaWizard.selectedMedia = null;
            mediaWizard.duplicate = false;

            await Promise.all([
                window.mediaLibraryData ? Promise.resolve() : fetchMediaLibrary(),
                window.currentScreens ? Promise.resolve() : fetchScreens()
            ]);
            populateWizardSlotSelect(slotId);
            populateWizardScreenSelect();
            setWizardSource('library');
            document.getElementById('wizard-media-search').value = '';
            renderWizardMediaList();
            document.getElementById('wizard-file-input').value = '';
            document.getElementById('wizard-save-btn').onclick = () => submitMediaWizard(false);
            document.getElementById('wizard-save-btn').textContent = 'Salva';
            document.getElementById('media-wizard-modal').classList.remove('hidden');
            setWizardStep(1);
        }

        function closeMediaWizard() {
            document.getElementById('media-wizard-modal').classList.add('hidden');
        }

        function populateWizardSlotSelect(slotId = null) {
            const select = document.getElementById('wizard-slot-select');
            const slots = window.currentSlots || [];
            select.innerHTML = '';
            slots.forEach(slot => {
                select.innerHTML += `<option value="${slot.id}">${escapeHtml(slot.nome)}</option>`;
            });
            if (slotId) select.value = slotId;
            updateWizardSubtitle();
        }

        function populateWizardScreenSelect() {
            const select = document.getElementById('wizard-screen-select');
            const screens = window.currentScreens || [];
            select.innerHTML = '';
            screens.forEach(screen => {
                select.innerHTML += `<option value="${screen.id}">${escapeHtml(screen.nome)}</option>`;
            });
        }

        function setWizardStep(step) {
            mediaWizard.step = step;
            document.querySelectorAll('.wizard-step').forEach(el => el.classList.add('hidden'));
            document.getElementById(`wizard-step-${step}`).classList.remove('hidden');
            document.querySelectorAll('.wizard-step-label').forEach((el, index) => {
                el.classList.toggle('bg-green-600', index + 1 === step);
                el.classList.toggle('text-white', index + 1 === step);
                el.classList.toggle('bg-slate-800', index + 1 !== step);
                el.classList.toggle('text-slate-400', index + 1 !== step);
            });
            document.getElementById('wizard-back-btn').classList.toggle('hidden', step === 1);
            document.getElementById('wizard-next-btn').classList.toggle('hidden', step === 4);
            document.getElementById('wizard-save-btn').classList.toggle('hidden', step !== 4);
            updateWizardSubtitle();
        }

        function updateWizardSubtitle() {
            const slot = getWizardSlot();
            document.getElementById('wizard-subtitle').textContent = slot ? `Slot: ${slot.nome}` : '';
        }

        function getWizardSlot() {
            const id = document.getElementById('wizard-slot-select')?.value;
            return (window.currentSlots || []).find(slot => String(slot.id) === String(id));
        }

        function setWizardSource(source) {
            mediaWizard.source = source;
            document.getElementById('wizard-library-panel').classList.toggle('hidden', source !== 'library');
            document.getElementById('wizard-upload-panel').classList.toggle('hidden', source !== 'upload');
            document.querySelectorAll('.wizard-source').forEach(btn => {
                btn.classList.remove('border-green-500', 'bg-green-500/10');
                btn.classList.add('border-slate-700', 'bg-slate-900');
            });
            const active = document.getElementById(`wizard-source-${source}`);
            active.classList.remove('border-slate-700', 'bg-slate-900');
            active.classList.add('border-green-500', 'bg-green-500/10');
        }

        function renderWizardMediaList() {
            const list = document.getElementById('wizard-media-list');
            const search = document.getElementById('wizard-media-search').value.toLowerCase();
            const media = (window.mediaLibraryData || []).filter(item =>
                item.file_name.toLowerCase().includes(search) ||
                item.file_type.toLowerCase().includes(search)
            );

            if (media.length === 0) {
                list.innerHTML = '<p class="text-sm text-slate-500">Nessun media trovato.</p>';
                return;
            }

            list.innerHTML = '';
            media.forEach(item => {
                const selected = mediaWizard.selectedMedia && String(mediaWizard.selectedMedia.id) === String(item.id);
                const card = document.createElement('button');
                card.type = 'button';
                card.className = `p-3 rounded-xl border text-left flex gap-3 items-center ${selected ? 'border-green-500 bg-green-500/10' : 'border-slate-700 bg-slate-900 hover:bg-slate-800'}`;
                card.onclick = () => selectWizardMedia(item);
                card.innerHTML = `
                    ${getMediaPreviewMarkup(item, 'w-16 h-12')}
                    <div class="min-w-0">
                        <p class="font-semibold text-sm truncate">${escapeHtml(item.file_name)}</p>
                        <p class="text-xs text-slate-500">${item.file_type} - ${formatFileSize(item.file_size)}</p>
                    </div>
                `;
                list.appendChild(card);
            });
        }

        function getMediaPreviewMarkup(media, sizeClass) {
            if (media.file_type === 'FOTO') {
                return `<img src="${media.file_path}" class="${sizeClass} object-cover rounded-lg bg-slate-950" alt="">`;
            }
            if (media.file_type === 'VIDEO') {
                return `<video src="${media.file_path}" class="${sizeClass} object-cover rounded-lg bg-slate-950" muted preload="metadata"></video>`;
            }
            return `<div class="${sizeClass} flex items-center justify-center rounded-lg bg-slate-950 text-blue-400">AUDIO</div>`;
        }

        function selectWizardMedia(media) {
            mediaWizard.selectedMedia = media;
            document.getElementById('wizard-type-select').value = media.file_type;
            renderWizardMediaList();
        }

        async function nextWizardStep() {
            if (mediaWizard.step === 1 && !document.getElementById('wizard-slot-select').value) {
                showToast('Seleziona uno slot', 'error');
                return;
            }
            if (mediaWizard.step === 2) {
                if (mediaWizard.source === 'upload') {
                    const uploaded = await uploadWizardFile();
                    if (!uploaded) return;
                    mediaWizard.selectedMedia = uploaded;
                    await fetchMediaLibrary();
                }
                if (!mediaWizard.selectedMedia) {
                    showToast('Seleziona o carica un media', 'error');
                    return;
                }
            }
            if (mediaWizard.step === 3) {
                if (!document.getElementById('wizard-screen-select').value) {
                    showToast('Seleziona uno schermo', 'error');
                    return;
                }
                await updateWizardSummary();
            }
            setWizardStep(Math.min(4, mediaWizard.step + 1));
        }

        function prevWizardStep() {
            setWizardStep(Math.max(1, mediaWizard.step - 1));
        }

        async function uploadWizardFile() {
            const input = document.getElementById('wizard-file-input');
            if (!input.files || !input.files[0]) {
                showToast('Seleziona un file da caricare', 'error');
                return null;
            }

            const formData = new FormData();
            formData.append('file', input.files[0]);
            const response = await fetch('/api/media-library/upload', { method: 'POST', body: formData });
            const result = await response.json();
            if (result.status !== 'ok') {
                showToast(result.message || 'Errore nel caricamento media', 'error');
                return null;
            }
            return result.data;
        }

        async function updateWizardSummary() {
            const slot = getWizardSlot();
            const screen = (window.currentScreens || []).find(item => String(item.id) === String(document.getElementById('wizard-screen-select').value));
            const media = mediaWizard.selectedMedia;
            const existingResponse = await fetch(`/api/media/talento?talento_id=${slot.id}`);
            const existingMedia = await existingResponse.json();
            mediaWizard.duplicate = Array.isArray(existingMedia) && existingMedia.some(item => item.file_path === media.file_path);
            document.getElementById('wizard-duplicate-warning').classList.toggle('hidden', !mediaWizard.duplicate);
            document.getElementById('wizard-save-btn').textContent = mediaWizard.duplicate ? 'Salva duplicato' : 'Salva';

            document.getElementById('wizard-summary').innerHTML = `
                ${summaryCard('Slot', slot.nome)}
                ${summaryCard('Media', media.file_name)}
                ${summaryCard('Schermo', screen ? screen.nome : '')}
                ${summaryCard('Tipo', document.getElementById('wizard-type-select').value)}
                ${summaryCard('Coda', 'Aggiunto automaticamente come pending')}
            `;
        }

        function summaryCard(label, value) {
            return `<div class="p-4 rounded-xl bg-slate-900 border border-slate-800">
                <p class="text-xs uppercase font-bold text-slate-500">${label}</p>
                <p class="mt-1 font-semibold">${escapeHtml(value)}</p>
            </div>`;
        }

        async function submitMediaWizard(allowDuplicate) {
            const slot = getWizardSlot();
            const media = mediaWizard.selectedMedia;
            try {
                const response = await fetch('/api/slot-media/add', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        talento_id: slot.id,
                        media_library_id: media.id,
                        screen_id: document.getElementById('wizard-screen-select').value,
                        tipo_media: document.getElementById('wizard-type-select').value,
                        allow_duplicate: allowDuplicate
                    })
                });
                const result = await response.json();
                if (response.status === 409 || result.status === 'duplicate') {
                    document.getElementById('wizard-duplicate-warning').classList.remove('hidden');
                    document.getElementById('wizard-save-btn').textContent = 'Salva duplicato';
                    document.getElementById('wizard-save-btn').onclick = () => submitMediaWizard(true);
                    return;
                }
                if (result.status !== 'ok') {
                    showToast(result.message || 'Errore nel salvataggio media', 'error');
                    return;
                }

                document.getElementById('wizard-save-btn').onclick = () => submitMediaWizard(false);
                await fetchMedia();
                await fetchMediaLibrary();
                closeMediaWizard();
            } catch (error) {
                console.error("Admin: Errore nel wizard media:", error);
                showToast('Errore nel salvataggio media', 'error');
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
            const confirmed = await showConfirm({
                title: 'Elimina slot',
                message: 'Vuoi eliminare questo slot?'
            });
            if (!confirmed) return;
            try {
                const response = await fetch(`${API_BASE}/elimina?id=${id}`, { method: 'DELETE' });
                const result = await response.json();
                if (result.status === 'ok') {
                    selectedItems.slots.delete(String(id));
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
                showToast('Seleziona un media dalla library', 'error');
                return;
            }

            try {
                // Get media library file path
                const libraryResponse = await fetch('/api/media-library');
                const libraryResult = await libraryResponse.json();
                const selectedMedia = libraryResult.data.find(m => m.id == data.media_library_id);

                if (!selectedMedia) {
                    showToast('Media non trovato', 'error');
                    return;
                }

                // Create media
                const mediaResponse = await fetch('/api/media', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        talento_id: data.talento_id,
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
            window.currentMedia = media;
            const list = document.getElementById('media-list');
            list.innerHTML = '';
            if (!document.getElementById('media-bulk-actions')) {
                list.insertAdjacentHTML('beforebegin', bulkActionBar('media', 'media'));
            }
            media.forEach(m => {
                const item = document.createElement('div');
                item.className = 'p-3 rounded-lg bg-slate-800/50 border border-slate-700 flex justify-between items-center';
                item.innerHTML = `
                    <div class="flex items-center gap-3 min-w-0">
                        <input type="checkbox" ${selectedItems.media.has(String(m.id)) ? 'checked' : ''} onchange="setSelected('media', ${m.id}, this.checked)">
                        <div class="min-w-0">
                        <span class="text-sm font-bold">${m.file_path}</span>
                        <span class="text-xs text-slate-500 ml-2">${m.tipo_media}</span>
                        </div>
                    </div>
                    <button onclick="deleteMedia(${m.id})" class="p-2 hover:bg-red-900/30 rounded-lg text-slate-400 hover:text-red-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                    </button>
                `;
                list.appendChild(item);
            });
            updateBulkActions('media');
        }

        async function deleteMedia(id) {
            const confirmed = await showConfirm({
                title: 'Elimina media',
                message: 'Vuoi eliminare questo media registrato?'
            });
            if (!confirmed) return;
            try {
                await fetch(`/api/media?id=${id}`, { method: 'DELETE' });
                selectedItems.media.delete(String(id));
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
                    return result.data;
                }
            } catch (error) {
                console.error("Admin: Errore nel recupero schermi:", error);
            }
            return [];
        }

        function renderScreensList(screens) {
            window.currentScreens = screens;
            const list = document.getElementById('screens-list');
            list.innerHTML = '';
            if (!document.getElementById('screens-bulk-actions')) {
                list.insertAdjacentHTML('beforebegin', bulkActionBar('screens', 'schermi'));
            }
            screens.forEach(screen => {
                const item = document.createElement('div');
                item.className = 'p-4 rounded-xl bg-slate-800/50 border border-slate-700';
                item.innerHTML = `
                    <div class="flex justify-between items-center mb-3">
                        <div class="flex items-center gap-3">
                            <input type="checkbox" ${selectedItems.screens.has(String(screen.id)) ? 'checked' : ''} onchange="setSelected('screens', ${screen.id}, this.checked)">
                            <h3 class="font-bold text-yellow-400">${screen.nome}</h3>
                        </div>
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
            updateBulkActions('screens');
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
            const confirmed = await showConfirm({
                title: 'Elimina schermo',
                message: 'Vuoi eliminare questo schermo?'
            });
            if (!confirmed) return;
            try {
                await fetch(`/api/screens/delete?id=${id}`, { method: 'DELETE' });
                selectedItems.screens.delete(String(id));
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
            window.currentNotes = notes;
            const list = document.getElementById('notes-list');
            list.innerHTML = '';
            if (!document.getElementById('notes-bulk-actions')) {
                list.insertAdjacentHTML('beforebegin', bulkActionBar('notes', 'note'));
            }
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
                        <div class="flex gap-3">
                            <input type="checkbox" class="mt-1" ${selectedItems.notes.has(String(note.id)) ? 'checked' : ''} onchange="setSelected('notes', ${note.id}, this.checked)">
                        <div>
                            <span class="text-xs font-bold ${tipoColors[note.tipo] || 'text-slate-400'} uppercase">${note.tipo}</span>
                            <span class="text-xs text-slate-500 ml-2">${note.talento_nome || 'Generale'}</span>
                            <p class="text-sm mt-1">${note.contenuto}</p>
                        </div>
                        </div>
                        <button onclick="deleteNote(${note.id})" class="p-2 hover:bg-red-900/30 rounded-lg text-slate-400 hover:text-red-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                        </button>
                    </div>
                `;
                list.appendChild(item);
            });
            updateBulkActions('notes');
        }

        async function deleteNote(id) {
            const confirmed = await showConfirm({
                title: 'Elimina nota',
                message: 'Vuoi eliminare questa nota?'
            });
            if (!confirmed) return;
            try {
                await fetch(`/api/notes/delete?id=${id}`, { method: 'DELETE' });
                selectedItems.notes.delete(String(id));
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
                showToast('Seleziona un file da caricare', 'error');
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
                    showToast('Media caricato con successo', 'success');
                } else {
                    showToast('Errore nel caricamento: ' + result.message, 'error');
                }
            } catch (error) {
                console.error("Admin: Errore nel caricamento media:", error);
                showToast('Errore nel caricamento media', 'error');
            }
        });

        async function fetchMediaLibrary() {
            try {
                const response = await fetch('/api/media-library');
                const result = await response.json();
                if (result.status === 'ok') {
                    renderMediaLibrary(result.data);
                    populateMediaLibrarySelect(result.data);
                    return result.data;
                }
            } catch (error) {
                console.error("Admin: Errore nel recupero media library:", error);
            }
            return [];
        }

        function renderMediaLibrary(media) {
            const list = document.getElementById('media-library-list');
            list.innerHTML = '';
            if (!document.getElementById('library-bulk-actions')) {
                list.insertAdjacentHTML('beforebegin', bulkActionBar('library', 'file'));
            }
            media.forEach(m => {
                const item = document.createElement('div');
                item.className = 'flex items-center justify-between p-4 bg-slate-800/50 rounded-xl group hover:bg-slate-800 transition-colors';
                item.innerHTML = `
                    <div class="flex items-center gap-4">
                        <input type="checkbox" ${selectedItems.library.has(String(m.id)) ? 'checked' : ''} onchange="setSelected('library', ${m.id}, this.checked)">
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
            updateBulkActions('library');
        }

        function formatFileSize(bytes) {
            if (!bytes) return 'N/A';
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(1024));
            return Math.round(bytes / Math.pow(1024, i) * 100) / 100 + ' ' + sizes[i];
        }

        async function deleteMediaLibrary(id) {
            const confirmed = await showConfirm({
                title: 'Elimina file media',
                message: 'Vuoi eliminare questo file dalla media library?'
            });
            if (!confirmed) return;
            try {
                await fetch(`/api/media-library/delete?id=${id}`, { method: 'DELETE' });
                selectedItems.library.delete(String(id));
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
                showToast('Seleziona almeno un file da registrare', 'error');
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
                    showToast(`${result.data.length} file registrati con successo`, 'success');
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
        fetchMedia();
    </script>
</body>
</html>
