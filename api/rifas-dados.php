<?php
declare(strict_types=1);

require_once __DIR__ . '/config.php';

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

function carregar_rifas_do_banco(?int $usuarioCriacao = null): array
{
    $pdo = db();

    if ($usuarioCriacao) {
        $stmt = $pdo->prepare('SELECT * FROM rifa WHERE usuario_criacao = ? ORDER BY id DESC');
        $stmt->execute([$usuarioCriacao]);
    } else {
        $stmt = $pdo->query('SELECT * FROM rifa ORDER BY id DESC');
    }

    $rifas = [];

    foreach ($stmt->fetchAll() as $rifa) {
        $rifaId = (int) $rifa['id'];

        $numeroStmt = $pdo->prepare(
            'SELECT numero, status FROM numero_rifa WHERE rifa_id = ? ORDER BY numero'
        );
        $numeroStmt->execute([$rifaId]);
        $numeros = $numeroStmt->fetchAll();

        $reservados = 0;
        foreach ($numeros as $numero) {
            if (($numero['status'] ?? '') === 'reservado') {
                $reservados++;
            }
        }

        $rifas[] = [
            'id' => $rifaId,
            'titulo' => (string) $rifa['descricao_rifa'],
            'descricao' => (string) $rifa['descricao_rifa'],
            'valorNumero' => (float) $rifa['valor_numero'],
            'quantidadeNumero' => (int) $rifa['quantidade_numero'],
            'reservados' => $reservados,
            'dataSorteio' => 'A definir',
            'linkPublico' => 'localhost/sistemaRifasSolidarias/dashboard.html?rifa=' . $rifaId,
            'numeros' => array_map(static fn (array $item): array => [
                'numero' => (int) $item['numero'],
                'status' => (string) $item['status'],
            ], $numeros),
        ];
    }

    return $rifas;
}

$usuarioLogadoId = isset($_SESSION['usuario_id']) ? (int) $_SESSION['usuario_id'] : null;
$rifasDoBanco = carregar_rifas_do_banco($usuarioLogadoId);
$rifaAtual = $rifasDoBanco[0] ?? null;
