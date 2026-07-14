<?php
$base = 'http://127.0.0.1:8078';
$cookie = tempnam(sys_get_temp_dir(), 'domini_ck_');

function http(string $url, string $cookie, string $method = 'GET', ?string $body = null, array $headers = []): array {
    $ch = curl_init($url);
    $h = array_merge(['Accept: text/html,application/json'], $headers);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_COOKIEJAR => $cookie,
        CURLOPT_COOKIEFILE => $cookie,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_CUSTOMREQUEST => $method,
        CURLOPT_HTTPHEADER => $h,
        CURLOPT_POSTFIELDS => $body,
    ]);
    $resp = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    return ['code' => $code, 'body' => (string) $resp];
}

// 1) Login page
$r = http("$base/admin/login", $cookie);
preg_match('/name="_token" value="([^"]+)"/', $r['body'], $m);
$token = $m[1] ?? '';
echo "Login page: {$r['code']} token=".($token ? 'ok' : 'missing')."\n";
if (! $token) exit(2);

// 2) Login POST
$r = http("$base/admin/login", $cookie, 'POST', http_build_query([
    '_token' => $token,
    'email' => 'admin@domini.com',
    'password' => 'password123',
]), ['Content-Type: application/x-www-form-urlencoded']);
echo "Login POST: {$r['code']}\n";

// 3) Messenger POST sans CSRF
$r = http("$base/admin/messenger-api/threads/1/messages", $cookie, 'POST', json_encode(['body' => '[http] '.date('H:i:s')]), [
    'Content-Type: application/json',
    'Accept: application/json',
    'X-Requested-With: XMLHttpRequest',
]);
$data = json_decode($r['body'], true);
echo "Messenger POST: {$r['code']}\n";
echo substr($r['body'], 0, 160)."\n";
echo ($r['code'] === 200 && ($data['success'] ?? false)) ? "OK\n" : "FAIL\n";
@unlink($cookie);
