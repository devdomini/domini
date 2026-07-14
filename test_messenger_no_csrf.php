<?php
/** Vérifie que messenger-api accepte POST sans token CSRF (exemption). */
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$commercial = App\Models\User::where('role', 'commercial')->first();
$thread = App\Models\SupportThread::query()->first();
if (! $commercial || ! $thread) {
    echo "missing data\n";
    exit(2);
}

auth()->login($commercial);
$request = Illuminate\Http\Request::create(
    "/admin/messenger-api/threads/{$thread->id}/messages",
    'POST',
    [],
    [],
    [],
    [
        'HTTP_ACCEPT' => 'application/json',
        'HTTP_X_REQUESTED_WITH' => 'XMLHttpRequest',
        'CONTENT_TYPE' => 'application/json',
    ],
    json_encode(['body' => '[no-csrf] '.date('H:i:s')])
);
$response = $kernel->handle($request);
$body = $response->getContent();
$data = json_decode($body, true);
echo 'HTTP '.$response->getStatusCode()."\n";
echo substr($body, 0, 200)."\n";
exit($response->getStatusCode() === 200 && ($data['success'] ?? false) ? 0 : 1);
