<?php
declare(strict_types=1);

require_once __DIR__ . '/helpers.php';

require_method('POST');

$input = input_json();

if (!$input) {
    $input = $_POST;
}

$email = trim((string) ($input['email'] ?? ''));
$senha = (string) ($input['senha'] ?? '');

if ($email === '' || $senha === '') {
    json_error('Informe e-mail e senha.');
}

$stmt = db()->prepare(
    'SELECT u.id, u.nome, u.email, u.senha, p.descricao AS perfil
     FROM usuario u
     LEFT JOIN perfil p ON p.id = u.id_perfil
     WHERE u.email = ?'
);
$stmt->execute([$email]);
$usuario = $stmt->fetch();

$senhaSalva = (string) ($usuario['senha'] ?? '');
$senhaValida = $usuario && (
    password_verify($senha, $senhaSalva) || hash_equals($senhaSalva, $senha)
);

if (!$senhaValida) {
    json_error('E-mail ou senha invalidos.', 401);
}

$_SESSION['usuario_id'] = (int) $usuario['id'];
$_SESSION['nome'] = (string) $usuario['nome'];
$_SESSION['perfil'] = (string) ($usuario['perfil'] ?? '');
$_SESSION['perfil_id'] = (string) ($usuario['id_perfil'] ?? '');

json_response([
    'id' => (int) $usuario['id'],
    'nome' => $usuario['nome'],
    'email' => $usuario['email'],
    'perfil' => $usuario['perfil'],
]);
