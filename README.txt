# Whisky Portal – Webbasierte Whiskyverwaltung

Ein vollständiges PHP/MySQL-Projekt zur Erfassung, Verwaltung und Dokumentation von Whiskys inklusive Bild- und PDF-Upload, Dashboard mit AJAX-Bearbeitung, Filterfunktionen, zentralem Portal zur Dienstübersicht sowie automatischem Raspberry-Pi Health- & Backup-System.

---

## PROJEKTÜBERSICHT

Das Projekt besteht aus fünf Hauptkomponenten:

1. Whisky Portal (Startseite / Dienstübersicht)
2. Whiskyerfassung (Eingabeformular)
3. Whisky Dashboard (Verwaltung & Bearbeitung)
4. Raspberry Pi Health & Backup System
5. Server- & Dienstkonfiguration (PHP, Apache, MySQL)

Datenbankname:
Whiskybewertungen

---

## VERZEICHNISSTRUKTUR

/var/www/html/Whisky_Bewertung/

- index_Portal.php  
- index_Bewertung.php  
- index_Dashboard.php  
- health_dashboard.php  
- uploads/  
- README.md  

Zusätzlich auf Systemebene:

/usr/local/bin/backup_script.sh  
/home/<user>/raspi_status.json  
/mnt/usb/Whiskybewertungen_backup.sql  
/mnt/usb/Whisky_Bewertung_uploads_backup.tar.gz  

---

## SYSTEMVORAUSSETZUNGEN

Server:
- Linux (Debian, Ubuntu Server, Raspberry Pi OS)
- Apache 2
- PHP 8.2
- MySQL oder MariaDB
- jq (für JSON-Erstellung im Backup-Skript)

Optional:
- systemd oder cron für automatische Backup-Ausführung
- USB-Stick oder externes Laufwerk für Offsite-Backups

PHP-Erweiterungen:
- mysqli
- mbstring
- fileinfo
- gd (empfohlen)

---

## Raspberry Pi HEALTH & BACKUP SYSTEM

Dieses Projekt enthält ein automatisiertes Überwachungs- und Backup-System für Raspberry Pi Server.

### Funktionen

Überprüfung von:
- SD-Karten-Mount
- MariaDB-Erreichbarkeit
- Upload-Ordner
- Speicherplatz

Zusätzlich:
- CPU-Last (1-Minuten Load)
- RAM-Auslastung (%)
- CPU-Temperatur
- Letzte 20 journalctl-Einträge
- Automatisches Backup von:
  - MySQL-Datenbank
  - Upload-Ordner (Bilder & PDFs)
- JSON-Statusdatei für Dashboard-Visualisierung

---

## Backup-Skript (backup_script.sh)

Das Bash-Skript:

- erstellt einen Datenbank-Dump (mysqldump)
- archiviert den Upload-Ordner
- speichert Backups auf USB-Stick
- erzeugt eine JSON-Statusdatei
- kopiert die JSON-Datei zusätzlich ins Home-Verzeichnis
- liefert Statusinformationen für das Health-Dashboard

Beispielausgabe:

Backup abgeschlossen: Status OK, Backup OK=true  
Backups und JSON auf USB-Stick: /mnt/usb  
JSON zusätzlich im Home-Verzeichnis: /home/pi/raspi_status.json  

Empfohlene Automatisierung via Cron (täglich um 03:00 Uhr):

0 3 * * * /usr/local/bin/backup_script.sh

---

## Health Dashboard (health_dashboard.php)

Weboberfläche im Dark-Whisky-Theme zur Anzeige von:

- Gesamtstatus
- SD-Mount-Status
- Datenbankstatus
- Backupstatus
- CPU-Auslastung
- RAM-Auslastung
- CPU-Temperatur
- SD-Kartenbelegung
- Syslog-Auszug

Statusanzeige:
- Grün → OK
- Rot → Fehler

Das Dashboard liest:
raspi_status.json

---

## PHP-KONFIGURATION (EMPFOHLEN)

upload_max_filesize = 2G  
post_max_size = 2G  
memory_limit = 2G  
max_execution_time = 300  
max_input_time = 300  
default_charset = UTF-8  

---

## DATENBANK

Tabelle: whisky

Felder:

- id
- Name
- Brennerei
- Land_Region
- Sorte
- Alter
- Alkoholgehalt
- Flaschengroesse
- Abfueller
- Kaufdatum
- Kaufpreis
- Bild
- PDF
- Fassreifung
- Beschreibung
- Datum_der_Flaschenoeffnung
- Grund_der_Flaschenoeffnung
- Status
- Fundort
- Anzahl_der_Flaschen

---

## WHISKY PORTAL (index_Portal.php)

Zentrale Startseite mit:
- Dienstübersicht
- Automatischer IP-Erkennung
- Canvas-Hintergrundanimation
- Verlinkung zum Health Dashboard

---

## WHISKYERFASSUNG (index_Bewertung.php)

- Formular zur Neueingabe
- Bild- & PDF-Upload
- UTF-8-Unterstützung
- Speicherung in MySQL

Uploads-Verzeichnis:
/var/www/html/Whisky_Bewertung/uploads/

(Der Ordner muss schreibbar sein)

---

## WHISKY DASHBOARD (index_Dashboard.php)

- Kartenansicht aller Whiskys
- Filter & Pagination
- AJAX-Bearbeitung
- Statusverwaltung
- Download von Bildern & PDFs

---

## SICHERHEIT

- Keine Zugangsdaten in GitHub committen
- config.php oder .env verwenden
- uploads/ nicht mit Inhalten hochladen
- Backup-Skript nicht öffentlich zugänglich machen
- USB-Backups regelmäßig testen

---

## LIZENZ

MIT License

---

## Fonts

Dieses Projekt verwendet die Schriftarten **Cinzel** und **Open Sans**.  
Die Fonts sind lokal eingebunden (offline) und stehen unter der  
SIL Open Font License (OFL).

Quelle: https://fonts.google.com
