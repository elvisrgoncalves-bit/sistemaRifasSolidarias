<?php
if (!isset($activeNav)) {
  $activeNav = 'dashboard';
}

$perfilUsuario = strtolower((string) ($_SESSION['perfil'] ?? ''));
$perfilUsuarioId = isset($_SESSION['perfil_id']) ? (string) $_SESSION['perfil_id'] : '';
$mostrarDashboard = in_array($perfilUsuario, ['1', '2', 'admin', 'administrador', 'perfil 1', 'perfil 2', 'organizador'], true)
  || in_array($perfilUsuarioId, ['1', '2'], true);
?>
<aside class="sidebar">
  <a class="brand" href="dashboard.php" aria-label="Rifas Solidarias">
    <span class="brand-mark"><i data-lucide="ticket-heart"></i></span>
    <span>
      <strong>Rifa</strong>
      <em>Solidaria</em>
    </span>
  </a>

  <nav class="side-nav" aria-label="Menu principal">
    <?php if ($mostrarDashboard): ?>
      <a class="<?= $activeNav === 'dashboard' ? 'active' : '' ?>" href="dashboard.php"><i data-lucide="home"></i>Inicio</a>
    <?php endif; ?>
    <a class="<?= $activeNav === 'rifas' ? 'active' : '' ?>" href="rifas.php"><i data-lucide="binary"></i>Rifas</a>
    <a href="usuario.php"><i data-lucide="user"></i>Usuário</a>
    <a href="#ajuda"><i data-lucide="circle-help"></i>Ajuda</a>
  </nav>
</aside>
