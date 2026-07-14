<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Http\Request;

$paths = [
    'admin/messenger-api/threads/1/messages',
    'admin/messenger-api/threads/1',
    'admin/login',
];

foreach ($paths as $path) {
    $req = Request::create($path, 'POST');
    $req->headers->set('HOST', '127.0.0.1');
    echo "$path => is(admin/messenger-api/*): ".($req->is('admin/messenger-api/*') ? 'YES' : 'NO')."\n";
}

$m = app(ValidateCsrfToken::class);
$req = Request::create('/admin/messenger-api/threads/1/messages', 'POST');
$ref = new ReflectionClass($m);
$method = $ref->getMethod('inExceptArray');
$method->setAccessible(true);
echo "inExceptArray messenger POST: ".($method->invoke($m, $req) ? 'YES' : 'NO')."\n";

$ref2 = new ReflectionClass(ValidateCsrfToken::class);
$prop = $ref2->getProperty('neverVerify');
$prop->setAccessible(true);
echo "neverVerify: ".json_encode($prop->getValue())."\n";
