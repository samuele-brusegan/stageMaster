# Video Engine Logic

Il motore video di Olmo's Got Talent Manager è progettato per gestire riproduzioni fluide con tagli e dissolvenze utilizzando esclusivamente tecnologie web standard.

## Tecnologie Utilizzate

- **HTML5 Video Element**: Utilizzato per la decodifica e la riproduzione del flusso video/audio.
- **Web Audio API**: Utilizzato per il controllo granulare dell'audio, specificamente per dissolvenze incrociate (fade-in e fade-out) senza scatti.
- **LocalStorage API**: Utilizzato per la sincronizzazione ultra-rapida tra la finestra della Dashboard (Regia), del Proiettore e del Gobbo.

## Logica di Sincronizzazione

Le finestre comunicano tramite l'evento `storage` di `window`. 
1. La **Dashboard** scrive un comando in `localStorage['regia_command']`.
2. Il **Proiettore** ascolta l'evento e agisce di conseguenza (play, pause, stop, fade).
3. Il **Proiettore** scrive periodicamente il suo stato (currentTime, duration) in `localStorage['projector_status']`.
4. La **Dashboard** legge lo stato per aggiornare la barra di progresso e il timer.

## Video Engine (Tagli e Dissolvenze)

### Tagli (Start/End)
I tagli sono gestiti impostando `currentTime` all'inizio del play e monitorando l'evento `timeupdate` per fermare la riproduzione quando viene raggiunto il timestamp di fine (`timestamp_fine`).

### Dissolvenze (Fades)
- **Audio**: Viene creato un `AudioContext` e un `GainNode`. Durante il `fadeIn`, il guadagno viene portato da 0 a 1 linearmente in un tempo definito (default 1s). Viceversa per il `fadeOut`.
- **Video**: Viene utilizzato un overlay nero (`#fade-overlay`) con transizione CSS sull'opacità per oscurare gradualmente il video.

## Sincronizzazione Finestre (Work Log)
- **Problema**: `postMessage` richiede un riferimento alla finestra figlia, che si perde se la pagina viene ricaricata.
- **Soluzione**: `localStorage` è persistente e globale per lo stesso dominio, rendendolo ideale per la sincronizzazione tra finestre indipendenti.
- **Limite**: `localStorage` ha una latenza di circa 20-50ms, accettabile per il controllo regia ma non per sincronizzazione audio-video fine tra dispositivi diversi (che richiederebbe WebSockets).
