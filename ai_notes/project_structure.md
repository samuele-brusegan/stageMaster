/
├── app/
│   ├── Controllers/            # Logica di controllo (Gestione eventi, Sincronizzazione)
│   ├── Models/                 # Interazione con MySQL (Talenti, Scaletta, Media)
│   └── Views/                  # Template HTML/JS (Dashboard, Proiettore, Gobbo)
├── config/
│   └── database.php            # Connessione PDO al database
├── public/
│   ├── css/                    # CSS
│   │   ├── style.css           # Stile principale
│   │   └── structure/          # Struttura del CSS
│   ├── fonts/                  # Font
│   ├── assets/                 # Immagini/Video/Audio
│   │   ├── images/             # Immagini
│   │   ├── videos/             # Video
│   │   └── audio/              # Audio
│   ├── svg/                    # SVG
│   ├── js/                     # Logica PWA e sincronizzazione WebSocket/Polling
│   ├── index.php               # Entry point dell'applicazione
│   ├── pwa/
│   │   ├── manifest.json       # Configurazione PWA
│   │   └── service-worker.js   # Gestione offline e cache
│   ├── functions.php           # Funzioni globali
│   ├── routes.php              # Rotte dell'applicazione
│   └── imports.php             # Importazioni globali
└── docs/                       # Documentazione generata dall'IA