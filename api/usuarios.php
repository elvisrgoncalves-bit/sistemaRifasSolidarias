<?php
declare(strict_types=1);

require_once __DIR__ . '/helpers.php';

require_method('POST');

$input = input_json();
$nome = trim((string) ($input['nome'] ?? ''));
$email = trim((string) ($input['email'] ?? ''));
$telefone = trim((string) ($input['telefone'] ?? ''));
$senha = (string) ($input['senha'] ?? '');

if ($nome === '' || $email === '' || $senha === '') {
    json_error('Preencha nome, e-mail e senha.');
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    json_error('Informe um e-mail valido.');
}

if (strlen($senha) < 6) {
    json_error('A senha deve ter pelo menos 6 caracteres.');
}

try {
    $stmt = db()->prepare(
        'INSERT INTO usuario (nome, endereco, telefone, id_perfil, email, senha)
         VALUES (?, NULL, ?, 3, ?, ?)'
    );
    $stmt->execute([$nome, $telefone ?: null, $email, password_hash($senha, PASSWORD_DEFAULT)]);
} catch (PDOException $exception) {
    if ($exception->getCode() === '23000') {
        json_error('Este e-mail ja esta cadastrado.', 409);
    }
    throw $exception;
}

json_response([
    'id' => (int) db()->lastInsertId(),
    'mensagem' => 'Usuario cadastrado com sucesso',
], 201);


