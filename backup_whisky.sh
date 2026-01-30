#!/bin/bash
set -euo pipefail

# ------------------------------
# Konfiguration / Pfade
# ------------------------------
# SD-Karte Mountpunkt (Root)
SD_PATH="/"                                      # SD-Karte
DB_NAME="Whiskybewertungen"                     # Datenbankname
UPLOAD_DIR="/var/www/html/Whisky_Bewertung/uploads"  # Upload-Ordner

# Speicherorte fuer Backups und JSON
USB_PATH="Pfad"
JSON_FILE_USB="$USB_PATH/raspi_status.json"
JSON_FILE_HOME="Pfad/raspi_status.json"

TMP_DB="$USB_PATH/.db_tmp.sql"                  # temporaere DB-Datei
FINAL_DB="$USB_PATH/Whiskybewertungen_backup.sql"
TMP_UP="$USB_PATH/.uploads_tmp.tar.gz"         # temporaeres Upload-Archiv
FINAL_UP="$USB_PATH/Whisky_Bewertung_uploads_backup.tar.gz"

DATE=$(date +"%Y-%m-%d %H:%M:%S")
HOST=$(hostname)

STATUS="OK"
BACKUP_DONE=false

# ------------------------------
# Pruefen, ob Ressourcen vorhanden sind
# ------------------------------
SD_OK=false
DB_OK=false
UPLOAD_OK=false
DISK_OK=false

# SD-Karte (Root) pruefen
mountpoint -q "$SD_PATH" && SD_OK=true || STATUS="FEHLER"

# MariaDB
mysqladmin ping --silent && DB_OK=true || STATUS="FEHLER"

# Upload-Ordner
[[ -d "$UPLOAD_DIR" ]] && UPLOAD_OK=true || STATUS="FEHLER"

# SD-Karten-Speicher pruefen
if df -h "$SD_PATH" >/dev/null 2>&1; then
    DISK_OK=true
    SD_TOTAL=$(df -h "$SD_PATH" | awk 'NR==2 {print $2}')  # Gesamt
    SD_USED=$(df -h "$SD_PATH" | awk 'NR==2 {print $3}')   # Belegt
else
    SD_TOTAL="n/a"
    SD_USED="n/a"
    STATUS="WARN"
fi

# ------------------------------
# System-Health-Daten sammeln
# ------------------------------
CPU_LOAD=$(awk '{print $1}' /proc/loadavg)            # 1-Minuten CPU Load
RAM_USAGE=$(free | awk '/Mem:/ {printf "%.2f", $3/$2*100}')  # RAM %
CPU_TEMP=$(awk '{printf "%.1f", $1/1000}' /sys/class/thermal/thermal_zone0/temp)  # Â°C

# Letzte 20 Syslog-Eintraege (Backup-relevant)
SYSLOG=$(journalctl -n 20 --no-pager 2>/dev/null | sed 's/"/\\"/g')

# ------------------------------
# Backup durchfuehren
# ------------------------------
if $SD_OK && $DB_OK && $UPLOAD_OK; then
    # 1. Datenbank in temporaere Datei dumpen
    mysqldump --single-transaction "$DB_NAME" > "$TMP_DB"

    # 2. Upload-Ordner in temporaeres Archiv packen
    tar -czf "$TMP_UP" -C /var/www/html Whisky_Bewertung/uploads

    # 3. Nur wenn alles erfolgreich: final verschieben auf USB-Stick
    mv "$TMP_DB" "$FINAL_DB"
    mv "$TMP_UP" "$FINAL_UP"

    BACKUP_DONE=true
else
    STATUS="FEHLER"
fi

# ------------------------------
# JSON-Status erstellen
# ------------------------------
jq -n \
  --arg timestamp "$DATE" \
  --arg host "$HOST" \
  --arg status "$STATUS" \
  --argjson sd_mounted "$SD_OK" \
  --argjson mariadb_alive "$DB_OK" \
  --argjson upload_dir_exists "$UPLOAD_OK" \
  --argjson disk_ok "$DISK_OK" \
  --arg cpu_load "$CPU_LOAD" \
  --arg ram_usage "$RAM_USAGE" \
  --arg cpu_temp "$CPU_TEMP" \
  --arg sd_total "$SD_TOTAL" \
  --arg sd_used "$SD_USED" \
  --argjson backup_done "$BACKUP_DONE" \
  --arg syslog "$SYSLOG" \
  '{
    timestamp: $timestamp,
    host: $host,
    status: $status,
    sd_mounted: $sd_mounted,
    mariadb_alive: $mariadb_alive,
    upload_dir_exists: $upload_dir_exists,
    disk_ok: $disk_ok,
    cpu_load_1min: $cpu_load,
    ram_usage_percent: $ram_usage,
    cpu_temp_celsius: $cpu_temp,
    sd_total: $sd_total,
    sd_used: $sd_used,
    backup_done: $backup_done,
    syslog_recent: $syslog
  }' > "$JSON_FILE_USB"

# Kopie ins Home-Verzeichnis
cp "$JSON_FILE_USB" "$JSON_FILE_HOME"

# ------------------------------
# Optional: Erfolg auf Konsole
# ------------------------------
echo "Backup abgeschlossen: Status $STATUS, Backup OK=$BACKUP_DONE"
echo "Backups und JSON auf USB-Stick: $USB_PATH"
echo "JSON zusaetzlich im Home-Verzeichnis: $JSON_FILE_HOME"
