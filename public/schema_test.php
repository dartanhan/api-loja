<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);
use Illuminate\Support\Facades\DB;
$schemas = [
    'loja_produtos_new' => DB::select('DESCRIBE loja_produtos_new'),
    'loja_produtos_variacao' => DB::select('DESCRIBE loja_produtos_variacao')
];
echo json_encode($schemas, JSON_PRETTY_PRINT);
