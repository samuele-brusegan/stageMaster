# Guida al Deploy su Ubuntu VPS

Questa guida descrive i passaggi necessari per ospitare l'applicazione **Olmo's Got Talent Manager** su una VPS Ubuntu utilizzando Docker (Metodo Consigliato) o manualmente.

## Metodo Docker (Consigliato)

### 1. Requisiti
- Ubuntu 22.04+
- Docker e Docker Compose installati

### 2. Configurazione
1. Clona il repository sulla VPS.
2. Copia `.env.example` in `.env` e configura le variabili (specialmente DB_HOST=db).
3. Avvia i container:
   ```bash
   docker-compose up -d --build
   ```

### 3. Accesso
- App: `http://indirizzo-ip:8080`
- phpMyAdmin: `http://indirizzo-ip:8081`

### 4. SSL con Nginx (Reverse Proxy)
Se vuoi usare un dominio con SSL, usa Nginx sulla VPS come reverse proxy per puntare alla porta 8080 del container.

---

## Metodo Manuale (Legacy)

### 1. Requisiti di Sistema
- Ubuntu 22.04+
- PHP 8.2+ (con pdo_mysql, mbstring, openssl)
- MySQL 8.0+
- Apache o Nginx
- Certbot (per SSL)

### 2. Configurazione del Web Server
(Vedere i dettagli precedenti nella configurazione Apache/Nginx...)

### 3. Setup Permessi e Media
```bash
sudo chown -R www-data:www-data /var/www/olmos-talent
sudo chmod -R 775 /var/www/olmos-talent/public/assets
```

## Script di Deploy Automatico
- `scripts/deploy_docker.sh`: Per l'aggiornamento rapido con Docker.
- `scripts/deploy.sh`: Per il deploy manuale legacy.
