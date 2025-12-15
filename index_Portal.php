<?php
// Whisky Portal: Dienste mit Vorschau
$dienste = [
    ["name" => "phpMyAdmin", "port" => 5001],
    ["name" => "Whiskyerfassung", "url"  => "http://192.168.178.96:5000/index_Bewertung.php"],
    ["name" => "Whisky Dashboard", "url"  => "http://192.168.178.96:5000/index_Dashboard.php"],
];

// Aktuelle LAN-IP automatisch ermitteln
$server_ip = gethostbyname(gethostname());

// Fallback, falls IP 127.0.x.x ist
if ($server_ip === "127.0.0.1" || $server_ip === "127.0.1.1") {
    $server_ip = "192.168.178.96"; // Deine LAN-IP
}
?>
<!DOCTYPE html>
<html lang="de">
<head>
<meta charset="UTF-8">
<title>Whiskylounge</title>
<style>
html, body {
    margin: 0;
    height: 100%;
    font-family: 'Open Sans', sans-serif;
    background: radial-gradient(circle at bottom right, #2b1a0d, #0e0704 80%);
    color: #e0c097;
    overflow: hidden;
}

h1 {
    margin: 20px 0 40px 0;
    font-size: 3em;
    text-align: center;
    font-family: 'Cinzel', serif;
    color: #f4d58d;
    text-shadow: 0 0 15px rgba(244, 213, 141, 0.3);
    position: relative;
    z-index: 2;
}

.container {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 25px;
    width: 90%;
    max-width: 1200px;
    margin: auto;
    position: relative;
    z-index: 2;
}

.card {
    background: rgba(33, 21, 13, 0.85);
    border: 1px solid rgba(153, 102, 51, 0.6);
    border-radius: 15px;
    padding: 25px 15px;
    text-align: center;
    transition: 0.3s;
    box-shadow: 0 0 10px rgba(255, 215, 128, 0.1);
}

.card:hover {
    transform: scale(1.05);
    box-shadow: 0 0 25px rgba(255, 215, 128, 0.3);
}

.card h2 {
    font-family: 'Cinzel', serif;
    color: #f8d686;
    font-size: 1.4em;
    margin-bottom: 10px;
}

.card p {
    color: #d4b87b;
    margin: 0;
}

.card .open-link {
    margin-top: 15px;
    display: inline-block;
    padding: 10px 18px;
    background: linear-gradient(90deg, #b48845, #f1c27d);
    color: #2b1a0d;
    border-radius: 10px;
    text-decoration: none;
    font-weight: bold;
    transition: 0.3s;
}

.card .open-link:hover {
    background: linear-gradient(90deg, #f1c27d, #b48845);
    transform: translateY(-2px);
}

/* Canvas Hintergrund */
#background {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    z-index: 1;
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

</style>
</head>
<body>
<canvas id="background"></canvas>

<h1>Whiskylounge</h1>
<div class="container">
<?php foreach($dienste as $d): ?>
    <div class="card">
        <h2><?= htmlspecialchars($d['name']) ?></h2>
        <?php if (isset($d['url'])): ?>
            <p>Direktlink</p>
            <a class="open-link" href="<?= htmlspecialchars($d['url']) ?>" target="_blank">Seite oeffnen</a>
        <?php else: ?>
            <p>Port: <?= htmlspecialchars($d['port']) ?></p>
            <?php
                $host = $d['host'] ?? $server_ip;
                $url = "http://{$host}:{$d['port']}";
            ?>
            <a class="open-link" href="<?= htmlspecialchars($url) ?>" target="_blank">Seite oeffnen</a>
        <?php endif; ?>
    </div>
<?php endforeach; ?>
</div>

<script>
// Dynamischer Whisky-Hintergrund: warme, fliesende Partikel
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
    ctx.arc(b.x, b.y, b.r, 0, Math.PI * 2);
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
