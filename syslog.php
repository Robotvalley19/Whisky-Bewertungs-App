<?php
// Pfad zur JSON-Datei (Home-Verzeichnis)
$json_file = 'Pfad';

if (!file_exists($json_file)) {
    die("JSON-Datei nicht gefunden: $json_file");
}

$data = json_decode(file_get_contents($json_file), true);

// Farben fuer Status
function statusColor($status) {
    return $status ? '#28a745' : '#dc3545';
}

// Syslog-Quelle Hinweis
$syslog_source = 'Letzte 20 Eintraege aus journalctl, gesammelt im Backup-Skript';

// CPU-Auslastung Prozent (wie bei RAM)
$cpu_percent = isset($data['cpu_load_percent']) ? $data['cpu_load_percent'] : round(floatval($data['cpu_load_1min'])*100, 1);

// SD-Karteninformationen
$sd_total = isset($data['sd_total']) ? $data['sd_total'] : 'n/a';
$sd_used  = isset($data['sd_used']) ? $data['sd_used'] : 'n/a';
?>
<!DOCTYPE html>
<html lang="de">
<head>
<meta charset="UTF-8">
<title>Whisky Pi Health Dashboard</title>
<style>
/* --------------- Dark Whisky Theme --------------- */
body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background-color: #1e1b18;
    color: #f5f5f5;
    margin: 0;
    padding: 20px;
}
h1 { text-align: center; margin-bottom: 30px; color: #f4c542; }

.card {
    background-color: #2e2a23;
    border-radius: 12px;
    padding: 20px;
    margin: 15px auto;
    max-width: 900px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.6);
}

.card h2 {
    margin-top: 0;
    border-bottom: 1px solid #444;
    padding-bottom: 5px;
    color: #f4c542;
}

.status {
    font-weight: bold;
    padding: 4px 10px;
    border-radius: 4px;
    color: #fff;
}

pre {
    background: #222;
    padding: 15px;
    border-radius: 8px;
    max-height: 400px;
    overflow-y: scroll;
    color: #c0c0c0;
}

/* Grid fuer Ressourcen */
.grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
    gap: 15px;
}
.label { font-weight: bold; }
</style>
</head>
<body>

<h1>Whisky Pi Health & Backup Dashboard</h1>

<div class="card">
<h2>Status Uebersicht</h2>
<p>Host: <strong><?= htmlspecialchars($data['host']) ?></strong></p>
<p>Letzter Check: <strong><?= htmlspecialchars($data['timestamp']) ?></strong></p>
<p>Status: <span class="status" style="background-color: <?= $data['status'] === 'OK' ? '#28a745' : '#dc3545' ?>"><?= htmlspecialchars($data['status']) ?></span></p>
<p>SD-Karte gemountet: <span class="status" style="background-color: <?= statusColor($data['sd_mounted']) ?>"><?= $data['sd_mounted'] ? 'OK' : 'FEHLER' ?></span></p>
<p>MariaDB erreichbar: <span class="status" style="background-color: <?= statusColor($data['mariadb_alive']) ?>"><?= $data['mariadb_alive'] ? 'OK' : 'FEHLER' ?></span></p>
<p>Upload-Ordner vorhanden: <span class="status" style="background-color: <?= statusColor($data['upload_dir_exists']) ?>"><?= $data['upload_dir_exists'] ? 'OK' : 'FEHLER' ?></span></p>
<p>Disk in Ordnung: <span class="status" style="background-color: <?= statusColor($data['disk_ok']) ?>"><?= $data['disk_ok'] ? 'OK' : 'FEHLER' ?></span></p>
<p>Backup erstellt: <span class="status" style="background-color: <?= statusColor($data['backup_done']) ?>"><?= $data['backup_done'] ? 'JA' : 'NEIN' ?></span></p>
<p>Syslog Quelle: <strong><?= htmlspecialchars($syslog_source) ?></strong></p>
</div>

<div class="card">
<h2>System-Ressourcen</h2>
<div class="grid">
    <div><span class="label">CPU-Auslastung:</span> <?= $cpu_percent ?>%</div>
    <div><span class="label">RAM-Auslastung:</span> <?= htmlspecialchars($data['ram_usage_percent']) ?>%</div>
    <div><span class="label">CPU Temperatur:</span> <?= htmlspecialchars($data['cpu_temp_celsius']) ?>Â°C</div>
    <div><span class="label">SD-Karte Gesamt:</span> <?= htmlspecialchars($sd_total) ?></div>
    <div><span class="label">SD-Karte Belegt:</span> <?= htmlspecialchars($sd_used) ?></div>
</div>
</div>

<div class="card">
<h2>Letzte Syslog-Eintraege</h2>
<pre><?= htmlspecialchars($data['syslog_recent']) ?></pre>
</div>

</body>
</html>
