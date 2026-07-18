<?php

declare(strict_types=1);

if (session_status() !== PHP_SESSION_ACTIVE) {
  session_start();
}

require_once __DIR__ . '/api/perfil.php';

$pageTitle = 'Perfil | Rifas Solidarias';
$pageHeading = 'Bem-vindo, <span id="userName">' . htmlspecialchars($_SESSION['nome'] ?? 'Usuário') . '</span>!';
$pageSubtitle = 'Vizualize o seu perfil, para se tornar um organizador entre em contato com o administrador.';
$activeNav = 'rifas';
$showTopbarActions = true;

$rifaAtual = $rifaAtual ?? null;
$rifaAtual = is_array($rifaAtual) ? $rifaAtual : [];

$dadosRifaJson = json_encode(
  $rifasAtivasDoBanco ?: [
    [
      'id' => 0,
      'nome ' => '',
      'endereco' => '',
      'telefone' => 0,
      'email' => 0,
      'senha' => 0,
      'perfil' => 'A definir',
    ],
  ],
  JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT
);

require_once __DIR__ . '/includes/header.php';
?>

<body class="form-page">
  <main class="form-shell">
    <section class="section-head" id="perfil">
      <h2>Perfil do usuário</h2>

      <form id="raffleForm" class="form-grid" novalidate>
        <label class="field span-2">
          <span>Titulo da rifa</span>
          <span class="input-icon"><i data-lucide="ticket"></i><input type="text" name="titulo" placeholder="Ex: Moto Honda CG 160" required></span>
        </label>

        <label class="field span-2">
          <span>Descricao</span>
          <textarea name="descricao" rows="4" placeholder="Conte o objetivo da rifa e o premio." required></textarea>
        </label>

        <label class="field">
          <span>Valor por numero</span>
          <span class="input-icon"><i data-lucide="badge-dollar-sign"></i><input type="number" name="valorNumero" min="1" step="0.01" placeholder="10.00" required></span>
        </label>

        <label class="field">
          <span>Quantidade de numeros</span>
          <span class="input-icon"><i data-lucide="hash"></i><input type="number" name="quantidadeNumero" min="10" max="10000" placeholder="1000" required></span>
        </label>

        <label class="field">
          <span>Data do sorteio</span>
          <span class="input-icon"><i data-lucide="calendar"></i><input type="date" name="dataSorteio" required></span>
        </label>

        <label class="field">
          <span>Hora do sorteio</span>
          <span class="input-icon"><i data-lucide="clock"></i><input type="time" name="horaSorteio" required></span>
        </label>

        <div class="form-actions span-2">
          <a class="btn btn-plain" href="dashboard.php">Cancelar</a>
          <button class="btn btn-primary" type="submit">Salvar rifa</button>
        </div>
      </form>
    </section>
  </main>
</body>

<?php require_once __DIR__ . '/includes/footer.php'; ?>