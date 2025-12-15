<?php
// ===========================================
// Whisky Bewertungsformular (UTF-8 stabil)
// ===========================================

// UTF-8 und Fehleranzeige
mb_internal_encoding("UTF-8");
mb_http_output("UTF-8");
ini_set('default_charset', 'UTF-8');

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Upload-Limits
ini_set('upload_max_filesize', '2G');
ini_set('post_max_size', '2G');
ini_set('memory_limit', '2G');
ini_set('max_execution_time', 300);
ini_set('max_input_time', 300);

// DB-Verbindung
$servername = "localhost";
$username   = "eigener User";
$password   = "eigenes Passwort";
$dbname     = "Whiskybewertungen";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) die("Verbindung fehlgeschlagen: ".$conn->connect_error);
$conn->set_charset("utf8mb4");

// Upload-Fehlermeldungen
function fileUploadErrorMessage($error_code) {
    $errors = [
        UPLOAD_ERR_OK         => "Kein Fehler.",
        UPLOAD_ERR_INI_SIZE   => "Datei > upload_max_filesize.",
        UPLOAD_ERR_FORM_SIZE  => "Datei > MAX_FILE_SIZE.",
        UPLOAD_ERR_PARTIAL    => "Datei nur teilweise hochgeladen.",
        UPLOAD_ERR_NO_FILE    => "Keine Datei hochgeladen.",
        UPLOAD_ERR_NO_TMP_DIR => "Kein temporaerer Ordner.",
        UPLOAD_ERR_CANT_WRITE => "Schreibfehler.",
        UPLOAD_ERR_EXTENSION  => "Upload durch Erweiterung gestoppt."
    ];
    return $errors[$error_code] ?? "Unbekannter Fehler.";
}

$uploadDirAbsolute = "/var/www/html/Whisky_Bewertung/uploads/";
$uploadDirWeb      = "uploads/";

if (!is_dir($uploadDirAbsolute)) mkdir($uploadDirAbsolute, 0777, true);
if (!is_writable($uploadDirAbsolute)) die("<p style='color:red;'>Upload-Ordner nicht beschreibbar.</p>");

// POST-Verarbeitung
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // Eingaben
    $name = trim(mysqli_real_escape_string($conn, $_POST['name'] ?? ''));
    $brennerei = trim(mysqli_real_escape_string($conn, $_POST['brennerei'] ?? ''));
    $land_region = mysqli_real_escape_string($conn, $_POST['land_region'] ?? '');
    $sorte = mysqli_real_escape_string($conn, $_POST['sorte'] ?? '');
    $alter = mysqli_real_escape_string($conn, $_POST['alter'] ?? '');
    $alkoholgehalt = mysqli_real_escape_string($conn, $_POST['alkoholgehalt'] ?? '');
    $flaschengroesse = mysqli_real_escape_string($conn, $_POST['flaschengroesse'] ?? '');
    $abfueller = mysqli_real_escape_string($conn, $_POST['abfueller'] ?? '');
    $kaufdatum = trim(mysqli_real_escape_string($conn, $_POST['kaufdatum'] ?? ''));
    $kaufpreis = mysqli_real_escape_string($conn, $_POST['kaufpreis'] ?? '');
    
    // Kaufdatum von DD.MM.YYYY in MySQL-Format YYYY-MM-DD konvertieren
    if (!empty($kaufdatum)) {
        $date = DateTime::createFromFormat('d.m.Y', $kaufdatum);
        if ($date) {
            $kaufdatum = $date->format('Y-m-d'); // korrekt fuer MySQL DATE
        } else {
            $kaufdatum = null; // ungueltiges Datum -> NULL speichern
        }
    } else {
        $kaufdatum = null; // leer -> NULL
    }

    // Minimalpruefung
    if (empty($name) && empty($brennerei)) {
        die("<p style='color:red;'>Bitte mindestens Name oder Brennerei angeben.</p>");
    }

    // Upload Bild
    $bildPath = null;
    if (!empty($_FILES['bild']['name'])) {
        if ($_FILES['bild']['error'] === UPLOAD_ERR_OK) {
            $bildName = time().'_'.basename($_FILES['bild']['name']);
            $bildPathAbsolute = $uploadDirAbsolute.$bildName;
            $bildPath = $uploadDirWeb.$bildName;

            if (!move_uploaded_file($_FILES['bild']['tmp_name'], $bildPathAbsolute)) {
                die("<p style='color:red;'>Fehler beim Verschieben der Bilddatei.</p>");
            }
        }
    }
    
    // Upload PDF
    $pdfPath = null;
    if (!empty($_FILES['pdf']['name'])) {
        if ($_FILES['pdf']['error'] === UPLOAD_ERR_OK) {
            $pdfName = time().'_'.basename($_FILES['pdf']['name']);
            $pdfPathAbsolute = $uploadDirAbsolute.$pdfName;
            $pdfPath = $uploadDirWeb.$pdfName;

            if (!move_uploaded_file($_FILES['pdf']['tmp_name'], $pdfPathAbsolute)) {
                die("<p style='color:red;'>Fehler beim Verschieben der PDF-Datei.</p>");
            }
        }
    }
    
    // SQL speichern
    $sql = "INSERT INTO whisky 
        (Name, Brennerei, Land_Region, Sorte, `Alter`, Alkoholgehalt, Flaschengroesse, Abfueller, Kaufdatum, Kaufpreis, Bild, PDF) 
        VALUES (
            '$name',
            '$brennerei',
            '$land_region',
            '$sorte',
            '$alter',
            '$alkoholgehalt',
            '$flaschengroesse',
            '$abfueller',
            '$kaufdatum',
            '$kaufpreis',
            ".($bildPath ? "'$bildPath'" : "NULL").",
            ".($pdfPath ? "'$pdfPath'" : "NULL")."
        )";

    if ($conn->query($sql) === TRUE) {
        echo "<p style='color:limegreen;'>Whisky erfolgreich gespeichert!</p>";
    } else {
        echo "<p style='color:red;'>Fehler beim Speichern: ".$conn->error."</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="de">
<head>
<meta charset="UTF-8">
<title>Whiskyerfassung</title>

<style>
html, body {
    margin: 0;
    padding: 0;
    font-family: 'Open Sans', sans-serif;
    height: 100%;
    color: #e0c097;
}

/* === LOKALE FONTS (Fallback-sicher) === */
@font-face {
    font-family: 'Cinzel';
    src:
        url('fonts/Cinzel-Regular.woff2') format('woff2'),
        url('fonts/Cinzel-Regular.ttf') format('truetype');
    font-weight: 500;
    font-style: normal;
}

@font-face {
    font-family: 'Open Sans';
    src:
        url('fonts/OpenSans-Light.woff2') format('woff2'),
        url('fonts/OpenSans-Light.ttf') format('truetype');
    font-weight: 300;
    font-style: normal;
}

@font-face {
    font-family: 'Open Sans';
    src:
        url('fonts/OpenSans-Regular.woff2') format('woff2'),
        url('fonts/OpenSans-Regular.ttf') format('truetype');
    font-weight: 400;
    font-style: normal;
}

/* KOMPAKTES, ZENTRIERTES FORMULAR */
.container {
    position: relative;
    z-index: 1;
    width: 450px;
    max-width: 90%;
    margin: 0 auto;
    padding: 25px;
    margin-top: 20px;
    background: rgba(20, 12, 8, 0.55);
    border-radius: 12px;
    box-shadow: 0 0 25px rgba(0,0,0,0.5);
}

h1.page-title {
    font-family:'Cinzel', serif;
    font-size:2.4em;
    text-align:center;
    color:#f4d58d;
    margin-top: 25px;
    margin-bottom: 15px;
}

input {
    width: 100%;
    max-width: 380px;
    padding: 10px;
    margin: 10px auto;
    display: block;
    background: #2b1a0d;
    color: #f4d58d;
    border-radius: 6px;
    border: 1px solid #c39a6a;
    font-size: 1.05em;
}

.file-upload {
    width: 48%;
    display: inline-block;
}

.file-upload input[type="file"] {
    width: 100%;
}

button {
    width: 100%;
    max-width: 380px;
    padding: 12px;
    margin: 20px auto;
    font-size: 1.2em;
    background: #c39a6a;
    color: #2b1a0d;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    display: block;
}
button:hover { background:#9f7e56; }

#background {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    z-index: -1 !important;
    background: radial-gradient(circle at bottom right, #2b1a0d, #0e0704 80%);
}
</style>
</head>

<body>
<canvas id="background"></canvas>

<h1 class="page-title">Whiskyerfassung</h1>

<div class="container">
<form method="post" enctype="multipart/form-data">

    <input type="text" name="name" placeholder="Name des Whiskys">
    <input type="text" name="brennerei" placeholder="Brennerei">
    <input type="text" name="land_region" placeholder="Land / Region">
    <input type="text" name="sorte" placeholder="Sorte">
    <input type="text" name="alter" placeholder="Alter">
    <input type="text" name="alkoholgehalt" placeholder="Alkoholgehalt">
    <input type="text" name="flaschengroesse" placeholder="Flaschengroesse">
    <input type="text" name="abfueller" placeholder="Abfueller">
    <input type="text" name="kaufdatum" placeholder="Kaufdatum z.b. 01.02.2025">
    <input type="text" name="kaufpreis" placeholder="Kaufpreis">

    <div class="file-upload">
        <label>Bild hochladen</label>
        <input type="file" name="bild" accept=".jpg,.jpeg,.png,.gif">
    </div>

    <div class="file-upload">
        <label>PDF hochladen</label>
        <input type="file" name="pdf" accept=".pdf">
    </div>

    <button type="submit">Whisky registrieren</button>
</form>
</div>

<script>
// Hintergrundanimation
const canvas = document.getElementById('background');
const ctx = canvas.getContext('2d');
let width = canvas.width = window.innerWidth;
let height = canvas.height = window.innerHeight;

window.addEventListener('resize', () => {
  width = canvas.width = window.innerWidth;
  height = canvas.height = window.innerHeight;
});

const bubbles = [];
for (let i = 0; i < 60; i++) {
  bubbles.push({
    x: Math.random() * width,
    y: Math.random() * height,
    r: 1 + Math.random() * 3,
    dx: (Math.random() - 0.5) * 0.5,
    dy: (Math.random() - 0.5) * 0.5,
    color: `rgba(255, ${150 + Math.random()*80}, 50, ${0.05 + Math.random()*0.15})`
  });
}

function animate() {
  ctx.fillStyle = 'rgba(15, 8, 4, 0.2)';
  ctx.fillRect(0, 0, width, height);

  bubbles.forEach(b => {
    ctx.beginPath();
    ctx.arc(b.x, b.y, b.r, 0, Math.PI*2);
    ctx.fillStyle = b.color;
    ctx.fill();

    b.x += b.dx;
    b.y += b.dy;

    if (b.x > width || b.x < 0) b.dx *= -1;
    if (b.y > height || b.y < 0) b.dy *= -1;
  });

  requestAnimationFrame(animate);
}
animate();
</script>

</body>
</html>
