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
                    <p class="text-slate-400 text-sm">Gestione talenti, scaletta e note tecniche</p>
                </div>
                <a href="/dashboard" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 rounded-lg font-semibold transition-all border border-slate-700 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                    <span>Dashboard</span>
                </a>
            </div>
        </header>

        <!-- Main Content -->
        <main class="flex-1 max-w-7xl mx-auto w-full p-6 space-y-8">
            <!-- Form Aggiunta Talento -->
            <section class="glass rounded-2xl p-6">
                <h2 class="text-xl font-bold mb-6 flex items-center gap-2 text-blue-400">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
                    Aggiungi Nuovo Talento
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
                    <div class="space-y-2">
                        <label class="text-xs font-bold uppercase text-slate-500">Note Luci</label>
                        <textarea name="note_luci" rows="2" class="w-full bg-slate-800 border border-slate-700 rounded-xl px-4 py-3 focus:outline-none focus:border-blue-500 transition-colors"></textarea>
                    </div>
                    <div class="space-y-2">
                        <label class="text-xs font-bold uppercase text-slate-500">Materiale Palco</label>
                        <textarea name="materiale_palco" rows="2" class="w-full bg-slate-800 border border-slate-700 rounded-xl px-4 py-3 focus:outline-none focus:border-blue-500 transition-colors"></textarea>
                    </div>
                    <div class="md:col-span-2 flex justify-end">
                        <button type="submit" class="px-8 py-3 bg-blue-600 hover:bg-blue-500 rounded-xl font-bold transition-all shadow-lg shadow-blue-900/20">
                            Salva Talento
                        </button>
                    </div>
                </form>
            </section>

            <!-- Scaletta Manager -->
            <section class="glass rounded-2xl p-6">
                <h2 class="text-xl font-bold mb-6 flex items-center gap-2 text-purple-400">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                    Scaletta Attuale
                </h2>
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="text-slate-500 text-xs uppercase tracking-wider border-b border-slate-700/50">
                                <th class="pb-4 pl-4">Pos</th>
                                <th class="pb-4">Talento</th>
                                <th class="pb-4">Categoria</th>
                                <th class="pb-4">Note Luci</th>
                                <th class="pb-4">Materiale</th>
                                <th class="pb-4 text-right pr-4">Azioni</th>
                            </tr>
                        </thead>
                        <tbody id="talent-list" class="divide-y divide-slate-700/50">
                            <!-- Mock Data -->
                            <tr class="group hover:bg-slate-800/30 transition-colors">
                                <td class="py-4 pl-4 font-mono text-blue-400">1</td>
                                <td class="py-4 font-bold">Mario Rossi</td>
                                <td class="py-4"><span class="px-2 py-0.5 rounded text-[10px] bg-blue-500/20 text-blue-400 border border-blue-500/30">CANTO</span></td>
                                <td class="py-4 text-sm text-slate-400">Puntata calda al centro...</td>
                                <td class="py-4 text-sm text-slate-400">Asta microfono</td>
                                <td class="py-4 text-right pr-4 flex justify-end gap-2">
                                    <button onclick="reorder(1, 'up')" class="p-2 hover:bg-slate-700 rounded-lg text-slate-400 hover:text-white transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path></svg>
                                    </button>
                                    <button onclick="reorder(1, 'down')" class="p-2 hover:bg-slate-700 rounded-lg text-slate-400 hover:text-white transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                    </button>
                                    <button onclick="deleteTalent(1)" class="p-2 hover:bg-red-900/30 rounded-lg text-slate-400 hover:text-red-400 transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>
        </main>
    </div>

    <script>
        const API_BASE = '/api/talenti';

        async function fetchTalenti() {
            try {
                const response = await fetch(API_BASE);
                console.log(response);
                const result = await response.text();
                console.log(result);
                if (result.status === 'ok') {
                    renderTalentList(result.data);
                }
            } catch (error) {
                console.error("Admin: Errore nel recupero talenti:", error);
            }
        }

        function renderTalentList(talenti) {
            const list = document.getElementById('talent-list');
            list.innerHTML = '';
            talenti.forEach((talent, index) => {
                const row = document.createElement('tr');
                row.className = 'group hover:bg-slate-800/30 transition-colors';
                row.innerHTML = `
                    <td class="py-4 pl-4 font-mono text-blue-400">${index + 1}</td>
                    <td class="py-4 font-bold">${talent.nome}</td>
                    <td class="py-4"><span class="px-2 py-0.5 rounded text-[10px] bg-blue-500/20 text-blue-400 border border-blue-500/30">${talent.categoria}</span></td>
                    <td class="py-4 text-sm text-slate-400">${talent.note_luci || '-'}</td>
                    <td class="py-4 text-sm text-slate-400">${talent.materiale_palco || '-'}</td>
                    <td class="py-4 text-right pr-4 flex justify-end gap-2">
                        <button onclick="moveTalent(${index}, 'up')" class="p-2 hover:bg-slate-700 rounded-lg text-slate-400 hover:text-white transition-colors" ${index === 0 ? 'disabled opacity-20' : ''}>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path></svg>
                        </button>
                        <button onclick="moveTalent(${index}, 'down')" class="p-2 hover:bg-slate-700 rounded-lg text-slate-400 hover:text-white transition-colors" ${index === talenti.length - 1 ? 'disabled opacity-20' : ''}>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                        <button onclick="deleteTalent(${talent.id})" class="p-2 hover:bg-red-900/30 rounded-lg text-slate-400 hover:text-red-400 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                        </button>
                    </td>
                `;
                list.appendChild(row);
            });
            // Salva l'ordine attuale globalmente per il reorder
            window.currentTalenti = talenti;
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

        async function moveTalent(index, direction) {
            const talenti = window.currentTalenti;
            if (direction === 'up' && index > 0) {
                [talenti[index], talenti[index - 1]] = [talenti[index - 1], talenti[index]];
            } else if (direction === 'down' && index < talenti.length - 1) {
                [talenti[index], talenti[index + 1]] = [talenti[index + 1], talenti[index]];
            }
            
            const ids = talenti.map(t => t.id);
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

        async function deleteTalent(id) {
            if (!confirm('Sei sicuro di voler eliminare questo talento?')) return;
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

        // Caricamento iniziale
        fetchTalenti();
    </script>
</body>
</html>
