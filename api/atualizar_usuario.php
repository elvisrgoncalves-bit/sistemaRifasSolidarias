<?php
declare(strict_types=1);

require_once __DIR__ . '/helpers.php';

if($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../usuario.php');
    exit;
}
$id = $_SESSION['usuario_id'] ?? null;

$NOME = trim((string) ($_POST['nome'] ?? ''));
$EMAIL = trim((string) ($_POST['email'] ?? ''));
$TELEFONE = trim((string) ($_POST['telefone'] ?? ''));
$ENDERECO = trim((string) ($_POST['endereco'] ?? ''));
$SENHA = trim((string) ($_POST['senha'] ?? ''));

$pdo = db();
if ($id === null) {
    json_error('Usuário nao encontrado.', 404);
}

if ($NOME === '' || $EMAIL === '' || $TELEFONE === '') {
    json_error('Preencha todos os campos.');
}

if (!filter_var($EMAIL, FILTER_VALIDATE_EMAIL)) {
    json_error('Informe um e-mail valido.');
}
if ($SENHA !== '') {

    $senhaHash = password_hash($SENHA, PASSWORD_DEFAULT);

    $sql = "
        UPDATE usuario
        SET
            nome = ?,
            telefone = ?,
            endereco = ?,
            email = ?,
            senha = ?
        WHERE id = ?
    ";

    $stmt = $pdo->prepare($sql);

    $stmt->execute([
        $NOME,
        $TELEFONE,
        $ENDERECO,
        $EMAIL,
        $senhaHash,
        $id
    ]);

} else {

    $sql = "
        UPDATE usuario
        SET
            nome = ?,
            telefone = ?,
            endereco = ?,
            email = ?
        WHERE id = ?
    ";

    $stmt = $pdo->prepare($sql);

    $stmt->execute([
        $NOME,
        $TELEFONE,
        $ENDERECO,
        $EMAIL,
        $id
    ]);
}

$_SESSION['nome'] = $NOME;

 $_SESSION['nome'] = $NOME;

    json_response([
        'sucesso' => true,
        'mensagem' => 'Usuário atualizado com sucesso.'
    ]);

exit;

try {
    $stmt = db()->prepare(
        'UPDATE usuario SET nome = ?, email = ?, telefone = ? WHERE id = ?'
    );
    $stmt->execute([$NOME, $EMAIL, $TELEFONE, $id]);
} catch (PDOException $exception) {
    json_error('Erro ao atualizar usuario.');
}

json_response([
    'mensagem' => 'Usuario atualizado com sucesso'
]);