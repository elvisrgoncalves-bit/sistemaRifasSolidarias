<?php
declare(strict_types=1);

require_once __DIR__ . '/config.php';

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

function carregar_rifas_do_banco(?int $usuarioCriacao = null, bool $apenasDoUsuario = false): array
{
    $pdo = db();
    $stmt = $pdo->query('SELECT * FROM rifa ORDER BY id DESC');

    $rifas = [];

    foreach ($stmt->fetchAll() as $rifa) {
        $rifaUsuarioId = null;
        $temColunaUsuarioCriacao = array_key_exists('usuario_criacao', $rifa);
        if ($temColunaUsuarioCriacao) {
            $rifaUsuarioId = (int) $rifa['usuario_criacao'];
        }

        if ($apenasDoUsuario && $usuarioCriacao !== null && $temColunaUsuarioCriacao && $rifaUsuarioId !== $usuarioCriacao) {
            continue;
        }

        $rifaId = (int) $rifa['id'];

        $numeroStmt = $pdo->prepare(
            'SELECT numero, status FROM numero_rifa WHERE rifa_id = ? ORDER BY numero'
        );
        $numeroStmt->execute([$rifaId]);
        $numeros = $numeroStmt->fetchAll();

        $criadorStmt = $pdo->prepare(
            'SELECT nome, telefone FROM usuario WHERE id = ?'
        );
        $criadorStmt->execute([$rifaUsuarioId]);
        $criador = $criadorStmt->fetch() ?: [];

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
            'usuarioCriacao' => (string) ($criador['nome'] ?? ''),
            'usuarioCriacaoTelefone' => (string) ($criador['telefone'] ?? ''),
            'reservados' => $reservados,
            'dataSorteio' => 'A definir',

            'numeros' => array_map(static fn (array $item): array => [
                'numero' => (int) $item['numero'],
                'status' => (string) $item['status'],
            ], $numeros),
        ];
    }

    return $rifas;
}

$usuarioLogadoId = isset($_SESSION['usuario_id']) ? (int) $_SESSION['usuario_id'] : null;
$rifasDoBanco = carregar_rifas_do_banco($usuarioLogadoId, true);
$rifasAtivasDoBanco = carregar_rifas_do_banco();
$rifaAtual = $rifasDoBanco[0] ?? null;
