<!DOCTYPE html>
<html lang="it" class="h-full bg-slate-900">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gobbo - Olmo's Got Talent</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Outfit', sans-serif; }
    </style>
</head>
<body class="h-full flex flex-col items-center justify-center p-12 text-center text-white overflow-hidden">
    <!-- Click to Sync Overlay -->
    <div id="sync-overlay" class="fixed inset-0 bg-slate-900 flex flex-col items-center justify-center z-50 transition-opacity duration-500">
        <button onclick="activateSync()" class="px-8 py-4 bg-purple-600 hover:bg-purple-500 text-white rounded-2xl font-bold text-2xl shadow-2xl transition-all hover:scale-105 active:scale-95">
            CLICCA PER ATTIVARE SINCRONIZZAZIONE
        </button>
        <p class="mt-4 text-slate-400 text-sm">Necessario per permettere la ricezione dei contenuti.</p>
    </div>

    <div id="content" class="w-full max-w-6xl">
        <h1 id="talent-name" class="text-4xl text-slate-400 mb-8 uppercase tracking-widest">In Attesa...</h1>
        <div id="main-text" class="text-7xl md:text-9xl font-bold leading-tight uppercase">
            PRONTO PER IL PROSSIMO TALENTO
        </div>
    </div>

    <!-- Clock / Timer -->
    <div class="fixed bottom-12 right-12 font-mono text-4xl text-slate-500">
        <span id="clock">00:00:00</span>
    </div>

    <script>
        const syncOverlay = document.getElementById('sync-overlay');

        function activateSync() {
            syncOverlay.style.opacity = '0';
            setTimeout(() => syncOverlay.style.display = 'none', 500);
            console.log("Gobbo: Sincronizzazione attivata dall'utente.");
        }

        function updateClock() {
            const now = new Date();
            document.getElementById('clock').textContent = now.toLocaleTimeString();
        }
        setInterval(updateClock, 1000);
        updateClock();

        window.addEventListener('storage', (e) => {
            if (e.key === 'gobbo_content' && e.newValue) {
                const data = JSON.parse(e.newValue);
                console.log("Gobbo: Nuovo contenuto ricevuto:", data);
                updateGobbo(data);
            }
        });

        // Controlla se c'è già del contenuto al caricamento
        window.addEventListener('load', () => {
            const initialData = localStorage.getItem('gobbo_content');
            if (initialData) {
                updateGobbo(JSON.parse(initialData));
            }
        });

        function updateGobbo(data) {
            const container = document.getElementById('content');
            container.style.opacity = '0';
            setTimeout(() => {
                document.getElementById('talent-name').textContent = data.talent || '';
                document.getElementById('main-text').textContent = data.text || '';
                container.style.opacity = '1';
                container.style.transition = 'opacity 0.3s ease-in-out';
            }, 300);
        }
    </script>
</body>
</html>
