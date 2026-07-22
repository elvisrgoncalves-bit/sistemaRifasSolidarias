<?php

declare(strict_types=1);

if (session_status() !== PHP_SESSION_ACTIVE) {
  session_start();
}

require_once __DIR__ . '/api/busca-tipo-ajuda.php';

$pageTitle = 'Ajuda | Rifas Solidárias';
$pageHeading = 'Bem-vindo, <span id="userName">' . htmlspecialchars($_SESSION['nome'] ?? 'Usuário') . '</span>!';
$activeNav = 'Ajuda';
$showTopbarActions = true;

require_once __DIR__ . '/includes/header.php';

$tiposAjuda = $tiposAjuda ?? [];
?>

<body class="form-page">

  <main class="form-shell">
    <section class="section-head" id="ajuda">
      <h2>Ajuda</h2>

      <form id="raffleForm" class="form-grid" action="api/"
        method="POST">
          <select name="tipo_ajuda_id" id="tipo_ajuda_id">
            <option value="">Selecione...</option>
            <?php foreach ($tiposAjuda as $tipo): ?>
              <option value="<?= $tipo['id'] ?>">
                <?= htmlspecialchars($tipo['descricao']) ?>
              </option>
            <?php endforeach; ?>
          </select>
        <label class="field span-2">
          <span>Nome</span>
          <span class="input-icon">
            <i data-lucide="user"></i>
            <input
              type="text"
              name="nome"
              value="<?= htmlspecialchars($usuarioLogado['nome'] ?? '') ?>"
              required>
          </span>
        </label>
        <div class="form-actions span-2">
          <a class="btn btn-plain" href="dashboard.php">Cancelar</a>
          <button class="btn btn-primary" type="submit">Salvar alterações</button>
        </div>
      </form>
    </section>
  </main>
  <?php require_once __DIR__ . '/includes/footer.php'; ?>

</body>

</html>