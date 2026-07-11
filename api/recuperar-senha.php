<?php
declare(strict_types=1);

require_once __DIR__ . '/helpers.php';

require_method('POST');

$input = input_json();
$email = trim((string) ($input['email'] ?? ''));

if ($email === '') {
    json_error('Informe seu e-mail.');
}

json_response(['mensagem' => 'Instrucoes enviadas']);
