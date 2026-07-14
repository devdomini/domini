<?php

/**
 * Vérifie les API messagerie support pour tous les rôles (HTTP réel).
 * Usage: php test_support_all_roles.php [base_url]
 */

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\SupportThread;
use App\Models\User;

$base = rtrim($argv[1] ?? 'http://127.0.0.1:8078', '/');

function ok(string $label, bool $pass, string $detail = ''): void
{
    echo sprintf("[%s] %s%s\n", $pass ? 'OK' : 'FAIL', $label, $detail !== '' ? " — $detail" : '');
}

function httpJson(string $method, string $url, ?string $token = null, ?array $body = null): array
{
    $ch = curl_init($url);
    $headers = ['Accept: application/json', 'Content-Type: application/json'];
    if ($token) {
        $headers[] = 'Authorization: Bearer '.$token;
    }
    curl_setopt_array($ch, [
        CURLOPT_CUSTOMREQUEST => $method,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => $headers,
        CURLOPT_TIMEOUT => 15,
    ]);
    if ($body !== null) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));
    }
    $raw = curl_exec($ch);
    $code = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $err = curl_error($ch);
    curl_close($ch);

    if ($raw === false) {
        return ['code' => 0, 'json' => false, 'raw' => $err, 'data' => null];
    }

    $data = json_decode($raw, true);

    return [
        'code' => $code,
        'json' => is_array($data),
        'raw' => substr(preg_replace('/\s+/', ' ', $raw), 0, 100),
        'data' => $data,
    ];
}

function tokenFor(User $user): string
{
    $user->tokens()->where('name', 'test-support-api')->delete();

    return $user->createToken('test-support-api')->plainTextToken;
}

echo "=== Test API messagerie — $base ===\n\n";

$employe = User::where('role', 'employe')->where('is_active', true)->first();
$livreur = User::where('role', 'livreur')->where('is_active', true)->first();
$commercial = User::where('role', 'commercial')->where('is_active', true)->first();
$admin = User::where('role', 'admin')->where('is_active', true)->first();
$thread = SupportThread::with('user')->first();

foreach (['employe' => $employe, 'livreur' => $livreur, 'commercial' => $commercial, 'admin' => $admin] as $role => $u) {
    echo $role.': '.($u ? "#{$u->id} {$u->name}" : 'ABSENT')."\n";
}
echo 'thread: '.($thread ? "#{$thread->id} user=#{$thread->user_id}" : 'ABSENT')."\n\n";

if (! $employe || ! $livreur || ! $commercial || ! $admin || ! $thread) {
    echo "Données de test insuffisantes.\n";
    exit(1);
}

$threadId = $thread->id;
$stamp = date('H:i:s');
$failures = 0;

$check = function (string $label, bool $pass, string $detail = '') use (&$failures) {
    ok($label, $pass, $detail);
    if (! $pass) {
        $failures++;
    }
};

echo "--- API /api/support (employé & livreur) ---\n";
foreach (['employe' => $employe, 'livreur' => $livreur] as $role => $user) {
    $token = tokenFor($user);
    $r = httpJson('GET', "$base/api/support/thread", $token);
    $check("$role GET /api/support/thread", $r['code'] === 200 && ($r['data']['success'] ?? false), "code={$r['code']} json=".($r['json'] ? 'yes' : 'no'));

    $r = httpJson('POST', "$base/api/support/messages", $token, ['body' => "[$role] test $stamp"]);
    $check("$role POST /api/support/messages", in_array($r['code'], [200, 201], true) && ($r['data']['success'] ?? false), "code={$r['code']}");
}

foreach (['commercial' => $commercial, 'admin' => $admin] as $role => $user) {
    $token = tokenFor($user);
    $r = httpJson('POST', "$base/api/support/messages", $token, ['body' => 'interdit']);
    $check("$role POST /api/support/messages → 403", $r['code'] === 403 && $r['json'], "code={$r['code']}");
}

echo "\n--- API /api/commercial/support (commercial) ---\n";
$commercialToken = tokenFor($commercial);

$r = httpJson('GET', "$base/api/commercial/support/threads", $commercialToken);
$check('commercial GET /threads', $r['code'] === 200 && ($r['data']['success'] ?? false), "code={$r['code']}");

$r = httpJson('GET', "$base/api/commercial/support/threads/$threadId", $commercialToken);
$check('commercial GET /threads/{id}', $r['code'] === 200 && ($r['data']['success'] ?? false), "code={$r['code']}");

$r = httpJson('POST', "$base/api/commercial/support/threads/$threadId/messages", $commercialToken, ['body' => "[commercial] test $stamp"]);
$check('commercial POST /threads/{id}/messages', $r['code'] === 200 && ($r['data']['success'] ?? false), $r['json'] ? "code={$r['code']}" : "HTML? {$r['raw']}");

$r = httpJson('POST', "$base/api/commercial/support/threads/$threadId/close", $commercialToken);
$check('commercial POST /threads/{id}/close', $r['code'] === 200 && ($r['data']['success'] ?? false), "code={$r['code']}");

$r = httpJson('POST', "$base/api/commercial/support/threads/$threadId/reopen", $commercialToken);
$check('commercial POST /threads/{id}/reopen', $r['code'] === 200 && ($r['data']['success'] ?? false), "code={$r['code']}");

$employeToken = tokenFor($employe);
$r = httpJson('GET', "$base/api/commercial/support/threads", $employeToken);
$check('employe GET /api/commercial/support → 403', $r['code'] === 403, "code={$r['code']}");

echo "\n--- Web messenger-api (session admin & commercial) ---\n";
foreach (['admin' => $admin, 'commercial' => $commercial] as $role => $user) {
    $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
    auth()->login($user);
    $csrf = csrf_token();
    $request = Illuminate\Http\Request::create(
        "/admin/messenger-api/threads/$threadId/messages",
        'POST',
        [],
        [],
        [],
        [
            'HTTP_ACCEPT' => 'application/json',
            'HTTP_X_REQUESTED_WITH' => 'XMLHttpRequest',
            'HTTP_X_CSRF_TOKEN' => $csrf,
            'CONTENT_TYPE' => 'application/json',
        ],
        json_encode(['body' => "[$role web] test $stamp"])
    );
    $request->headers->set('X-CSRF-TOKEN', $csrf);
    $response = $kernel->handle($request);
    $content = $response->getContent();
    $data = json_decode($content, true);
    $isJson = is_array($data);
    $pass = $response->getStatusCode() === 200 && ($data['success'] ?? false) && $isJson;
    $detail = $isJson ? "code={$response->getStatusCode()}" : 'HTML? '.substr($content, 0, 80);
    $check("$role POST /admin/messenger-api/threads/{id}/messages", $pass, $detail);
    auth()->logout();
}

echo "\nRésumé: ".($failures === 0 ? 'TOUS OK' : "$failures échec(s)")."\n";
exit($failures === 0 ? 0 : 2);
