<?php
declare(strict_types=1);

require_once __DIR__ . '/helpers.php';

require_method('GET');

$id = (int) ($_GET['id'] ?? 0);
if ($id <= 0) {
    json_error('Informe o ID da rifa.');
}

$stmt = db()->prepare('SELECT * FROM rifa WHERE id = ?');
$stmt->execute([$id]);
$rifa = $stmt->fetch();

if (!$rifa) {
    json_error('Rifa nao encontrada.', 404);
}

json_response(format_rifa($rifa, fetch_numeros($id)));
