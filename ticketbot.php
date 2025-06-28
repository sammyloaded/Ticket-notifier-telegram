<?php
// ── ERRORS & PLAIN TEXT OUTPUT ────────────────────────────────────────────────
ini_set('display_errors', 1);
error_reporting(E_ALL);
header('Content-Type: text/plain; charset=utf-8');

echo "=== Pending Tickets Notification ===\n";
echo "Run at: " . date('Y-m-d H:i:s') . "\n\n";

// ── CONFIG ─────────────────────────────────────────────────────────────────────
$dbHost     = 'YOUR_DB_HOST';
$dbName     = 'YOUR_DB_NAME';
$dbUser     = 'YOUR_DB_USER';
$dbPass     = 'YOUR_DB_PASSWORD';
$botToken   = 'YOUR_TELEGRAM_BOT_TOKEN';
$chatId     = 'YOUR_TELEGRAM_CHAT_ID';
// ────────────────────────────────────────────────────────────────────────────────

// 1) CONNECT
$conn = new mysqli($dbHost, $dbUser, $dbPass, $dbName);
if ($conn->connect_error) {
    die("DB Connection failed: " . $conn->connect_error . "\n");
}

// 2) FETCH *ALL* pending tickets
$sql = "SELECT id, subject FROM tickets WHERE status = 'pending'";
$res = $conn->query($sql);
if (!$res) {
    die("Query error: " . $conn->error . "\n");
}

$count = $res->num_rows;
echo "Found {$count} pending ticket(s).\n\n";
if ($count === 0) {
    exit("No pending tickets to notify.\n");
}

// 3) SEND each to Telegram
while ($row = $res->fetch_assoc()) {
    $id    = $row['id'];
    $subj  = $row['subject'];
    $url   = "https://megaboost.com.ng/admin/tickets/view/{$id}";

    echo "→ Ticket #{$id}: {$subj}\n";

    $text = "📨 *You have a Pending Ticket*\n"
          . "*Ticket ID:* {$id}\n"
          . "*Ticket Subject:* {$subj}";

    $payload = [
        'chat_id'      => $chatId,
        'parse_mode'   => 'Markdown',
        'text'         => $text,
        'reply_markup' => json_encode([
            'inline_keyboard'=>[
                [['text'=>'View Ticket','url'=>$url]]
            ]
        ]),
    ];

    $ch   = curl_init("https://api.telegram.org/bot{$botToken}/sendMessage");
    curl_setopt_array($ch, [
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => $payload,
        CURLOPT_RETURNTRANSFER => true,
    ]);
    $resp = curl_exec($ch);

    if ($resp === false) {
        echo "   [ERROR] cURL: " . curl_error($ch) . "\n";
    } else {
        echo "   [OK] Telegram: {$resp}\n";
    }
    curl_close($ch);

    echo "\n";
}

$conn->close();
echo "=== Done ===\n";
