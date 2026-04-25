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
$uploadWebPath = "/uploads/";

// =======================
$gesamtResult = $conn->query("SELECT COUNT(*) as gesamt FROM whisky");
$gesamtRow = $gesamtResult->fetch_assoc();
$gesamtAnzahlWhiskys = $gesamtRow['gesamt'] ?? 0;

$flaschenResult = $conn->query("SELECT SUM(Anzahl_der_Flaschen) as gesamtflaschen FROM whisky");
$flaschenRow = $flaschenResult->fetch_assoc();
$gesamtAnzahlFlaschen = $flaschenRow['gesamtflaschen'] ?? 0;
if ($gesamtAnzahlFlaschen === null) $gesamtAnzahlFlaschen = 0;

// =======================
// AJAX SAVE (MULTI UPDATE)
// =======================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    header('Content-Type: application/json; charset=utf-8');

    $data = json_decode($_POST['data'] ?? '[]', true);

    if (!is_array($data)) {
        echo json_encode(["status"=>"error"]);
        exit;
    }

    function convertDate($input) {
        if (empty($input)) return null;
        $date = DateTime::createFromFormat('d.m.Y', $input);
        if (!$date) $date = DateTime::createFromFormat('Y-m-d', $input);
        return $date ? $date->format('Y-m-d') : null;
    }

    foreach ($data as $item) {

        $id = intval($item['id'] ?? 0);
        $feld = $item['feld'] ?? '';
        $wert = $item['wert'] ?? null;

        if ($id <= 0 || $feld === '') continue;

        if ($feld === "Datum_der_Flaschenoeffnung" || $feld === "Kaufdatum") {
            $wert = convertDate($wert);
        }

        if ($wert === '' || $wert === null) {
            $sqlValue = "NULL";
        } else {
            $sqlValue = "'" . $conn->real_escape_string($wert) . "'";
        }

        $feld_safe = $conn->real_escape_string($feld);

        $conn->query("UPDATE whisky SET `$feld_safe` = $sqlValue WHERE id = $id");
    }

    echo json_encode(["status"=>"ok"]);
    exit;
}

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
?>

<!DOCTYPE html>
<html lang="de">
<head>
<meta charset="UTF-8">
<title>Whisky Dashboard</title>

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

<style>
html,body{margin:0;padding:0;font-family:'Open Sans',sans-serif;height:100%;background:radial-gradient(circle at bottom right,#2b1a0d,#0e0704 80%);color:#e0c097;overflow-x:hidden;}
#background{position:fixed;top:0;left:0;width:100%;height:100%;z-index:0;}
h1.page-title{font-family:'Cinzel',serif;font-size:3em;text-align:center;margin:20px 0;color:#f4d58d;text-shadow:0 0 15px rgba(244,213,141,0.4);position:relative;z-index:1;}

.container{display:flex;max-width:1400px;margin:auto;padding:20px;position:relative;z-index:1;}

.sidebar{width:250px;background:rgba(33,21,13,0.9);border-radius:15px;padding:15px;margin-right:20px;max-height:90vh;overflow-y:auto;}
.sidebar h2{font-family:'Cinzel',serif;color:#f4d58d;margin-top:0;font-size:1.5em;text-align:center;}
.whisky-gesamt{font-size:1.1em;text-align:center;margin-bottom:5px;color:#f4d58d;font-weight:bold;}

.grid{flex:1;display:grid;grid-template-columns:repeat(auto-fit,minmax(250px,1fr));gap:20px;}

.card{background:rgba(33,21,13,0.85);border-radius:15px;overflow:hidden;transition:all 0.5s ease;position:relative;}
.card:hover{transform:scale(1.02);}
.card img{width:100%;height:180px;object-fit:cover;display:block;border-bottom:1px solid #c39a6a;opacity:0.9;}
.card h2{margin:10px 0;font-family:'Cinzel',serif;text-align:center;color:#f4d58d;cursor:pointer;}

.card-content{padding:15px;color:#e0c097;max-height:0;opacity:0;overflow:hidden;transition:all 0.6s ease;}
.card.active .card-content{max-height:2000px;opacity:1;}

textarea,input,select{
width:100%;margin-top:5px;background:#2b1a0d;color:#f4d58d;
border:1px solid #c39a6a;border-radius:5px;padding:5px;
}

/* ========================= */
/* 💾 SPEICHERN BUTTON (UPGRADE)
/* ========================= */
#saveAll{
position:fixed;
top:20px;
right:20px;
z-index:999;
background:linear-gradient(135deg,#f4d58d,#c39a6a);
border:none;
padding:14px 22px;
border-radius:12px;
cursor:pointer;
font-weight:bold;
font-size:15px;
color:#1a0f08;
box-shadow:0 0 25px rgba(244,213,141,0.7);
transition:all 0.3s ease;
}

#saveAll:hover{
transform:scale(1.05);
box-shadow:0 0 35px rgba(244,213,141,1);
}

/* ========================= */
/* FILTER BUTTON (STYLE FIX)
/* ========================= */
.sidebar button[type="submit"]{
margin-top:10px;
width:100%;
padding:10px;
border:none;
border-radius:8px;
background:linear-gradient(135deg,#c39a6a,#8b5e3c);
color:#1a0f08;
font-weight:bold;
cursor:pointer;
transition:0.3s;
}

.sidebar button[type="submit"]:hover{
transform:scale(1.03);
}

.error{border:2px solid red !important;}
</style>
</head>

<body>

<canvas id="background"></canvas>

<button id="saveAll">💾 Speichern</button>

<h1 class="page-title">Whisky Dashboard</h1>

<div class="container">

<div class="sidebar">
<h2>Filter</h2>

<div class="whisky-gesamt">Whiskysorten: <?= $gesamtAnzahlWhiskys ?></div>
<div class="whisky-gesamt">Anzahl Flaschen: <?= $gesamtAnzahlFlaschen ?></div>

<form method="get">
<?php
$filter_fields = ['Name','Brennerei','Land_Region','Sorte','Alter','Alkoholgehalt','Flaschengroesse','Abfueller','Kaufdatum','Kaufpreis','Status','Fundort','Anzahl_der_Flaschen'];
foreach($filter_fields as $f){
$val = $_GET[$f] ?? '';
echo "<label>$f</label><input type='text' name='$f' value='".htmlspecialchars($val, ENT_QUOTES, 'UTF-8')."'>";
}
?>
<button type="submit">Filter anwenden</button>
</form>

</div>

<div class="grid">

<?php while($row = $result->fetch_assoc()): ?>
<div class="card" data-id="<?= $row['id'] ?>">

<?php if(!empty($row['Bild'])): ?>
<img src="<?= $uploadWebPath.basename($row['Bild']) ?>">
<?php endif; ?>

<h2><?= htmlspecialchars($row['Name']) ?></h2>

<div class="card-content">

<?php
$felder = [
'Name','Brennerei','Land_Region','Sorte','Alter','Alkoholgehalt',
'Flaschengroesse','Abfueller','Kaufdatum','Kaufpreis',
'Fassreifung','Beschreibung','Datum_der_Flaschenoeffnung',
'Grund_der_Flaschenoeffnung','Status','Fundort','Anzahl_der_Flaschen'
];

foreach($felder as $feld):
$value = htmlspecialchars($row[$feld] ?? '', ENT_QUOTES, 'UTF-8');
?>

<p><strong><?= $feld ?>:</strong>

<?php if($feld === 'Status'): ?>
<select data-feld="<?= $feld ?>">
<?php foreach($statusOptionen as $opt): ?>
<option value="<?= $opt ?>" <?= $value==$opt?'selected':'' ?>><?= $opt ?></option>
<?php endforeach; ?>
</select>

<?php elseif(in_array($feld,['Beschreibung','Fassreifung','Grund_der_Flaschenoeffnung'])): ?>
<textarea data-feld="<?= $feld ?>"><?= $value ?></textarea>

<?php else: ?>
<input type="text" data-feld="<?= $feld ?>" value="<?= $value ?>">
<?php endif; ?>

</p>

<?php endforeach; ?>

</div>
</div>
<?php endwhile; ?>

</div>
</div>

<script>

document.querySelectorAll('.card h2').forEach(h=>{
h.onclick=()=>h.closest('.card').classList.toggle('active');
});

let changes=[];

document.querySelectorAll('.card').forEach(card=>{
card.querySelectorAll('[data-feld]').forEach(el=>{
el.addEventListener('input',()=>{

const id=card.dataset.id;
const feld=el.dataset.feld;
const wert=el.value;

let e=changes.find(x=>x.id==id && x.feld==feld);
if(!e){changes.push({id,feld,wert});}
else{e.wert=wert;}

if(feld==='Name' && wert.trim()===''){
el.classList.add('error');
}else{
el.classList.remove('error');
}

});
});
});

document.getElementById('saveAll').onclick=()=>{

let invalid=changes.find(c=>c.feld==='Name' && (!c.wert || c.wert.trim()===''));
if(invalid){
alert("Name darf nicht leer sein!");
return;
}

fetch('',{
method:'POST',
body:new URLSearchParams({
data:JSON.stringify(changes)
})
})
.then(r=>r.json())
.then(res=>{
if(res.status==='ok'){
alert("Gespeichert");
changes=[];
}else{
alert("Fehler beim Speichern");
}
});

};

</script>

</body>
</html>
