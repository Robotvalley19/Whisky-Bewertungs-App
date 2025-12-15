# 🥃 Whisky Management & Bewertungs-System

Ein webbasiertes PHP/MySQL-Projekt zur Verwaltung, Dokumentation und Bewertung einer privaten Whisky-Sammlung.

Das Projekt besteht aus:
- einem **Erfassungsformular** (Upload von Bildern & PDFs)
- einem **interaktiven Dashboard** mit Filter-, Such- und AJAX-Editierfunktionen
- moderner, animierter Oberfläche (Canvas-Bubble-Hintergrund)

---

## ✨ Features

- Whisky-Erfassung mit Metadaten
- Bild- & PDF-Upload
- Dashboard mit Kartenansicht
- Sidebar-Filter (Live-Suche)
- AJAX-Speicherung einzelner Felder
- Datumskonvertierung (DD.MM.YYYY ⇄ MySQL DATE)
- Statusverwaltung (Offen, Geschlossen, Leer, Sample)
- Responsive Layout
- UTF-8 / utf8mb4 sicher

---

## 🛠️ Technik

- PHP (procedural + AJAX)
- MySQL / MariaDB
- HTML5 / CSS3
- JavaScript (Fetch API)
- Canvas Animation
- Google Fonts

---

## 📂 Projektstruktur

```
/
├── index.php              # Whisky-Erfassung
├── dashboard.php          # Whisky-Dashboard
├── config.php.example     # Beispiel-Konfiguration
├── uploads/               # Upload-Ordner (ignoriert durch git)
├── README.md
├── LICENSE
└── .gitignore
```

---

## ⚙️ Installation

1. Repository klonen
2. `config.php.example` → `config.php` kopieren
3. Datenbank anlegen und Tabelle `whisky` erstellen
4. Upload-Ordner erstellen:
   ```bash
   mkdir uploads
   chmod 777 uploads
   ```
5. Projekt im Browser öffnen

---

## 🗄️ Datenbank

Die Tabelle `whisky` muss u.a. folgende Felder enthalten:

- Name
- Brennerei
- Land_Region
- Sorte
- Alter
- Alkoholgehalt
- Flaschengroesse
- Kaufdatum
- Kaufpreis
- Bild
- PDF
- Status
- Beschreibung
- Fassreifung

*(Schema kann projektspezifisch erweitert werden)*

---

## 🔐 Sicherheitshinweis

Dieses Projekt ist für **private Nutzung** gedacht.
Für öffentliche Nutzung empfohlen:

- Prepared Statements
- Login / Authentifizierung
- Upload-Validierung
- CSRF-Schutz

---

## 📜 Lizenz

MIT License – siehe [LICENSE](LICENSE)

---

## 🍂 Hinweis

Dieses Projekt ist aus persönlichem Interesse entstanden und erhebt keinen Anspruch auf Vollständigkeit oder professionelle Einsatzreife.

Viel Spaß beim Sammeln & Genießen 🥃


🔤 Fonts

Dieses Projekt verwendet die Schriftarten Cinzel und Open Sans.

Lokal eingebunden (offline)

Keine externen Google-Requests

Lizenz: SIL Open Font License (OFL)

Quelle:
https://fonts.google.com