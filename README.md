# 🥃 Whisky Management & Bewertungs-System

Ein webbasiertes PHP/MySQL-Projekt zur Verwaltung, Dokumentation und Bewertung einer privaten Whisky-Sammlung – inklusive automatischem Raspberry-Pi Health- & Backup-System.

Das Projekt besteht aus:

- einem **Erfassungsformular** (Upload von Bildern & PDFs)
- einem **interaktiven Dashboard** mit Filter-, Such- und AJAX-Editierfunktionen
- moderner, animierter Oberfläche (Canvas-Bubble-Hintergrund)
- einem **Server Health Dashboard**
- einem **automatisierten Backup-Skript** (Datenbank + Uploads)

---

## ✨ Features

### 🥃 Whisky-Verwaltung
- Whisky-Erfassung mit Metadaten
- Bild- & PDF-Upload
- Dashboard mit Kartenansicht
- Sidebar-Filter (Live-Suche)
- AJAX-Speicherung einzelner Felder
- Datumskonvertierung (DD.MM.YYYY ⇄ MySQL DATE)
- Statusverwaltung (Offen, Geschlossen, Leer, Sample)
- Responsive Layout
- UTF-8 / utf8mb4 sicher

### 🖥️ Raspberry Pi Health & Backup
- Automatische Überprüfung von:
  - SD-Karten-Mount
  - MariaDB-Status
  - Upload-Verzeichnis
  - Speicherplatz
- CPU-Last (1-Minuten-Load)
- RAM-Auslastung (%)
- CPU-Temperatur
- Letzte 20 `journalctl`-Einträge
- Automatischer Datenbank-Dump (mysqldump)
- Archivierung des Upload-Ordners (tar.gz)
- JSON-Statusdatei für Web-Dashboard
- USB-Backup-Unterstützung
- Cronjob-fähig

---

## 🛠️ Technik

### Webanwendung
- PHP (procedural + AJAX)
- MySQL / MariaDB
- HTML5 / CSS3
- JavaScript (Fetch API)
- Canvas Animation
- Lokale Fonts (keine externen Requests)

### Server & Backup
- Bash (Backup-Skript)
- jq (JSON-Erstellung)
- mysqldump
- tar
- journalctl
- cron (optional)

---

## 📂 Projektstruktur

```
/
├── index.php              # Whisky-Erfassung
├── dashboard.php          # Whisky-Dashboard
├── health_dashboard.php   # Raspberry Pi Health Dashboard
├── backup_script.sh       # Automatisches Backup-Skript
├── config.php.example     # Beispiel-Konfiguration
├── uploads/               # Upload-Ordner (ignoriert durch git)
├── README.md
├── LICENSE
└── .gitignore
```

Systemebene (Beispiel Raspberry Pi):

```
/usr/local/bin/backup_script.sh
/home/<user>/raspi_status.json
/mnt/usb/Whiskybewertungen_backup.sql
/mnt/usb/Whisky_uploads_backup.tar.gz
```

---

## ⚙️ Installation

### 1. Repository klonen

```bash
git clone <repository-url>
```

---

### 2. Konfiguration erstellen

```
config.php.example → config.php
```

Datenbank-Zugangsdaten eintragen.

---

### 3. Datenbank anlegen

Datenbank:
```
Whiskybewertungen
```

Tabelle:
```
whisky
```

(Felder siehe unten)

---

### 4. Upload-Ordner erstellen

```bash
mkdir uploads
chmod 775 uploads
```

---

### 5. Optional: Backup-Skript aktivieren

```bash
chmod +x backup_script.sh
sudo mv backup_script.sh /usr/local/bin/
```

Cronjob (täglich um 03:00 Uhr):

```bash
0 3 * * * /usr/local/bin/backup_script.sh
```

---

## 🗄️ Datenbank

Die Tabelle `whisky` sollte u.a. folgende Felder enthalten:

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
- Status
- Beschreibung
- Fassreifung
- Anzahl_der_Flaschen
- Fundort

*(Schema kann projektspezifisch erweitert werden)*

---

## 🖥️ Health Dashboard

Das Health-Dashboard liest eine automatisch erzeugte Datei:

```
raspi_status.json
```

Anzeige:

- Gesamtstatus (OK / Fehler)
- Mount-Status
- Datenbankstatus
- Backupstatus
- CPU / RAM / Temperatur
- SD-Kartenbelegung
- Syslog-Auszug

Farbcodierung:

- Grün → OK  
- Rot → Fehler  

Dark-Whisky-Theme passend zum Hauptprojekt.

---

## 🔐 Sicherheitshinweis

Dieses Projekt ist für **private Nutzung** gedacht.

Für öffentliche Nutzung empfohlen:

- Prepared Statements
- Login / Authentifizierung
- Upload-Validierung (MIME-Check)
- CSRF-Schutz
- Rechteverwaltung
- Kein 777 in Produktivumgebungen

---

## 📜 Lizenz

MIT License – siehe [LICENSE](LICENSE)

---

## 🍂 Hinweis

Dieses Projekt ist aus persönlichem Interesse entstanden und erhebt keinen Anspruch auf Vollständigkeit oder professionelle Einsatzreife.

Viel Spaß beim Sammeln & Genießen 🥃

---

## 🔤 Fonts

Dieses Projekt verwendet die Schriftarten:

- **Cinzel**
- **Open Sans**

✔ Lokal eingebunden (offline)  
✔ Keine externen Google-Requests  
✔ Lizenz: SIL Open Font License (OFL)  

Quelle:  
https://fonts.google.com
