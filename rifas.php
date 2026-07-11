<?php
declare(strict_types=1);

if (session_status() !== PHP_SESSION_ACTIVE) {
  session_start();
}

require_once __DIR__ . '/api/rifas-dados.php';

$rifaAtual = $rifaAtual ?? null;
$rifaAtual = is_array($rifaAtual) ? $rifaAtual : [];

$dadosRifaJson = json_encode(
  $rifaAtual ?: [
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
  JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT
);
?>
<!doctype html>
<html lang="pt-BR">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Painel | Rifas Solidarias</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body class="app-page">
  <aside class="sidebar">
    <a class="brand" href="rifas.html" aria-label="Rifas Solidarias">
      <span class="brand-mark"><i data-lucide="ticket-heart"></i></span>
      <span>
        <strong>Rifa</strong>
        <em>Solidaria</em>
      </span>
    </a>

    <nav class="side-nav" aria-label="Menu principal">
      <!-- <a class="active" href="dashboard.html"><i data-lucide="home"></i>Inicio</a> -->
      <!-- <a href="#minhas-rifas"><i data-lucide="calendar-check"></i>Minhas Rifas</a> -->
      <a href="rifas.php"><i data-lucide="binary"></i>Rifas</a>
      <a href="#perfil"><i data-lucide="user"></i>Perfil</a>
      <a href="#ajuda"><i data-lucide="circle-help"></i>Ajuda</a>
    </nav>
  </aside>

  <main class="dashboard">
    <header class="topbar">
      <div>
        <h1>Bem-vindo, <span id="userName"><?= htmlspecialchars($_SESSION['nome'] ?? 'Usuário') ?></span>!</h1>
        <p>Gerencie suas rifas e acompanhe seus resultados.</p>
      </div>
      <div class="topbar-actions">
        <button class="btn btn-light" type="button" id="sharePlatform">
          <i data-lucide="user-plus"></i>
          Compartilhar plataforma
        </button>
        <button class="btn btn-plain" type="button" id="logoutButton">
          <i data-lucide="log-out"></i>
          Sair
        </button>
      </div>
    </header>

    <section class="dashboard-grid dashboard-summary" aria-label="Resumo">

      <!-- <article class="panel stats-panel" aria-label="Indicadores">
        <div class="stat"><i data-lucide="ticket"></i><strong id="statRifas"><?= count($rifasDoBanco) ?></strong><span>Rifa criada</span></div>
        <div class="stat"><i data-lucide="users"></i><strong id="statReservados"><?= (int) ($rifaAtual['reservados'] ?? 0) ?></strong><span>Numeros reservados</span></div>
        <div class="stat"><i data-lucide="badge-dollar-sign"></i><strong id="statTotal">R$ <?= number_format(((int) ($rifaAtual['reservados'] ?? 0)) * ((float) ($rifaAtual['valorNumero'] ?? 0)), 2, ',', '.') ?></strong><span>Total arrecadado</span></div>
        <div class="stat"><i data-lucide="calendar-days"></i><strong id="statSorteios"><?= $rifaAtual ? 1 : 0 ?></strong><span>Sorteio agendado</span></div>
      </article> -->

      <!-- <article class="panel raffle-info">
        <h2>Sobre esta rifa</h2>
        <dl>
          <div><dt><i data-lucide="shield"></i>Valor por numero</dt><dd id="infoValor">R$ 10,00</dd></div>
          <div><dt><i data-lucide="calendar"></i>Data do sorteio</dt><dd id="infoData">31/08/2026 as 18:00</dd></div>
          <div><dt><i data-lucide="hash"></i>Total de numeros</dt><dd id="infoTotal">1000</dd></div>
          <div><dt><i data-lucide="badge-check"></i>Numeros reservados</dt><dd id="infoReservados">450 (45%)</dd></div>
        </dl>
        <div class="progress-line"><span id="infoProgress" style="width:45%"></span></div>
        <button class="btn btn-primary" type="button" data-scroll="#numeros">Escolher Numeros</button>
      </article> -->
    </section>

    <section class="content-grid">
      <div class="main-column">
        <section class="section-head" id="minhas-rifas">
          <h2>Rifas Disponíveis</h2>
        </section>

        <article class="panel raffle-card">
          <div class="raffle-thumb"><i data-lucide="ticket-heart"></i></div>
          <div class="raffle-body">
            <span class="badge">Ativa</span>
            <h3 id="raffleTitle"><?= htmlspecialchars($rifaAtual['titulo'] ?? 'Nenhuma rifa cadastrada') ?></h3>
            <p id="raffleDescription"><?= htmlspecialchars($rifaAtual['descricao'] ?? 'Cadastre uma rifa para começar.') ?></p>
            <div class="raffle-meta">
              <div><span>Valor por numerosss</span><strong id="rafflePrice"><?= $rifaAtual['valorNumero'] ?? '0,00' ?></strong></div>
              <div><span>Numeros reservados</span><strong id="raffleReserved"><?= $rifaAtual['reservados'] ?? '0' ?> / <?= $rifaAtual['quantidadeNumero'] ?? '0' ?></strong><div class="mini-progress"><span style="width:<?= ($rifaAtual['reservados'] ?? 0) / ($rifaAtual['quantidadeNumero'] ?? 1) * 100 ?>%"></span></div></div>
              <div><span>Sorteio</span><strong id="raffleDate"><?= $rifaAtual['dataSorteio'] ?? 'A definir' ?></strong></div>
            </div>
            <div class="public-link">
              <span>Link publico da rifa</span>
              <strong id="publicLink"><?= htmlspecialchars($rifaAtual['linkPublico'] ?? '') ?></strong>
              <button class="text-button" type="button" id="copyLink"><i data-lucide="link"></i>Copiar link</button>
            </div>
          </div>
          <div class="raffle-actions">
            <button class="btn btn-outline" type="button" id="visualizarRifa"><i data-lucide="eye"></i>Visualizar Números</button>
          </div>
        </article>

        <p class="tip"><i data-lucide="shield-check"></i>Dica: compartilhe o link da sua rifa nas redes sociais e grupos de WhatsApp para alcancar mais pessoas.</p>

        <section class="panel numbers-panel is-hidden" id="numeros" aria-labelledby="numbers-title" aria-hidden="true">
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
          <div id="numbersGrid" class="numbers-grid" aria-label="Numeros da rifa"></div>
        </section>
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
          <h2>Numeros selecionados</h2>
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
  </main>

  <dialog class="modal" id="reservationModal">
    <form id="reservationForm" class="modal-card form-stack" method="dialog" novalidate>
      <div class="modal-head">
        <div>
          <h2>Reservar numeros</h2>
          <p>Informe seus dados para confirmar a reserva.</p>
        </div>
        <button class="icon-button" type="button" data-close-modal aria-label="Fechar"><i data-lucide="x"></i></button>
      </div>
      <label class="field">
        <span>Nome</span>
        <span class="input-icon"><i data-lucide="user"></i><input type="text" name="nome" required placeholder="Nome completo"></span>
      </label>
      <label class="field">
        <span>Telefone</span>
        <span class="input-icon"><i data-lucide="phone"></i><input type="tel" name="telefone" required placeholder="WhatsApp"></span>
      </label>
      <label class="field">
        <span>E-mail</span>
        <span class="input-icon"><i data-lucide="mail"></i><input type="email" name="email" placeholder="voce@email.com"></span>
      </label>
      <button class="btn btn-primary" type="submit">Confirmar reserva</button>
    </form>
  </dialog>

  <div id="toast" class="toast" role="status" aria-live="polite"></div>
  <script id="initialRaffleData" type="application/json"><?= $dadosRifaJson ?></script>

  <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.min.js"></script>
  <script src="assets/js/api.js"></script>
  <script src="assets/js/dashboard.js"></script>
</body>
</html>
