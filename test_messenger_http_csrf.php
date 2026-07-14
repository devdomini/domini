<?php
/** Vérifie si la route messenger-api est bien exemptée CSRF via HTTP réel. */
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$base = rtrim($argv[1] ?? 'http://127.0.0.1:8078', '/');
$cookieFile = sys_get_temp_dir().'/domini_csrf_test_cookies.txt';
@unlink($cookieFile);

function req(string $url, string $cookieFile, array $opts = []): array {
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_COOKIEJAR => $cookieFile,
        CURLOPT_COOKIEFILE => $cookieFile,
        CURLOPT_FOLLOWLOCATION => false,
        CURLOPT_HEADER => true,
        CURLOPT_HTTPHEADER => $opts['headers'] ?? [],
        CURLOPT_POST => ($opts['method'] ?? 'GET') === 'POST',
        CURLOPT_POSTFIELDS => $opts['body'] ?? null,
    ]);
    $raw = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    $pos = strpos($raw, "\r\n\r\n");
    return ['code' => $code, 'body' => $pos !== false ? substr($raw, $pos + 4) : $raw];
}

// Login admin
$r = req("$base/admin/login", $cookieFile);
preg_match('/name="_token" value="([^"]+)"/', $r['body'], $m);
$csrf = $m[1] ?? '';
if (! $csrf) {
    echo "FAIL: impossible de lire _token login (HTTP {$r['code']})\n";
    echo substr($r['body'], 0, 200)."\n";
    exit(2);
}

$r = req("$base/admin/login", $cookieFile, [
    'method' => 'POST',
    'headers' => ['Content-Type: application/x-www-form-urlencoded'],
    'body' => http_build_query(['_token' => $csrf, 'email' => 'admin@domini.com', 'password' => 'password123']),
]);
echo "Login: HTTP {$r['code']}\n";

// POST messenger SANS aucun header CSRF
$r = req("$base/admin/messenger-api/threads/1/messages", $cookieFile, [
    'method' => 'POST',
    'headers' => ['Accept: application/json', 'Content-Type: application/json'],
    'body' => json_encode(['body' => '[http-no-csrf] test']),
]);
$data = json_decode($r['body'], true);
echo "POST sans CSRF: HTTP {$r['code']}\n";
echo substr($r['body'], 0, 180)."\n";
$ok = $r['code'] === 200 && ($data['success'] ?? false);
echo $ok ? "OK exemption CSRF active\n" : "FAIL exemption CSRF inactive (419 = CSRF encore vérifié)\n";
exit($ok ? 0 : 1);
