<?php
declare(strict_types=1);

require_once __DIR__ . '/helpers.php';

require_method('GET');

$usuario = fetch_user(require_login());

if (!$usuario) {
    json_error('Usuario nao encontrado.', 404);
}

json_response([
    'id' => (int) $usuario['id'],
    'nome' => $usuario['nome'],
    'email' => $usuario['email'],
    'perfil' => $usuario['perfil'],
]);
