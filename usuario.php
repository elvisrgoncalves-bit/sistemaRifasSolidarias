<?php
declare(strict_types=1);

if (session_status() !== PHP_SESSION_ACTIVE) {
  session_start();
}

require_once __DIR__ . '/api/usuario.php';

$pageTitle = 'Usuários | Rifas Solidárias';
$pageHeading = 'Bem-vindo, <span id="userName">' . htmlspecialchars($_SESSION['nome'] ?? 'Usuário') . '</span>!';
$pageSubtitle = 'Visualize o seu perfil. Para se tornar um organizador, entre em contato com o administrador.';
$activeNav = 'usuario';
$showTopbarActions = true;

require_once __DIR__ . '/includes/header.php';
?>

<body class="form-page">

  <main class="form-shell">
    <section class="section-head" id="perfil">
      <h2>Perfil do usuário</h2>

      <form id="raffleForm" class="form-grid" novalidate>

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

        <label class="field span-2">
          <span>Telefone</span>
          <span class="input-icon">
            <i data-lucide="phone"></i>
            <input
              type="tel"
              id="telefone"
              name="telefone"
              maxlength="15"
              value="<?= htmlspecialchars($usuarioLogado['telefone'] ?? '') ?>"
              required>
          </span>
        </label>

        <label class="field">
          <span>Endereço</span>
          <span class="input-icon">
            <i data-lucide="map-pin"></i>
            <input
              type="text"
              name="endereco"
              value="<?= htmlspecialchars($usuarioLogado['endereco'] ?? '') ?>"
              required>
          </span>
        </label>

        <label class="field">
          <span>E-mail</span>
          <span class="input-icon">
            <i data-lucide="mail"></i>
            <input
              type="email"
              name="email"
              value="<?= htmlspecialchars($usuarioLogado['email'] ?? '') ?>"
              required>
          </span>
        </label>

        <label class="field">
          <span>Senha</span>
          <span class="input-icon">
            <i data-lucide="lock"></i>
            <input
              type="password"
              name="senha"
              placeholder="Digite uma nova senha">
          </span>
        </label>

        <label class="field">
          <span>Perfil</span>
          <span class="input-icon">
            <i data-lucide="shield"></i>
            <input
              type="text"
              name="perfil"
              value="<?= htmlspecialchars($usuarioLogado['perfil'] ?? '') ?>"
              readonly>
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

  <script>
    function aplicarMascaraTelefone(valor) {
      let v = valor.replace(/\D/g, "");

      if (v.length > 11) {
        v = v.substring(0, 11);
      }

      if (v.length === 11) {
        v = v.replace(/^(\d{2})(\d{5})(\d{4})$/, "($1) $2-$3");
      } else if (v.length === 10) {
        v = v.replace(/^(\d{2})(\d{4})(\d{4})$/, "($1) $2-$3");
      } else if (v.length > 6) {
        v = v.replace(/^(\d{2})(\d{4})(\d+)$/, "($1) $2-$3");
      } else if (v.length > 2) {
        v = v.replace(/^(\d{2})(\d+)$/, "($1) $2");
      } else if (v.length > 0) {
        v = v.replace(/^(\d*)$/, "($1");
      }

      return v;
    }

    // Executa quando o DOM está pronto
    document.addEventListener("DOMContentLoaded", function () {
      const telefone = document.getElementById("telefone");

      if (telefone) {
        // Aplica máscara ao carregar se houver valor
        if (telefone.value) {
          telefone.value = aplicarMascaraTelefone(telefone.value);
        }

        // Aplica máscara enquanto digita
        telefone.addEventListener("input", function (e) {
          e.target.value = aplicarMascaraTelefone(e.target.value);
        });
      }
    });
  </script>
</body>
</html>