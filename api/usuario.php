<?php
declare(strict_types=1);

require_once __DIR__ . '/config.php';

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

function carregarUsuarioLogado(): ?array
{
    if (!isset($_SESSION['usuario_id'])) {
        return null;
    }

    $pdo = db();

    $stmt = $pdo->prepare("
        SELECT
            u.nome,
            u.endereco,
            u.telefone,
            u.email,
            u.senha,
            u.id_perfil,
            p.descricao AS perfil
        FROM usuario u
        INNER JOIN perfil p
            ON p.id = u.id_perfil
        WHERE u.id = :id
    ");

    $stmt->execute([
        ':id' => $_SESSION['usuario_id']
    ]);

    return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
}
$usuarioLogado = carregarUsuarioLogado();
