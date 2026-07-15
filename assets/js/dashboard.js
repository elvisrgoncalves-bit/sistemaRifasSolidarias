document.addEventListener("DOMContentLoaded", () => {
  RifasUI.initIcons();

  const initialRaffleData = document.getElementById("initialRaffleData");

  function parseInitialRaffleData() {
    const raw = initialRaffleData?.textContent?.trim() ?? "";
    if (!raw) return null;

    try {
      return JSON.parse(raw);
    } catch (error) {
      const decoded = raw
        .replace(/&quot;/g, '"')
        .replace(/&#39;/g, "'")
        .replace(/&amp;/g, "&")
        .replace(/&lt;/g, "<")
        .replace(/&gt;/g, ">");

      try {
        return JSON.parse(decoded);
      } catch (decodeError) {
        return null;
      }
    }
  }

  function normalizeRaffleData(raffle) {
    const safeRaffle = {
      id: 0,
      titulo: "Nenhuma rifa cadastrada",
      descricao: "Cadastre uma rifa para começar.",
      valorNumero: 0,
      quantidadeNumero: 0,
      reservados: 0,
      dataSorteio: "A definir",
      linkPublico: "",
      numeros: [],
      ...(raffle || {})
    };

    if (!Array.isArray(safeRaffle.numeros)) {
      safeRaffle.numeros = [];
    }

    if (safeRaffle.numeros.length === 0 && safeRaffle.quantidadeNumero > 0) {
      safeRaffle.numeros = Array.from({ length: safeRaffle.quantidadeNumero }, (_, index) => ({
        numero: index + 1,
        status: "disponivel"
      }));
    }

    return safeRaffle;
  }

  function normalizeRaffleList(input) {
    if (Array.isArray(input)) {
      return input.map(normalizeRaffleData).filter((item) => item.id);
    }

    const single = normalizeRaffleData(input?.id ? input : null);
    return single.id ? [single] : [];
  }

  const initialRaffle = parseInitialRaffleData();
  const initialRaffles = normalizeRaffleList(initialRaffle);
  const state = {
    rifas: initialRaffles,
    rifa: initialRaffles[0] ? initialRaffles[0] : normalizeRaffleData(null),
    selecionados: new Set()
  };

  const selectedNumbers = document.getElementById("selectedNumbers");
  const selectedCount = document.getElementById("selectedCount");
  const totalToPay = document.getElementById("totalToPay");
  const reservationModal = document.getElementById("reservationModal");
  const reservationForm = document.getElementById("reservationForm");

  function statusClass(status) {
    if (status === "indisponivel") return "blocked";
    if (status === "reservado" || status === "reserved") return "reserved";
    return "available";
  }

  function padded(number) {
    return String(number).padStart(3, "0");
  }

  function setText(id, value) {
    const element = document.getElementById(id);
    if (element) {
      element.textContent = value;
    }
  }

  function renderRaffle() {
    const rifa = state.rifa;
    const percent = rifa.quantidadeNumero ? Math.round((rifa.reservados / rifa.quantidadeNumero) * 100) : 0;

    setText("raffleTitle", rifa.titulo || "Nenhuma rifa cadastrada");
    setText("raffleDescription", rifa.descricao || "Cadastre uma rifa para começar.");
    setText("publicLink", rifa.linkPublico || "");
    setText("statReservados", rifa.reservados ?? 0);
    setText("statTotal", RifasUI.money((rifa.reservados ?? 0) * (rifa.valorNumero ?? 0)));
    setText("statSorteios", rifa.dataSorteio && rifa.dataSorteio !== "A definir" ? 1 : 0);

    const infoValue = document.getElementById("infoValor");
    if (infoValue) infoValue.textContent = RifasUI.money(rifa.valorNumero || 0);
    const infoData = document.getElementById("infoData");
    if (infoData) infoData.textContent = rifa.dataSorteio || "A definir";
    const infoTotal = document.getElementById("infoTotal");
    if (infoTotal) infoTotal.textContent = rifa.quantidadeNumero || 0;
    const infoReservados = document.getElementById("infoReservados");
    if (infoReservados) infoReservados.textContent = `${rifa.reservados ?? 0} (${percent}%)`;
    const infoProgress = document.getElementById("infoProgress");
    if (infoProgress) infoProgress.style.width = `${percent}%`;
  }

  function renderNumbers() {
    document.querySelectorAll('[data-rifa-grid]').forEach((grid) => {
      grid.innerHTML = "";
    });

    const currentGrid = document.querySelector(`[data-rifa-grid="${state.rifa.id}"]`);
    if (!currentGrid) return;

    const numeros = Array.isArray(state.rifa?.numeros) ? state.rifa.numeros : [];
    numeros.forEach((item) => {
      const button = document.createElement("button");
      button.type = "button";
      button.className = `number-button ${statusClass(item.status)}`;
      button.textContent = padded(item.numero);
      button.disabled = item.status === "indisponivel" || item.status === "reservado" || item.status === "reserved";

      if (state.selecionados.has(item.numero)) {
        button.classList.add("selected");
      }

      button.addEventListener("click", () => {
        if (item.status === "indisponivel") return;
        if (state.selecionados.has(item.numero)) {
          state.selecionados.delete(item.numero);
        } else {
          state.selecionados.add(item.numero);
        }
        renderNumbers();
        renderSelection();
      });

      currentGrid.appendChild(button);
    });
  }

  function renderSelection() {
    const numeros = Array.from(state.selecionados).sort((a, b) => a - b);

    const selectionContainer = document.getElementById("selectedNumbers");
    const selectionCount = document.getElementById("selectedCount");
    const selectionTotal = document.getElementById("totalToPay");

    if (selectionCount) {
      selectionCount.textContent = numeros.length;
    }

    if (selectionTotal) {
      selectionTotal.textContent = RifasUI.money(numeros.length * state.rifa.valorNumero);
    }

    if (!selectionContainer || !(selectionContainer instanceof HTMLElement)) {
      return;
    }

    selectionContainer.innerHTML = "";

    numeros.forEach((numero) => {
      const chip = document.createElement("button");
      chip.type = "button";
      chip.className = "selected-chip";
      chip.textContent = `${padded(numero)} x`;
      chip.addEventListener("click", () => {
        state.selecionados.delete(numero);
        renderNumbers();
        renderSelection();
      });
      selectionContainer.appendChild(chip);
    });
  }

  async function loadData() {
    const storedUser = JSON.parse(localStorage.getItem("rifas_user") || "{}");
    if (storedUser.nome) {
      document.getElementById("userName").textContent = storedUser.nome.split(" ")[0];
    }

    if (state.rifas.length) {
      state.rifa = normalizeRaffleData({
        ...state.rifa,
        ...state.rifas[0],
        numeros: state.rifas[0].numeros || state.rifa.numeros
      });
    } else {
      state.rifa = normalizeRaffleData(state.rifa);
    }

    renderRaffle();
    renderNumbers();
    renderSelection();
  }

  document.querySelectorAll("[data-scroll]").forEach((button) => {
    button.addEventListener("click", () => {
      document.querySelector(button.dataset.scroll)?.scrollIntoView({ behavior: "smooth", block: "start" });
    });
  });

  function syncRaffleButtons() {
    document.querySelectorAll(".btn-select-rifa").forEach((button) => {
      const rifaId = Number(button.dataset.rifaId);
      const panel = document.querySelector(`[data-rifa-panel="${rifaId}"]`);
      const isActive = state.rifa?.id === rifaId && panel && !panel.classList.contains("is-hidden");

      button.innerHTML = isActive
        ? '<i data-lucide="eye-off"></i>Ocultar Números'
        : '<i data-lucide="eye"></i>Visualizar Números';
    });

    if (window.lucide) {
      window.lucide.createIcons();
    }
  }

  document.querySelectorAll(".btn-select-rifa").forEach((button) => {
    button.addEventListener("click", () => {
      const rifaId = Number(button.dataset.rifaId);
      const selectedRaffle = state.rifas.find((item) => item.id === rifaId);
      const panel = document.querySelector(`[data-rifa-panel="${rifaId}"]`);

      if (!selectedRaffle || !panel) {
        return;
      }

      const isAlreadyActive = state.rifa?.id === rifaId && !panel.classList.contains("is-hidden");

      if (isAlreadyActive) {
        document.querySelectorAll('[data-rifa-panel]').forEach((item) => {
          item.classList.add("is-hidden");
          item.setAttribute("aria-hidden", "true");
        });
        syncRaffleButtons();
        return;
      }

      document.querySelectorAll('[data-rifa-panel]').forEach((item) => {
        item.classList.add("is-hidden");
        item.setAttribute("aria-hidden", "true");
      });

      state.rifa = normalizeRaffleData(selectedRaffle);
      state.selecionados.clear();
      renderRaffle();
      renderNumbers();
      renderSelection();

      panel.classList.remove("is-hidden");
      panel.setAttribute("aria-hidden", "false");
      panel.scrollIntoView({ behavior: "smooth", block: "start" });
      syncRaffleButtons();
    });
  });

  document.querySelectorAll("[data-close-modal]").forEach((button) => {
    button.addEventListener("click", () => button.closest("dialog").close());
  });

  document.getElementById("clearSelection")?.addEventListener("click", () => {
    state.selecionados.clear();
    renderNumbers();
    renderSelection();
  });

  document.getElementById("continueReservation")?.addEventListener("click", () => {
    if (!state.selecionados.size) {
      RifasUI.toast("Escolha pelo menos um numero.");
      return;
    }
    reservationModal.showModal();
  });

  reservationForm?.addEventListener("submit", async (event) => {
    event.preventDefault();
    if (!reservationForm.reportValidity()) return;

    const payload = {
      rifaId: state.rifa.id,
      numeros: Array.from(state.selecionados),
      participante: RifasUI.formData(reservationForm)
    };

    try {
      await RifasAPI.reservarNumeros(payload);
      RifasUI.toast("Numeros reservados com sucesso.");
      reservationModal.close();
      state.selecionados.clear();
      renderSelection();
    } catch (error) {
      RifasUI.toast("Front pronto. Crie api/reservas.php para confirmar reservas.");
    }
  });

  document.getElementById("copyLink")?.addEventListener("click", async () => {
    await navigator.clipboard.writeText(state.rifa.linkPublico);
    RifasUI.toast("Link copiado.");
  });

  document.getElementById("shareRaffle")?.addEventListener("click", () => {
    navigator.share?.({ title: state.rifa.titulo, text: state.rifa.descricao, url: `https://${state.rifa.linkPublico}` })
      || RifasUI.toast("Use os botoes de compartilhamento ao lado.");
  });

  document.getElementById("sharePlatform")?.addEventListener("click", async () => {
    try {
      await RifasAPI.compartilhar({ tipo: "plataforma" });
    } catch (error) {
      // Opcional no backend.
    }
    RifasUI.toast("Compartilhe o link da plataforma com seus contatos.");
  });

  document.getElementById("logoutButton")?.addEventListener("click", async () => {
    try {
      await RifasAPI.logout();
    } catch (error) {
      // Mesmo com falha de rede, limpa os dados locais e volta ao login.
    }

    localStorage.removeItem("rifas_user");
    window.location.href = "login.html";
  });

  document.querySelectorAll("[data-share]").forEach((button) => {
    button.addEventListener("click", async () => {
      const link = `https://${state.rifa.linkPublico}`;
      const type = button.dataset.share;

      if (type === "copy" || type === "qr") {
        await navigator.clipboard.writeText(link);
        RifasUI.toast(type === "qr" ? "Link copiado para gerar QR Code no backend." : "Link copiado.");
        return;
      }

      const url = type === "whatsapp"
        ? `https://wa.me/?text=${encodeURIComponent(`${state.rifa.titulo} - ${link}`)}`
        : `https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(link)}`;

      window.open(url, "_blank", "noopener,noreferrer");
    });
  });

  loadData();
});
