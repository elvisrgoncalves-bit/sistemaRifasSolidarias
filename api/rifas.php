<?php
declare(strict_types=1);

require_once __DIR__ . '/helpers.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $stmt = db()->query('SELECT * FROM rifa ORDER BY id DESC');
    $rifas = [];

    foreach ($stmt->fetchAll() as $rifa) {
        $rifas[] = format_rifa($rifa, fetch_numeros((int) $rifa['id']));
    }

    json_response($rifas);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_login();

    $input = input_json();
    $titulo = trim((string) ($input['titulo'] ?? ''));
    $descricao = trim((string) ($input['descricao'] ?? ''));
    $valor = (float) ($input['valorNumero'] ?? 0);
    $quantidade = (int) ($input['quantidadeNumero'] ?? 0);

    if ($titulo === '' && $descricao === '') {
        json_error('Informe o titulo ou descricao da rifa.');
    }

    if ($valor <= 0 || $quantidade < 1) {
        json_error('Informe valor e quantidade validos.');
    }

    $texto = $titulo !== '' ? $titulo : $descricao;

    $usuarioId = current_user_id();
    if ($usuarioId === null) {
        json_error('Usuario nao autenticado.', 401);
    }

    $pdo = db();
    $pdo->beginTransaction();

    try {
        $colunaExiste = $pdo->query("SHOW COLUMNS FROM rifa LIKE 'usuario_criacao'")->fetch();
        if (!$colunaExiste) {
            $pdo->exec("ALTER TABLE rifa ADD COLUMN usuario_criacao bigint DEFAULT NULL");
        }

        $stmt = $pdo->prepare(
            'INSERT INTO rifa (descricao_rifa, valor_numero, quantidade_numero, usuario_criacao)
             VALUES (?, ?, ?, ?)'
        );
        $stmt->execute([$texto, $valor, $quantidade, $usuarioId]);
        $rifaId = (int) $pdo->lastInsertId();

        $numeroStmt = $pdo->prepare(
            'INSERT INTO numero_rifa (numero, status, rifa_id) VALUES (?, ?, ?)'
        );

        for ($numero = 1; $numero <= $quantidade; $numero++) {
            $numeroStmt->execute([$numero, 'disponivel', $rifaId]);
        }

        $pdo->commit();
    } catch (Throwable $throwable) {
        $pdo->rollBack();
        throw $throwable;
    }

    json_response([
        'id' => $rifaId,
        'mensagem' => 'Rifa criada com sucesso',
    ], 201);
}

json_error('Metodo nao permitido.', 405);
