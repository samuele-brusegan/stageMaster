# Architettura MVC

Il sistema segue il pattern **Model-View-Controller (MVC)** per separare la logica di business, la gestione dei dati e la presentazione.

## 1. Model (Modelli) - `app/Models/`
I modelli si occupano dell'interazione diretta con il database MySQL tramite PDO.
- **Talento.php**: Gestisce i dati dei talenti e la scaletta (ordine di esecuzione). Include la logica di reordering in una transazione atomica.
- **Media.php**: Gestire i file audio/video associati a ogni talento.
- **PlayerState.php**: Gestisce lo stato in tempo reale di Proiettore e Gobbo (cosa stanno trasmettendo).

## 2. View (Viste) - `app/Views/`
Contengono i template HTML/JS che vengono renderizzati dal `Controller.php`.
- **Dashboard**: Interfaccia di controllo per il reordering e la gestione stati.
- **Proiettore/Gobbo**: Pagine delegate alla trasmissione dei contenuti.

## 3. Controller (Controllori) - `app/Controllers/`
Il ponte tra Modelli e Viste.
- **ApiController.php**: Base per tutte le API JSON. Gestisce header, formati di risposta e validazione base.
- **TalentoController.php**: Espone le API per listare e riordinare i talenti.
- **MediaController.php**: Fornisce i media necessari per le performance.
- **PlayerStateController.php**: Gestisce la sincronizzazione tra Dashboard e Player.

## Flusso della Richiesta
1. La richiesta arriva a `public/index.php`.
2. `Router.php` analizza l'URL e invoca il Controller/Metodo corrispondente definito in `public/routes.php`.
3. Il Controller interagisce con il Model per ottenere/modificare i dati.
4. Il Controller restituisce una risposta JSON (per le API) o renderizza una Vista.
