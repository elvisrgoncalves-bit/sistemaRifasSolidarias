<?php
declare(strict_types=1);

require_once __DIR__ . '/helpers.php';

require_method('POST');

$input = input_json();
$rifaId = (int) ($input['rifaId'] ?? 0);
$numeros = $input['numeros'] ?? [];
$participante = is_array($input['participante'] ?? null) ? $input['participante'] : [];

$nome = trim((string) ($participante['nome'] ?? ''));
$telefone = trim((string) ($participante['telefone'] ?? ''));
$email = trim((string) ($participante['email'] ?? ''));

if ($rifaId <= 0 || !is_array($numeros) || count($numeros) === 0) {
    json_error('Informe a rifa e pelo menos um numero.');
}

if ($nome === '' || $telefone === '') {
    json_error('Informe nome e telefone do participante.');
}

if ($email === '') {
    $email = preg_replace('/\D+/', '', $telefone) . '@sem-email.local';
}

$pdo = db();
$pdo->beginTransaction();

try {
    $stmt = $pdo->prepare('SELECT id FROM usuario WHERE email = ?');
    $stmt->execute([$email]);
    $usuarioId = (int) ($stmt->fetchColumn() ?: 0);

    if (!$usuarioId) {
        $stmt = $pdo->prepare(
            'INSERT INTO usuario (nome, endereco, telefone, id_perfil, email, senha)
             VALUES (?, NULL, ?, 3, ?, ?)'
        );
        $stmt->execute([$nome, $telefone, $email, password_hash(bin2hex(random_bytes(8)), PASSWORD_DEFAULT)]);
        $usuarioId = (int) $pdo->lastInsertId();
    }

    $selectNumero = $pdo->prepare(
        'SELECT id, status FROM numero_rifa WHERE rifa_id = ? AND numero = ?'
    );
    $updateNumero = $pdo->prepare(
        'UPDATE numero_rifa SET status = ? WHERE id = ?'
    );
    $insertReserva = $pdo->prepare(
        'INSERT INTO reserva (numero_rifa_id, usuario_id, data_reserva)
         VALUES (?, ?, NOW())'
    );

    $reservaId = 0;
    foreach (array_unique(array_map('intval', $numeros)) as $numero) {
        if ($numero <= 0) {
            json_error('Numero invalido.');
        }

        $selectNumero->execute([$rifaId, $numero]);
        $numeroRifa = $selectNumero->fetch();

        if (!$numeroRifa) {
            json_error("Numero {$numero} nao existe nesta rifa.", 404);
        }

        if ($numeroRifa['status'] !== 'disponivel') {
            json_error("Numero {$numero} nao esta disponivel.", 409);
        }

        $updateNumero->execute(['reservado', (int) $numeroRifa['id']]);
        $insertReserva->execute([(int) $numeroRifa['id'], $usuarioId]);
        $reservaId = (int) $pdo->lastInsertId();
    }

    $pdo->commit();
} catch (Throwable $throwable) {
    $pdo->rollBack();
    throw $throwable;
}

json_response([
    'id' => $reservaId,
    'mensagem' => 'Numeros reservados com sucesso',
], 201);
