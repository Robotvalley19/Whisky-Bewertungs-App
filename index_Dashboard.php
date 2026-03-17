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
// Gesamtanzahl aller Whiskys
// =======================
$gesamtResult = $conn->query("SELECT COUNT(*) as gesamt FROM whisky");
$gesamtRow = $gesamtResult->fetch_assoc();
$gesamtAnzahlWhiskys = $gesamtRow['gesamt'] ?? 0;

// =======================
// Gesamtanzahl aller Flaschen (NEU)
// =======================
$flaschenResult = $conn->query("SELECT SUM(Anzahl_der_Flaschen) as gesamtflaschen FROM whisky");
$flaschenRow = $flaschenResult->fetch_assoc();
$gesamtAnzahlFlaschen = $flaschenRow['gesamtflaschen'] ?? 0;

if ($gesamtAnzahlFlaschen === null) {
    $gesamtAnzahlFlaschen = 0;
}

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

    if (isset($_POST['update_fassreifung'])) {
        $val = $_POST['fassreifung'] ?? null;
        $val_sql = $val !== '' ? "'".$conn->real_escape_string($val)."'" : "NULL";
        $conn->query("UPDATE whisky SET Fassreifung=$val_sql WHERE id=$id");
        exit("OK");
    }

    if (isset($_POST['update_beschreibung'])) {
        $val = $_POST['beschreibung'] ?? null;
        $val_sql = $val !== '' ? "'".$conn->real_escape_string($val)."'" : "NULL";
        $conn->query("UPDATE whisky SET Beschreibung=$val_sql WHERE id=$id");
        exit("OK");
    }

    if (isset($_POST['update_datum'])) {
        $datum = convertDate($_POST['Datum_der_Flaschenoeffnung'] ?? '');
        $datum_sql = $datum ? "'$datum'" : "NULL";
        $conn->query("UPDATE whisky SET Datum_der_Flaschenoeffnung=$datum_sql WHERE id=$id");
        exit("OK");
    }

    if (isset($_POST['update_grund'])) {
        $val = $_POST['Grund_der_Flaschenoeffnung'] ?? null;
        $val_sql = $val !== '' ? "'".$conn->real_escape_string($val)."'" : "NULL";
        $conn->query("UPDATE whisky SET Grund_der_Flaschenoeffnung=$val_sql WHERE id=$id");
        exit("OK");
    }

    if (isset($_POST['update_status'])) {
        $val = $_POST['status'] ?? null;
        $val_sql = $val !== '' ? "'".$conn->real_escape_string($val)."'" : "NULL";
        $conn->query("UPDATE whisky SET Status=$val_sql WHERE id=$id");
        exit("OK");
    }

    if (isset($_POST['update_fundort'])) {
        $val = $_POST['fundort'] ?? null;
        $val_sql = $val !== '' ? "'".$conn->real_escape_string($val)."'" : "NULL";
        $conn->query("UPDATE whisky SET Fundort=$val_sql WHERE id=$id");
        exit("OK");
    }

    if (isset($_POST['update_anzahl'])) {
        $anzahl = intval($_POST['Anzahl_der_Flaschen'] ?? 0);
        $conn->query("UPDATE whisky SET Anzahl_der_Flaschen=$anzahl WHERE id=$id");
        exit("OK");
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
    <div class="sidebar">
        <h2>Filter</h2>

        <div class="whisky-gesamt">
            Whiskysorten: <?= $gesamtAnzahlWhiskys ?>
        </div>

        <div class="whisky-gesamt">
            Anzahl der Flaschen: <?= $gesamtAnzahlFlaschen ?>
        </div>
        
        <form method="get">
            <?php
            $filter_fields = ['Name','Brennerei','Land_Region','Sorte','Alter','Alkoholgehalt','Flaschengroesse','Abfueller','Kaufdatum','Kaufpreis','Status','Fundort','Anzahl_der_Flaschen'];
            foreach($filter_fields as $f) {
                $val = $_GET[$f] ?? '';
                echo "<label>$f</label><input type='text' name='$f' value='".htmlspecialchars($val, ENT_QUOTES, 'UTF-8')."'>";
            }
            ?>
            <button type="submit">Filter anwenden</button>
            <button type="button" onclick="window.location='';">Filter zuruecksetzen</button>
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
                <p><strong>Brennerei:</strong> <?= htmlspecialchars($row['Brennerei'] ?? '', ENT_QUOTES, 'UTF-8') ?></p>
                <p><strong>Land/Region:</strong> <?= htmlspecialchars($row['Land_Region'] ?? '', ENT_QUOTES, 'UTF-8') ?></p>
                <p><strong>Sorte:</strong> <?= htmlspecialchars($row['Sorte'] ?? '', ENT_QUOTES, 'UTF-8') ?></p>
                <p><strong>Alter:</strong> <?= htmlspecialchars($row['Alter'] ?? '', ENT_QUOTES, 'UTF-8') ?></p>
                <p><strong>Alkoholgehalt:</strong> <?= htmlspecialchars($row['Alkoholgehalt'] ?? '', ENT_QUOTES, 'UTF-8') ?>%</p>
                <p><strong>Flaschengroesse:</strong> <?= htmlspecialchars($row['Flaschengroesse'] ?? '', ENT_QUOTES, 'UTF-8') ?>l</p>
                <p><strong>Abfueller:</strong> <?= htmlspecialchars($row['Abfueller'] ?? '', ENT_QUOTES, 'UTF-8') ?></p>
                <p><strong>Kaufdatum:</strong> <?= htmlspecialchars($row['Kaufdatum'] ?? '', ENT_QUOTES, 'UTF-8') ?></p>
                <p><strong>Kaufpreis:</strong> <?= htmlspecialchars($row['Kaufpreis'] ?? '', ENT_QUOTES, 'UTF-8') ?> Euro</p>

                <!-- Editable Fields -->
                <p><strong>Fassreifung:</strong><br>
                    <textarea class="fassreifung-input"><?= htmlspecialchars($row['Fassreifung'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
                    <button class="save-btn" data-field="fassreifung" data-id="<?= $row['id'] ?>">Speichern</button>
                </p>
                <p><strong>Beschreibung:</strong><br>
                    <textarea class="beschreibung-input"><?= htmlspecialchars($row['Beschreibung'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
                    <button class="save-btn" data-field="beschreibung" data-id="<?= $row['id'] ?>">Speichern</button>
                </p>
                <p><strong>Datum der Flaschenoeffnung:</strong><br>
                    <input type="text" class="datum-input" value="<?= htmlspecialchars($row['Datum_der_Flaschenoeffnung'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                    <button class="save-btn" data-field="datum" data-id="<?= $row['id'] ?>">Speichern</button>
                </p>
                <p><strong>Grund der Flaschenoeffnung:</strong><br>
                    <textarea class="grund-textarea"><?= htmlspecialchars($row['Grund_der_Flaschenoeffnung'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
                    <button class="save-btn" data-field="grund" data-id="<?= $row['id'] ?>">Speichern</button>
                </p>
                 <p><strong>Status:</strong><br>
                    <select class="status-select"><?= implode('', array_map(fn($opt)=>"<option value='$opt'".($row['Status']===$opt?' selected':'').">$opt</option>", $statusOptionen)) ?></select>
                    <button class="save-btn" data-field="status" data-id="<?= $row['id'] ?>">Speichern</button>
                </p>
                <p><strong>Fundort:</strong><br>
                    <input type="text" class="fundort-input" value="<?= htmlspecialchars($row['Fundort'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                    <button class="save-btn" data-field="fundort" data-id="<?= $row['id'] ?>">Speichern</button>
                </p>
                <p><strong>Anzahl der Flaschen:</strong><br>
                    <input type="number" class="anzahl-flaschen-input" value="<?= htmlspecialchars($row['Anzahl_der_Flaschen'] ?? 0, ENT_QUOTES, 'UTF-8') ?>">
                    <button class="save-btn" data-field="anzahl" data-id="<?= $row['id'] ?>">Speichern</button>
                </p>

                <?php if(!empty($row['Bild'])): ?><a href="<?= htmlspecialchars($bildUrl) ?>" download class="download-link">Bild herunterladen</a><?php endif; ?>
                <?php if(!empty($row['PDF'])): $pdfUrl = $uploadWebPath.basename($row['PDF']); ?>
                    <a href="<?= htmlspecialchars($pdfUrl) ?>" download class="download-link">PDF herunterladen</a>
                <?php endif; ?>
            </div>
        </div>
    <?php endwhile; ?>
    </div>
</div>

<script>
// Bubble-Hintergrund
const canvas=document.getElementById('background');
const ctx=canvas.getContext('2d');
let width=canvas.width=window.innerWidth,height=canvas.height=window.innerHeight;
window.addEventListener('resize',()=>{width=canvas.width=window.innerWidth;height=canvas.height=window.innerHeight;});
const bubbles=[];for(let i=0;i<60;i++){bubbles.push({x:Math.random()*width,y:Math.random()*height,r:1+Math.random()*3,dx:(Math.random()-0.5)*0.5,dy:(Math.random()-0.5)*0.5,color:`rgba(255,${150+Math.random()*80},50,${0.05+Math.random()*0.15})`});}
function animate(){ctx.fillStyle='rgba(15,8,4,0.2)';ctx.fillRect(0,0,width,height);bubbles.forEach(b=>{ctx.beginPath();ctx.arc(b.x,b.y,b.r,0,2*Math.PI);ctx.fillStyle=b.color;ctx.fill();b.x+=b.dx;b.y+=b.dy;if(b.x>width||b.x<0)b.dx*=-1;if(b.y>height||b.y<0)b.dy*=-1;});requestAnimationFrame(animate);}
animate();

// Karten aufklappen
document.querySelectorAll('.card h2').forEach(h2=>{h2.addEventListener('click',e=>{e.target.closest('.card').classList.toggle('active');});});

// AJAX speichern
document.querySelectorAll('.save-btn').forEach(btn=>{
    btn.addEventListener('click', ()=>{
        const id = btn.dataset.id;
        const field = btn.dataset.field;
        let val;
        const card = btn.closest('.card');
        switch(field){
            case 'fassreifung': val = card.querySelector('.fassreifung-input').value; break;
            case 'beschreibung': val = card.querySelector('.beschreibung-input').value; break;
            case 'datum': val = card.querySelector('.datum-input').value; break;
            case 'grund': val = card.querySelector('.grund-textarea').value; break;
            case 'status': val = card.querySelector('.status-select').value; break;
            case 'fundort': val = card.querySelector('.fundort-input').value; break;
            case 'anzahl': val = card.querySelector('.anzahl-flaschen-input').value; break;
            default: return;
        }
        const data = new FormData();
        data.append('id', id);
        data.append(`update_${field}`, 1);
        data.append({
            'fassreifung':'fassreifung',
            'beschreibung':'beschreibung',
            'datum':'Datum_der_Flaschenoeffnung',
            'grund':'Grund_der_Flaschenoeffnung',
            'status':'status',
            'fundort':'fundort',
            'anzahl':'Anzahl_der_Flaschen'
        }[field], val);

        fetch('', {method:'POST', body:data}).then(r=>r.text()).then(r=>{if(r==='OK'){alert('Gespeichert');} else{alert('Fehler');}});
    });
});
</script>
</body>
</html>
