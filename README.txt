Whisky Portal – Webbasierte Whiskyverwaltung

Ein vollständiges PHP/MySQL-Projekt zur Erfassung, Verwaltung und Dokumentation von Whiskys inklusive Bild- und PDF-Upload, Dashboard mit AJAX-Bearbeitung, Filterfunktionen sowie einem zentralen Portal zur Dienstübersicht.

------------------------------------------------------------
PROJEKTÜBERSICHT
------------------------------------------------------------

Das Projekt besteht aus vier Hauptkomponenten:

1. Whisky Portal (Startseite / Dienstübersicht)
2. Whiskyerfassung (Eingabeformular)
3. Whisky Dashboard (Verwaltung & Bearbeitung)
4. Server- & Dienstkonfiguration (PHP, Apache, MySQL)

Datenbankname:
Whiskybewertungen

------------------------------------------------------------
VERZEICHNISSTRUKTUR
------------------------------------------------------------

/var/www/html/Whisky_Bewertung/
|-- index_Portal.php
|-- index_Bewertung.php
|-- index_Dashboard.php
|-- uploads/
|   |-- (Bilder & PDFs)
|-- README.txt

------------------------------------------------------------
SYSTEMVORAUSSETZUNGEN
------------------------------------------------------------

Server:
- Linux (z. B. Debian Server, Ubuntu Server, Raspberry Pi)
- Apache 2
- PHP 8.2
- MySQL oder MariaDB

PHP-Erweiterungen:
- mysqli
- mbstring
- fileinfo
- gd (empfohlen)

------------------------------------------------------------
PHP-KONFIGURATION (EMPFOHLEN)
------------------------------------------------------------

upload_max_filesize = 2G
post_max_size = 2G
memory_limit = 2G
max_execution_time = 300
max_input_time = 300
default_charset = UTF-8

------------------------------------------------------------
DATENBANK
------------------------------------------------------------

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

------------------------------------------------------------
WHISKY PORTAL (index_Portal.php)
------------------------------------------------------------

Zentrale Startseite mit:
- Dienstübersicht
- Automatischer IP-Erkennung
- Canvas-Hintergrundanimation

------------------------------------------------------------
WHISKYERFASSUNG (index_Bewertung.php)
------------------------------------------------------------

- Formular zur Neueingabe
- Bild- & PDF-Upload
- UTF-8 Unterstützung
- Speicherung in MySQL

Uploads-Verzeichnis:
/var/www/html/Whisky_Bewertung/uploads/
(Der Ordner muss schreibbar sein)

------------------------------------------------------------
WHISKY DASHBOARD (index_Dashboard.php)
------------------------------------------------------------

- Kartenansicht aller Whiskys
- Filter & Pagination
- AJAX-Bearbeitung
- Statusverwaltung
- Download von Bildern & PDFs

------------------------------------------------------------
SICHERHEIT
------------------------------------------------------------

- Keine Zugangsdaten in GitHub committen
- config.php oder .env verwenden
- uploads/ nicht mit Inhalten hochladen

------------------------------------------------------------
LIZENZ
------------------------------------------------------------

MIT License


## Fonts

Dieses Projekt verwendet die Schriftarten **Cinzel** und **Open Sans**.
Die Fonts sind lokal eingebunden (offline) und stehen unter der
SIL Open Font License (OFL).

Quelle: https://fonts.google.com

