<?php
declare(strict_types=1);

require_once __DIR__ . '/config.php';

header('Content-Type: application/json; charset=utf-8');

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

function json_response(mixed $data, int $status = 200): void
{
    http_response_code($status);
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

function json_error(string $message, int $status = 400): void
{
    json_response(['mensagem' => $message], $status);
}

function input_json(): array
{
    return json_decode(file_get_contents('php://input'), true) ?? [];
}

function require_method(string $method): void
{
    if ($_SERVER['REQUEST_METHOD'] !== $method) {
        json_error('Metodo nao permitido.', 405);
    }
}

function current_user_id(): ?int
{
    return isset($_SESSION['usuario_id']) ? (int) $_SESSION['usuario_id'] : null;
}

function require_login(): int
{
    $id = current_user_id();
    if (!$id) {
        json_error('Usuario nao autenticado.', 401);
    }
    return $id;
}

function fetch_user(int $id): ?array
{
    $stmt = db()->prepare(
        'SELECT u.id, u.nome, u.email, u.telefone, p.descricao AS perfil
         FROM usuario u
         LEFT JOIN perfil p ON p.id = u.id_perfil
         WHERE u.id = ?'
    );
    $stmt->execute([$id]);
    return $stmt->fetch() ?: null;
}

function format_rifa(array $rifa, array $numeros = []): array
{
    $reservados = 0;
    foreach ($numeros as $numero) {
        if (($numero['status'] ?? '') === 'reservado') {
            $reservados++;
        }
    }

    return [
        'id' => (int) $rifa['id'],
        'titulo' => $rifa['descricao_rifa'],
        'descricao' => $rifa['descricao_rifa'],
        'valorNumero' => (float) $rifa['valor_numero'],
        'quantidadeNumero' => (int) $rifa['quantidade_numero'],
        'reservados' => $reservados,
        'dataSorteio' => 'A definir',
        'linkPublico' => 'localhost/sistemaRifasSolidarias/dashboard.html?rifa=' . (int) $rifa['id'],
        'numeros' => array_map(static fn (array $item): array => [
            'numero' => (int) $item['numero'],
            'status' => $item['status'],
        ], $numeros),
    ];
}

function fetch_numeros(int $rifaId): array
{
    $stmt = db()->prepare('SELECT numero, status FROM numero_rifa WHERE rifa_id = ? ORDER BY numero');
    $stmt->execute([$rifaId]);
    return $stmt->fetchAll();
}
