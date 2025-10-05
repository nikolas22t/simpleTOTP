<?php
// PHP TOTP manager with per-user session storage
session_start();
$timeStep = 30;
$digits = 6;

function base32_decode_custom($b32) {
    $alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
    $b32 = strtoupper($b32);
    $b32 = preg_replace('/[^A-Z2-7]/', '', $b32);
    $bits = '';
    foreach (str_split($b32) as $char) {
        $val = strpos($alphabet, $char);
        $bits .= str_pad(decbin($val), 5, '0', STR_PAD_LEFT);
    }
    $bytes = '';
    foreach (str_split($bits, 8) as $byte) {
        if (strlen($byte) < 8) continue;
        $bytes .= chr(bindec($byte));
    }
    return $bytes;
}

function hotp($key, $counter, $digits = 6) {
    $counterBytes = pack('N*', 0) . pack('N*', $counter);
    $hash = hash_hmac('sha1', $counterBytes, $key, true);
    $offset = ord($hash[19]) & 0xf;
    $binary = (ord($hash[$offset]) & 0x7f) << 24 |
              (ord($hash[$offset+1]) & 0xff) << 16 |
              (ord($hash[$offset+2]) & 0xff) << 8 |
              (ord($hash[$offset+3]) & 0xff);
    return str_pad($binary % pow(10, $digits), $digits, '0', STR_PAD_LEFT);
}

function totp($secretBase32, $timeStep = 30, $digits = 6, $time = null) {
    if ($time === null) $time = time();
    $counter = floor($time / $timeStep);
    $key = base32_decode_custom($secretBase32);
    return hotp($key, $counter, $digits);
}

// Load entries from session
$entries = $_SESSION['totp_entries'] ?? [];

// Handle actions: add, delete, clear, import, sort, move
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_name'], $_POST['add_secret'])) {
        $name = trim($_POST['add_name']);
        $secret = trim($_POST['add_secret']);
        if ($name !== '' && $secret !== '') {
            $entries[] = ['name' => $name, 'secret' => $secret];
            $_SESSION['totp_entries'] = $entries;
            header('Location: ' . strtok($_SERVER['REQUEST_URI'], '?'));
            exit;
        }
    }

    if (isset($_POST['delete_index'])) {
        $i = intval($_POST['delete_index']);
        if (isset($entries[$i])) {
            array_splice($entries, $i, 1);
            $_SESSION['totp_entries'] = $entries;
            header('Location: ' . strtok($_SERVER['REQUEST_URI'], '?'));
            exit;
        }
    }

    if (isset($_POST['clear_all'])) {
        $entries = [];
        $_SESSION['totp_entries'] = $entries;
        header('Location: ' . strtok($_SERVER['REQUEST_URI'], '?'));
        exit;
    }

    if (isset($_FILES['import_file']) && $_FILES['import_file']['error'] === 0) {
        $json = file_get_contents($_FILES['import_file']['tmp_name']);
        $decoded = json_decode($json, true);
        if (is_array($decoded)) {
            $entries = $decoded;
            $_SESSION['totp_entries'] = $entries;
            header('Location: ' . strtok($_SERVER['REQUEST_URI'], '?'));
            exit;
        }
    }

    // Sort alphabetically
    if (isset($_POST['sort']) && $_POST['sort'] === 'name') {
        usort($entries, fn($a, $b) => strcasecmp($a['name'], $b['name']));
        $_SESSION['totp_entries'] = $entries;
        header('Location: ' . strtok($_SERVER['REQUEST_URI'], '?'));
        exit;
    }

    // Move entries up/down
    if (isset($_POST['move_index'], $_POST['move'])) {
        $i = intval($_POST['move_index']);
        if ($_POST['move'] === 'up' && $i > 0) {
            [$entries[$i-1], $entries[$i]] = [$entries[$i], $entries[$i-1]];
        }
        if ($_POST['move'] === 'down' && $i < count($entries)-1) {
            [$entries[$i], $entries[$i+1]] = [$entries[$i+1], $entries[$i]];
        }
        $_SESSION['totp_entries'] = $entries;
        header('Location: ' . strtok($_SERVER['REQUEST_URI'], '?'));
        exit;
    }
}

$now = time();
$secondsLeft = $timeStep - ($now % $timeStep);
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<link rel="icon" type="image/x-icon" href="faviconkey.ico">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Simple TOTP Manager (Per-User Session)</title>
<style>
body { font-family: system-ui, -apple-system, 'Segoe UI', Roboto, Arial; max-width:800px; margin:30px auto; padding:0 20px; }
.card { border:1px solid #ddd; padding:12px; border-radius:8px; margin-bottom:10px; }
.row { display:flex; gap:8px; align-items:center; }
.name { flex:1; font-weight:600; }
.code { font-family: monospace; font-size:1.6rem; min-width:120px; text-align:center; }
.muted { color:#666; font-size:0.9rem; }
form.inline { display:inline-block; }
input[type=text] { padding:8px; border-radius:6px; border:1px solid #ccc; }
button { padding:8px 12px; border-radius:6px; border:0; background:#007bff; color:white; cursor:pointer; }
button.warn { background:#dc3545; }
.small { font-size:0.9rem; }
</style>
</head>
<body>
<h1>Simple TOTP Manager (Per-User Session)</h1>
<p class="muted">Each user sees only their own TOTP entries. Use export/import JSON files to move between sessions or machines.</p>

<div class="card">
  <form method="post" enctype="multipart/form-data">
    <div style="display:flex;gap:8px;flex-wrap:wrap;align-items:center;">
      <input name="add_name" placeholder="Name (e.g. GitHub)" required>
      <input name="add_secret" placeholder="Secret (Base32)" required>
      <button type="submit">Add</button>
      <button type="submit" name="clear_all" value="1" class="warn" onclick="return confirm('Clear all entries?');">Clear All</button>
    </div>
  </form>
</div>

<div class="card">
  <h3>Export / Import</h3>
  <form method="post" enctype="multipart/form-data">
    <button type="submit" formaction="?export=1">Export JSON</button>
    <input type="file" name="import_file" accept="application/json">
    <button type="submit">Import JSON File</button>
  </form>
</div>

<div class="card">
  <form method="post">
    <button type="submit" name="sort" value="name">Sort by Name (A→Z)</button>
  </form>
</div>

<?php
if (isset($_GET['export']) && $_GET['export'] == 1) {
    header('Content-Type: application/json');
    header('Content-Disposition: attachment; filename="totp_entries.json"');
    echo json_encode($entries, JSON_PRETTY_PRINT);
    exit;
}
?>

<div class="card">
  <div class="row" style="margin-bottom:8px;">
    <div class="name">Account</div>
    <div class="code">Code</div>
    <div class="small muted">Time left</div>
    <div></div>
  </div>
  <?php if (count($entries) === 0): ?>
    <div class="muted">No entries yet.</div>
  <?php else: ?>
    <?php foreach ($entries as $i => $e):
        $code = totp($e['secret'], $timeStep, $digits, $now);
    ?>
      <div class="row" style="padding:8px 0;border-top:1px solid #f0f0f0;">
        <div class="name"><?=htmlspecialchars($e['name'])?></div>
        <div class="code" id="code-<?=$i?>"><?=htmlspecialchars($code)?></div>
        <div class="small muted" id="left-<?=$i?>"><?=$secondsLeft?>s</div>
        <div style="display:flex; gap:4px;">
          <form method="post" class="inline" onsubmit="return confirm('Delete <?=htmlspecialchars($e['name'])?> ?');">
            <input type="hidden" name="delete_index" value="<?=$i?>">
            <button type="submit" class="warn small">Delete</button>
          </form>
          <form method="post" class="inline">
            <input type="hidden" name="move_index" value="<?=$i?>">
            <button type="submit" name="move" value="up" <?= $i===0 ? 'disabled' : '' ?>>↑</button>
            <button type="submit" name="move" value="down" <?= $i===count($entries)-1 ? 'disabled' : '' ?>>↓</button>
          </form>
        </div>
      </div>
    <?php endforeach; ?>
  <?php endif; ?>
</div>

<script>
let secondsLeft = <?=json_encode($secondsLeft)?>;
function startTimer() {
  const update = () => {
    secondsLeft -= 1;
    if (secondsLeft <= 0) {
      location.reload();
    } else {
      document.querySelectorAll('[id^="left-"]').forEach(el => el.textContent = secondsLeft + 's');
    }
  };
  document.querySelectorAll('[id^="left-"]').forEach(el => el.textContent = secondsLeft + 's');
  setInterval(update, 1000);
}
startTimer();
</script>

</body>
</html>

