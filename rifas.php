<?php

declare(strict_types=1);

if (session_status() !== PHP_SESSION_ACTIVE) {
  session_start();
}

require_once __DIR__ . '/api/rifas-dados.php';

$pageTitle = 'Rifas | Rifas Solidarias';
$pageHeading = 'Bem-vindo, <span id="userName">' . htmlspecialchars($_SESSION['nome'] ?? 'Usuário') . '</span>!';
$pageSubtitle = 'Gerencie suas rifas e acompanhe seus resultados.';
$activeNav = 'rifas';
$showTopbarActions = true;

$rifaAtual = $rifaAtual ?? null;
$rifaAtual = is_array($rifaAtual) ? $rifaAtual : [];

$dadosRifaJson = json_encode(
  $rifasAtivasDoBanco ?: [
    [
      'id' => 0,
      'titulo' => 'Nenhuma rifa cadastrada',
      'descricao' => 'Cadastre uma rifa para começar.',
      'valorNumero' => 0,
      'quantidadeNumero' => 0,
      'reservados' => 0,
      'dataSorteio' => 'A definir',
      'linkPublico' => '',
      'numeros' => [],
    ],
  ],
  JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT
);

require_once __DIR__ . '/includes/header.php';
?>

<section class="content-grid">
  <div class="main-column">
    <section class="section-head" id="minhas-rifas">
      <h2>Rifas Disponíveis</h2>
    </section>

    <div class="raffles-list">
      <?php if (empty($rifasAtivasDoBanco)): ?>
        <article class="panel raffle-card">
          <div class="raffle-body">
            <span class="badge">Sem rifas</span>
            <h3 id="raffleTitle">Nenhuma rifa cadastrada</h3>
            <p id="raffleDescription">Cadastre uma rifa para começar.</p>
          </div>
        </article>
      <?php else: ?>
        <?php foreach ($rifasAtivasDoBanco as $rifa): ?>
          <article class="panel raffle-card" data-rifa-id="<?= (int) ($rifa['id'] ?? 0) ?>">
            <div class="raffle-body">
              <span class="badge">Ativa</span>
              <h3><?= htmlspecialchars((string) ($rifa['titulo'] ?? 'Rifa sem nome')) ?></h3>
              <p><?= htmlspecialchars((string) ($rifa['descricao'] ?? '')) ?></p>
              <div class="raffle-meta">
                <div><span>Valor por número</span><strong><?= number_format((float) ($rifa['valorNumero'] ?? 0), 2, ',', '.') ?></strong></div>
                <div><span>Números reservados</span><strong><?= (int) ($rifa['reservados'] ?? 0) ?> / <?= (int) ($rifa['quantidadeNumero'] ?? 0) ?></strong>
                  <div class="mini-progress"><span style="width:<?= ((int) ($rifa['quantidadeNumero'] ?? 0) > 0 ? ((int) ($rifa['reservados'] ?? 0) / (int) ($rifa['quantidadeNumero'] ?? 0)) * 100 : 0) ?>%"></span></div>
                </div>
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
                <h2 id="numbers-title">Escolha seus numeros</h2>
                <p>Selecione os numeros desejados para participar da rifa.</p>
              </div>
              <div class="legend">
                <span><b class="dot available"></b>Disponivel</span>
                <span><b class="dot reserved"></b>Reservado</span>
                <span><b class="dot blocked"></b>Indisponivel</span>
              </div>
            </div>
            <div class="numbers-grid" data-rifa-grid="<?= (int) ($rifa['id'] ?? 0) ?>" aria-label="Numeros da rifa"></div>
          </section>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </div>

  <aside class="right-column">
    <section class="panel share-panel">
      <h2>Compartilhe e ajude a alcancar mais pessoas!</h2>
      <div class="share-options">
        <button type="button" data-share="whatsapp"><i data-lucide="message-circle"></i><span>WhatsApp</span></button>
        <button type="button" data-share="facebook"><i data-lucide="facebook"></i><span>Facebook</span></button>
        <button type="button" data-share="copy"><i data-lucide="link"></i><span>Copiar link</span></button>
        <button type="button" data-share="qr"><i data-lucide="qr-code"></i><span>QR Code</span></button>
      </div>
    </section>

    <section class="panel warning-panel">
      <p>Esta rifa e de responsabilidade do organizador. Em caso de duvidas, entre em contato.</p>
    </section>

    <section class="panel checkout-panel">
      <h2>Numeros selecionados </h2>
      <p><span id="selectedCount">0</span> numeros selecionados</p>
      <div id="selectedNumbers" class="selected-list"></div>
      <div class="total-row">
        <span>Total a pagar</span>
        <strong id="totalToPay">R$ 0,00</strong>
      </div>
      <button class="btn btn-primary" type="button" id="continueReservation">Continuar</button>
      <button class="text-button danger" type="button" id="clearSelection"><i data-lucide="trash-2"></i>Limpar selecao</button>
    </section>
  </aside>
</section>

<script id="initialRaffleData" type="application/json">
  <?= $dadosRifaJson ?>
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
