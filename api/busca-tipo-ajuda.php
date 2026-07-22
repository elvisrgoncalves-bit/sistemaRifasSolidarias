<?php
require_once 'config.php';

$pdo = db();

$stmt = $pdo->query("
    SELECT id, descricao
    FROM tipo_ajuda
    ORDER BY descricao");

$tiposAjuda = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
