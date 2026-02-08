# WORK_LOG - Backend Agent

## Gestione Sicurezza e Validazione

### 1. Sicurezza delle chiamate API
- **Session Check**: Ogni richiesta che passa per `index.php` invoca `checkSessionExpiration()`. Sebbene non ci sia ancora un sistema di login complesso, la struttura è pronta per integrare middleware di autenticazione.
- **SQL Injection**: Tutte le interazioni con il database nei Modelli utilizzano **Prepared Statements** con PDO, prevenendo attacchi di SQL Injection tramite escaping automatico dei parametri.
- **Transactions**: Il riordinamento della scaletta (`Talento::reorder`) viene eseguito all'interno di una transazione database. In caso di errore durante l'aggiornamento di uno degli ID, viene effettuato un rollback completo per evitare stati inconsistenti della scaletta.

### 2. Validazione dei dati
- **Metodo `validate()` in `ApiController`**: È stato implementato un sistema di validazione base che controlla la presenza di campi obbligatori.
- **Sanitizzazione Input**: Il metodo `getJsonInput()` decodifica il payload JSON. I controller verificano che i tipi di dati siano corretti (es. `is_array` per `ordered_ids`).
- **Error Handling**: Se la validazione fallisce, l'API risponde istantaneamente con un `400 Bad Request` e un messaggio descrittivo in formato JSON, interrompendo l'esecuzione.

### 3. Sincronizzazione Player State
- Il sistema utilizza il pattern **Polling/Update** tramite la tabella `player_state`. 
- Il Proiettore e il Gobbo inviano aggiornamenti sul loro stato (playing/paused/stopped) e sul media corrente.
- La Dashboard interroga periodicamente queste API per mostrare lo stato live all'operatore.
