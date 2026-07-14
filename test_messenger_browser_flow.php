<?php
/**
 * Simule le flux navigateur : login web + POST messenger-api avec cookies/session.
 * Usage: php test_messenger_browser_flow.php [base_url] [email] [password]
 */
$base = rtrim($argv[1] ?? 'http://127.0.0.1:8078', '/');
$email = $argv[2] ?? 'idrissa@domini.test';
$password = $argv[3] ?? 'password';

$cookieFile = sys_get_temp_dir() . '/domini_messenger_test_cookies.txt';
@unlink($cookieFile);

function browserRequest(string $url, string $cookieFile, array $opts = []): array
{
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_COOKIEJAR => $cookieFile,
        CURLOPT_COOKIEFILE => $cookieFile,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HEADER => true,
        CURLOPT_HTTPHEADER => $opts['headers'] ?? [],
        CURLOPT_POST => ($opts['method'] ?? 'GET') === 'POST',
        CURLOPT_POSTFIELDS => $opts['body'] ?? null,
    ]);
    $raw = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    $headerSize = strpos($raw, "\r\n\r\n");
    $headers = $headerSize !== false ? substr($raw, 0, $headerSize) : '';
    $body = $headerSize !== false ? substr($raw, $headerSize + 4) : $raw;

    return ['code' => $code, 'headers' => $headers, 'body' => $body];
}

echo "=== Test flux navigateur — $base ===\n";
echo "Compte: $email\n\n";

// 1. Page login
$r = browserRequest("$base/admin/login", $cookieFile);
preg_match('/name="_token" value="([^"]+)"/', $r['body'], $m);
if (empty($m[1])) {
    preg_match('/<meta name="csrf-token" content="([^"]+)"/', $r['body'], $m);
}
$csrf = $m[1] ?? null;
echo $csrf ? "[OK] CSRF login: ".substr($csrf, 0, 12)."…\n" : "[FAIL] Pas de CSRF sur login\n";

// 2. Login POST
$r = browserRequest("$base/admin/login", $cookieFile, [
    'method' => 'POST',
    'headers' => [
        'Content-Type: application/x-www-form-urlencoded',
        'X-CSRF-TOKEN: '.$csrf,
        'X-Requested-With: XMLHttpRequest',
    ],
    'body' => http_build_query([
        '_token' => $csrf,
        'email' => $email,
        'password' => $password,
    ]),
]);
echo "[INFO] Login HTTP {$r['code']}\n";

// 3. Page messagerie commercial
$r = browserRequest("$base/admin/commercial/support", $cookieFile);
preg_match('/<meta name="csrf-token" content="([^"]+)"/', $r['body'], $m2);
$csrfPage = $m2[1] ?? null;
preg_match('/activeThreadId:\s*(\d+)/', $r['body'], $m3);
$threadId = $m3[1] ?? '1';
echo $csrfPage ? "[OK] CSRF messagerie: ".substr($csrfPage, 0, 12)."…\n" : "[FAIL] Pas de CSRF messagerie\n";
echo "[INFO] Thread actif détecté: #$threadId\n";

if (! $csrfPage) {
    echo "\nCorps (début):\n".substr($r['body'], 0, 300)."\n";
    exit(2);
}

// 4. POST message via messenger-api
$payload = json_encode(['body' => '[browser-test] '.date('H:i:s')]);
$r = browserRequest("$base/admin/messenger-api/threads/$threadId/messages", $cookieFile, [
    'method' => 'POST',
    'headers' => [
        'Accept: application/json',
        'Content-Type: application/json',
        'X-Requested-With: XMLHttpRequest',
        'X-CSRF-TOKEN: '.$csrfPage,
    ],
    'body' => $payload,
]);

$json = json_decode($r['body'], true);
$isJson = is_array($json);
$ok = $r['code'] === 200 && ($json['success'] ?? false);

echo "\n--- POST messenger-api ---\n";
echo "HTTP: {$r['code']}\n";
echo "JSON: ".($isJson ? 'oui' : 'non')."\n";
echo "Body: ".substr($r['body'], 0, 200)."\n";
echo $ok ? "\n[OK] Envoi message réussi\n" : "\n[FAIL] Envoi message échoué\n";
exit($ok ? 0 : 1);
