# Whisky Portal – Webbasierte Whiskyverwaltung

Ein vollständiges, praxisnahes PHP/MySQL-Projekt zur Erfassung, Verwaltung und Dokumentation von Whiskys. Das Projekt richtet sich an Entwickler, die ein echtes Full-Stack-Projekt aufbauen möchten, und zeigt Kompetenzen in Webentwicklung, Datenbankmanagement, Serveradministration und Automatisierung. Die Anwendung bietet ein modernes, interaktives Dashboard, intuitive Filter- und Bearbeitungsfunktionen, Upload-Möglichkeiten für Bilder und PDFs sowie ein automatisiertes Health- und Backup-System, speziell für den Betrieb auf einem Raspberry Pi Server optimiert.

Dieses Projekt entstand mit dem Ziel, eine zentrale, leicht zu bedienende Whisky-Verwaltungsplattform zu schaffen, die sowohl Hobby-Sammler als auch professionelle Nutzer anspricht. Es kombiniert klassische Webtechnologien wie PHP, MySQL, HTML, CSS und JavaScript mit systemnahen Funktionen wie Bash-Skripten, JSON-Statusdateien und Cron-basierten Automatisierungen. Damit demonstriert es ein breites Skillset von Frontend-Entwicklung, Backend-Logik, Datenbankdesign bis hin zu Server-Monitoring und Automatisierung.

Durch die Verwendung eines Raspberry Pi als Hostsystem zeigt das Projekt praxisnah, wie man kleine Server effizient überwachen, Backups automatisieren und eine Webanwendung stabil betreiben kann. Gleichzeitig bietet es moderne UI-Elemente wie AJAX-basierte Bearbeitung, Kartenansichten, Pagination und Canvas-Animationen, sodass die Anwendung sowohl funktional als auch optisch ansprechend ist.

---

## 📌 Projektübersicht

Das Projekt besteht aus fünf Hauptkomponenten:

1. **Whisky Portal** – Startseite / Dienstübersicht
2. **Whiskyerfassung** – Eingabeformular
3. **Whisky Dashboard** – Verwaltung & Bearbeitung
4. **Raspberry Pi Health & Backup System**
5. **Server- & Dienstkonfiguration** – PHP, Apache, MySQL

**Datenbankname:** `Whiskybewertungen`

---

## 📂 Verzeichnisstruktur

**Web-Anwendung:** `/var/www/html/Whisky_Bewertung/`

* `index_Portal.php`
* `index_Bewertung.php`
* `index_Dashboard.php`
* `health_dashboard.php`
* `uploads/`
* `README.md`

**System-Skripte auf Raspberry Pi:**

* `/usr/local/bin/backup_script.sh`
* `/home/<user>/raspi_status.json`
* `/mnt/usb/Whiskybewertungen_backup.sql`
* `/mnt/usb/Whisky_Bewertung_uploads_backup.tar.gz`

---

## 🖥️ Systemvoraussetzungen

**Server:**

* Linux (Debian, Ubuntu Server, Raspberry Pi OS)
* Apache 2
* PHP 8.2
* MySQL oder MariaDB
* jq (für JSON-Erstellung im Backup-Skript)

**Optional:**

* systemd oder cron für automatische Backup-Ausführung
* USB-Stick oder externes Laufwerk für Offsite-Backups

**PHP-Erweiterungen:**

* mysqli
* mbstring
* fileinfo
* gd (empfohlen)

---

## ⚙️ Raspberry Pi Health & Backup System

Automatisiertes Überwachungs- und Backup-System für Raspberry Pi Server.

### Funktionen

* Überprüfung von SD-Karten-Mount, Datenbank-Erreichbarkeit, Upload-Ordner und Speicherplatz
* CPU-Last, RAM-Auslastung, CPU-Temperatur
* Letzte 20 journalctl-Einträge
* Automatisches Backup von MySQL-Datenbank und Upload-Ordner (Bilder & PDFs)
* JSON-Statusdatei für Dashboard-Visualisierung

---

## 💾 Backup-Skript (`backup_script.sh`)

* Erstellt Datenbank-Dump (`mysqldump`)
* Archiviert Upload-Ordner
* Speichert Backups auf USB-Stick
* Erzeugt JSON-Statusdatei
* Kopiert JSON zusätzlich ins Home-Verzeichnis
* Liefert Statusinformationen für Health-Dashboard

**Beispielausgabe:**

```
Backup abgeschlossen: Status OK, Backup OK=true
Backups und JSON auf USB-Stick: /mnt/usb
JSON zusätzlich im Home-Verzeichnis: /home/pi/raspi_status.json
```

**Cron-Empfehlung:**

```
0 3 * * * /usr/local/bin/backup_script.sh
```

---

## 📊 Health Dashboard (`health_dashboard.php`)

Weboberfläche im Dark-Whisky-Theme zur Anzeige von:

* Gesamtstatus
* SD-Mount-Status
* Datenbankstatus
* Backupstatus
* CPU-Auslastung, RAM-Auslastung, CPU-Temperatur
* SD-Kartenbelegung
* Syslog-Auszug

**Statusanzeige:**

* 🟢 Grün → OK
* 🔴 Rot → Fehler

Das Dashboard liest `raspi_status.json`.

---

## PHP-Konfiguration (empfohlen)

```
upload_max_filesize = 2G
post_max_size = 2G
memory_limit = 2G
max_execution_time = 300
max_input_time = 300
default_charset = UTF-8
```

---

## 🗄️ Datenbank

**Tabelle:** `whisky`

**Felder:**

* id
* Name
* Brennerei
* Land_Region
* Sorte
* Alter
* Alkoholgehalt
* Flaschengroesse
* Abfueller
* Kaufdatum
* Kaufpreis
* Bild
* PDF
* Fassreifung
* Beschreibung
* Datum_der_Flaschenoeffnung
* Grund_der_Flaschenoeffnung
* Status
* Fundort
* Anzahl_der_Flaschen

---

## 🖼️ Whisky Portal (`index_Portal.php`)

* Startseite mit Dienstübersicht
* Automatische IP-Erkennung
* Canvas-Hintergrundanimation
* Verlinkung zum Health Dashboard

---

## ✍️ Whiskyerfassung (`index_Bewertung.php`)

* Formular zur Neueingabe
* Bild- & PDF-Upload
* UTF-8-Unterstützung
* Speicherung in MySQL
* Upload-Verzeichnis: `/var/www/html/Whisky_Bewertung/uploads/` (muss schreibbar sein)

---

## 📊 Whisky Dashboard (`index_Dashboard.php`)

* Kartenansicht aller Whiskys
* Filter & Pagination
* AJAX-Bearbeitung
* Statusverwaltung
* Download von Bildern & PDFs

---

## 🔒 Sicherheit

* Keine Zugangsdaten in GitHub committen
* `config.php` oder `.env` verwenden
* `uploads/` nicht mit Inhalten hochladen
* Backup-Skript nicht öffentlich zugänglich machen
* USB-Backups regelmäßig testen

---

## 📄 Lizenz

MIT License

---

## 🔤 Fonts

Dieses Projekt verwendet die Schriftarten **Cinzel** und **Open Sans**. Sie sind lokal eingebunden (offline) und stehen unter der SIL Open Font License (OFL).
Quelle: [Google Fonts](https://fonts.google.com)

---

## 👨‍💻 Autor

Robotvalley19  

Eigenständig entwickelt als praxisorientiertes Full-Stack-Projekt.
