<?php
if (!isset($pageTitle)) {
  $pageTitle = 'Painel | Rifas Solidarias';
}
if (!isset($pageHeading)) {
  $pageHeading = 'Bem-vindo, <span id="userName">Usuário</span>!';
}
if (!isset($pageSubtitle)) {
  $pageSubtitle = 'Gerencie suas rifas e acompanhe seus resultados.';
}
if (!isset($activeNav)) {
  $activeNav = 'dashboard';
}
if (!isset($showTopbarActions)) {
  $showTopbarActions = true;
}
?>
<!doctype html>
<html lang="pt-BR">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= htmlspecialchars($pageTitle) ?></title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body class="app-page">
  <?php require_once __DIR__ . '/sidebar.php'; ?>

  <main class="dashboard">
    <header class="topbar">
      <div>
        <h1><?= $pageHeading ?></h1>
        <p><?= $pageSubtitle ?></p>
      </div>
      <?php if ($showTopbarActions): ?>
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
      <?php endif; ?>
    </header>
