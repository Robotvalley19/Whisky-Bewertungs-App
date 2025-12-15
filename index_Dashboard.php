<?php
// ===========================================
// Whisky Dashboard mit Sidebar-Filtern + AJAX Speicherung + Datumskonvertierung + Bubble-Hintergrund
// ===========================================

mb_internal_encoding("UTF-8");
mb_http_output("UTF-8");
ini_set('default_charset', 'UTF-8');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$servername = "localhost"; 
$username = "eigener User";       
$password = "eigene Passwort";    
$dbname = "Whiskybewertungen";    

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) die("Verbindung fehlgeschlagen: " . $conn->connect_error);
$conn->set_charset("utf8mb4");

$statusOptionen = ['Geschlossen','Offen','Leer','Sample'];
$uploadWebPath = "uploads/";

// =======================
// AJAX Speicherung
// =======================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = intval($_POST['id'] ?? 0);
    if ($id <= 0) exit("ERROR");

    function convertDate($input) {
        if (empty($input)) return null;
        $date = DateTime::createFromFormat('d.m.Y', $input);
        if (!$date) $date = DateTime::createFromFormat('Y-m-d', $input);
        return $date ? $date->format('Y-m-d') : null;
    }

    $fieldsMap = [
        'fassreifung' => 'Fassreifung',
        'beschreibung' => 'Beschreibung',
        'datum' => 'Datum_der_Flaschenoeffnung',
        'grund' => 'Grund_der_Flaschenoeffnung',
        'status' => 'Status',
        'fundort' => 'Fundort',
        'anzahl' => 'Anzahl_der_Flaschen'
    ];

    foreach($fieldsMap as $key=>$column){
        if(isset($_POST["update_$key"])){
            $val = $_POST[$column] ?? null;
            if($key==='datum') $val = convertDate($val);
            $val_sql = $val !== '' && $val !== null ? "'".$conn->real_escape_string($val)."'" : "NULL";
            $conn->query("UPDATE whisky SET $column=$val_sql WHERE id=$id");
            exit("OK");
        }
    }
}

// =======================
// Filter & Pagination
// =======================
$filterSQL = "WHERE 1=1";
foreach ($_GET as $key => $value) {
    if ($value !== '' && $key !== 'page') {
        $safeKey = $conn->real_escape_string($key);
        $safeValue = $conn->real_escape_string($value);
        $filterSQL .= " AND `$safeKey` LIKE '%$safeValue%'";
    }
}

$limit = 50;
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$offset = ($page - 1) * $limit;

$result = $conn->query("SELECT * FROM whisky $filterSQL ORDER BY id DESC LIMIT $offset, $limit");
$totalResult = $conn->query("SELECT COUNT(*) as total FROM whisky $filterSQL");
$totalRow = $totalResult->fetch_assoc();
$totalPages = ceil($totalRow['total'] / $limit);
?>

<!DOCTYPE html>
<html lang="de">
<head>
<meta charset="UTF-8">
<title>Whisky Dashboard</title>

<style>

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

/* ---- Styles unverändert ---- */
html,body{margin:0;padding:0;font-family:'Open Sans',sans-serif;height:100%;background:radial-gradient(circle at bottom right,#2b1a0d,#0e0704 80%);color:#e0c097;overflow-x:hidden;}
#background{position:fixed;top:0;left:0;width:100%;height:100%;z-index:0;}
h1.page-title{font-family:'Cinzel',serif;font-size:3em;text-align:center;margin:20px 0;color:#f4d58d;text-shadow:0 0 15px rgba(244,213,141,0.4);position:relative;z-index:1;}
.container{display:flex;max-width:1400px;margin:auto;padding:20px;position:relative;z-index:1;}
.sidebar{width:250px;background:rgba(33,21,13,0.9);border-radius:15px;padding:15px;margin-right:20px;max-height:90vh;overflow-y:auto;}
.sidebar h2{font-family:'Cinzel',serif;color:#f4d58d;margin-top:0;font-size:1.5em;text-align:center;}
.sidebar label{display:block;margin:10px 0 5px;font-weight:bold;}
.sidebar input,.sidebar select{width:100%;padding:5px;border-radius:5px;border:1px solid #c39a6a;background:#2b1a0d;color:#f4d58d;}
.grid{flex:1;display:grid;grid-template-columns:repeat(auto-fit,minmax(250px,1fr));gap:20px;}
.card{background:rgba(33,21,13,0.85);border-radius:15px;cursor:pointer;overflow:hidden;transition:all 0.5s ease;position:relative;box-shadow:0 0 10px rgba(0,0,0,0.4);}
.card:hover{transform:scale(1.02);}
.card img{width:100%;height:180px;object-fit:cover;display:block;border-bottom:1px solid #c39a6a;opacity:0.9;}
.card h2{margin:10px 0;font-family:'Cinzel',serif;text-align:center;color:#f4d58d;cursor:pointer;}
.card-content{padding:15px;color:#e0c097;max-height:0;opacity:0;overflow:hidden;transition:all 0.6s ease;}
.card.active .card-content{max-height:2000px;opacity:1;}
.download-link{display:inline-block;padding:6px 12px;margin-top:5px;background:#c39a6a;color:#fff;border-radius:5px;text-decoration:none;transition:background 0.3s ease;}
.download-link:hover{background:#9f7e56;}
textarea,input,select{background:#2b1a0d;color:#f4d58d;border:1px solid #c39a6a;border-radius:5px;padding:4px 8px;margin-top:10px;width:100%;}
textarea:hover,input:hover,select:hover{background:#3d2616;}
button{margin-top:5px;padding:5px 10px;border-radius:5px;background:#c39a6a;color:#fff;border:none;cursor:pointer;}
</style>
</head>
<body>

<canvas id="background"></canvas>
<h1 class="page-title">Whisky Dashboard</h1>

<div class="container">
    <!-- Sidebar Filter -->
    <div class="sidebar">
        <h2>Filter</h2>
        <form method="get">
            <?php
            $filter_fields = ['Name','Brennerei','Land_Region','Sorte','Alter','Alkoholgehalt','Flaschengroesse','Abfueller','Kaufdatum','Kaufpreis','Status','Fundort','Anzahl_der_Flaschen'];
            foreach($filter_fields as $f) {
                $val = $_GET[$f] ?? '';
                echo "<label>$f</label><input type='text' name='$f' value='".htmlspecialchars($val, ENT_QUOTES, 'UTF-8')."'>";
            }
            ?>
            <button type="submit">Filter anwenden</button>
            <button type="button" onclick="window.location='';">Filter zurücksetzen</button>
        </form>
    </div>

    <!-- Grid -->
    <div class="grid">
    <?php while($row = $result->fetch_assoc()): ?>
        <div class="card" data-id="<?= $row['id'] ?>">
            <?php if(!empty($row['Bild'])): $bildUrl = $uploadWebPath.basename($row['Bild']); ?>
                <img src="<?= htmlspecialchars($bildUrl, ENT_QUOTES, 'UTF-8') ?>" alt="<?= htmlspecialchars($row['Name'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
            <?php endif; ?>
            <h2><?= htmlspecialchars($row['Name'] ?? '', ENT_QUOTES, 'UTF-8') ?></h2>
            <div class="card-content">
                <!-- Inhalte & Editable Fields wie vorher -->
                <!-- ... gleich wie dein bestehendes Skript ... -->
            </div>
        </div>
    <?php endwhile; ?>
    </div>
</div>

<script>
// Bubble-Hintergrund & AJAX wie in deinem Skript
// ... unverändert ...
</script>
</body>
</html>
