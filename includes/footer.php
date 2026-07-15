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

  <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.min.js"></script>
  <script src="assets/js/api.js"></script>
  <script src="assets/js/dashboard.js"></script>
</body>
</html>
