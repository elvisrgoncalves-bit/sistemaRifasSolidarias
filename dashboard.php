<?php
session_start();

if (!isset($_SESSION['usuario_id'])) {
  header('Location: index.html');
  exit;
}

$perfilUsuario = strtolower((string) ($_SESSION['perfil'] ?? ''));
if (!in_array($perfilUsuario, ['1', '2', 'admin', 'administrador', 'perfil 1', 'perfil 2'], true)) {
  header('Location: rifas.php');
  exit;
}

require_once __DIR__ . '/api/rifas-dados.php';

$pageTitle = 'Painel | Rifas Solidarias';
$pageHeading = 'Bem-vindo, <span id="userName">' . htmlspecialchars($_SESSION['nome'] ?? 'Usuário') . '</span>!';
$pageSubtitle = 'Gerencie suas rifas e acompanhe seus resultados.';
$activeNav = 'dashboard';

require_once __DIR__ . '/includes/header.php';
?>

<section class="dashboard-grid dashboard-summary" aria-label="Resumo">
  <article class="panel create-panel">
    <div class="panel-icon big"><i data-lucide="ticket-plus"></i></div>
    <div>
      <h2>Crie sua rifa</h2>
      <p>Crie uma rifa e compartilhe o link com quem desejar.</p>
      <a class="btn btn-soft" href="criar-rifa.html">Criar Rifa</a>
    </div>
  </article>

  <article class="panel stats-panel" aria-label="Indicadores">
    <div class="stat"><i data-lucide="ticket"></i><strong><?= count($rifasDoBanco) ?></strong><span>Rifa(s) criada(s)</span></div>
    <div class="stat"><i data-lucide="users"></i><strong><?= array_sum(array_column($rifasDoBanco, 'reservados')) ?></strong><span>Números reservados</span></div>
    <div class="stat"><i data-lucide="badge-dollar-sign"></i><strong>R$ <?= number_format(array_sum(array_map(static function ($rifa): float { return ((float) ($rifa['valorNumero'] ?? 0)) * ((int) ($rifa['reservados'] ?? 0)); }, $rifasDoBanco)), 2, ',', '.') ?></strong><span>Total arrecadado</span></div>
    <div class="stat"><i data-lucide="calendar-days"></i><strong><?= count($rifasDoBanco) ?></strong><span>Rifa(s) cadastrada(s)</span></div>
  </article>

  <article class="panel raffle-info">
    <h2>Sobre esta rifa</h2>
    <dl>
      <div><dt><i data-lucide="shield"></i>Valor por numero</dt><dd>R$ <?= number_format((float) ($rifaAtual['valorNumero'] ?? 0), 2, ',', '.') ?></dd></div>
      <div><dt><i data-lucide="calendar"></i>Data do sorteio</dt><dd><?= htmlspecialchars((string) ($rifaAtual['dataSorteio'] ?? 'A definir')) ?></dd></div>
      <div><dt><i data-lucide="hash"></i>Total de numeros</dt><dd><?= (int) ($rifaAtual['quantidadeNumero'] ?? 0) ?></dd></div>
      <div><dt><i data-lucide="badge-check"></i>Números reservados</dt><dd><?= (int) ($rifaAtual['reservados'] ?? 0) ?></dd></div>
    </dl>
    <div class="progress-line"><span style="width:<?= !empty($rifaAtual['quantidadeNumero']) ? min(100, ((int) ($rifaAtual['reservados'] ?? 0) / (int) $rifaAtual['quantidadeNumero']) * 100) : 0 ?>%"></span></div>
    <button class="btn btn-primary" type="button" data-scroll="#numeros">Escolher Números</button>
  </article>
</section>

<section class="content-grid">
  <div class="main-column">
    <section class="section-head" id="minhas-rifas">
      <h2>Minhas rifas</h2>
    </section>

    <?php if (empty($rifasDoBanco)): ?>
      <article class="panel raffle-card">
        <div class="raffle-thumb"><i data-lucide="ticket-heart"></i></div>
        <div class="raffle-body">
          <span class="badge">Sem rifas</span>
          <h3>Nenhuma rifa criada por você</h3>
          <p>Crie sua primeira rifa para começar.</p>
        </div>
      </article>
    <?php else: ?>
      <?php foreach ($rifasDoBanco as $rifa): ?>
        <article class="panel raffle-card" data-rifa-id="<?= (int) ($rifa['id'] ?? 0) ?>">
          <div class="raffle-thumb"><i data-lucide="ticket-heart"></i></div>
          <div class="raffle-body">
            <span class="badge">Ativa</span>
            <h3><?= htmlspecialchars((string) ($rifa['titulo'] ?? 'Rifa sem nome')) ?></h3>
            <p><?= htmlspecialchars((string) ($rifa['descricao'] ?? '')) ?></p>
            <div class="raffle-meta">
              <div><span>Valor por número</span><strong>R$ <?= number_format((float) ($rifa['valorNumero'] ?? 0), 2, ',', '.') ?></strong></div>
              <div><span>Números reservados</span><strong><?= (int) ($rifa['reservados'] ?? 0) ?> / <?= (int) ($rifa['quantidadeNumero'] ?? 0) ?></strong><div class="mini-progress"><span style="width:<?= !empty($rifa['quantidadeNumero']) ? min(100, ((int) ($rifa['reservados'] ?? 0) / (int) $rifa['quantidadeNumero']) * 100) : 0 ?>%"></span></div></div>
              <div><span>Sorteio</span><strong><?= htmlspecialchars((string) ($rifa['dataSorteio'] ?? 'A definir')) ?></strong></div>
            </div>
          </div>
          <div class="raffle-actions">
            <button class="btn btn-outline btn-select-rifa" type="button" data-rifa-id="<?= (int) ($rifa['id'] ?? 0) ?>"><i data-lucide="eye"></i>Visualizar Números</button>
          </div>
        </article>

        <section class="panel numbers-panel is-hidden" data-rifa-panel="<?= (int) ($rifa['id'] ?? 0) ?>" aria-labelledby="numbers-title" aria-hidden="true">
          <div class="numbers-head">
            <button class="icon-button" type="button" data-scroll="#minhas-rifas" aria-label="Voltar"><i data-lucide="arrow-left"></i></button>
            <div>
              <h2 id="numbers-title">Escolha seus números</h2>
              <p>Selecione os números desejados para participar da rifa.</p>
            </div>
            <div class="legend">
              <span><b class="dot available"></b>Disponível</span>
              <span><b class="dot reserved"></b>Reservado</span>
              <span><b class="dot blocked"></b>Indisponível</span>
            </div>
          </div>
          <div class="numbers-grid" data-rifa-grid="<?= (int) ($rifa['id'] ?? 0) ?>" aria-label="Números da rifa"></div>
        </section>
      <?php endforeach; ?>
    <?php endif; ?>

    <p class="tip"><i data-lucide="shield-check"></i>Dica: compartilhe o link da sua rifa nas redes sociais e grupos de WhatsApp para alcançar mais pessoas.</p>
  </div>

  <aside class="right-column">
    <section class="panel share-panel">
      <h2>Compartilhe e ajude a alcançar mais pessoas!</h2>
      <div class="share-options">
        <button type="button" data-share="whatsapp"><i data-lucide="message-circle"></i><span>WhatsApp</span></button>
        <button type="button" data-share="facebook"><i data-lucide="facebook"></i><span>Facebook</span></button>
        <button type="button" data-share="copy"><i data-lucide="link"></i><span>Copiar link</span></button>
        <button type="button" data-share="qr"><i data-lucide="qr-code"></i><span>QR Code</span></button>
      </div>
    </section>

    <section class="panel warning-panel">
      <p>Esta rifa é de responsabilidade do organizador. Em caso de dúvidas, entre em contato.</p>
    </section>
  </aside>
</section>

<script id="initialRaffleData" type="application/json">
  <?= json_encode($rifasDoBanco ?: [], JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) ?>
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
