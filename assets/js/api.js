(function () {
  const API_BASE = "api";

  async function request(endpoint, options = {}) {
    const config = {
      headers: {
        "Accept": "application/json",
        "Content-Type": "application/json"
      },
      credentials: "same-origin",
      ...options
    };

    if (config.body && typeof config.body !== "string") {
      config.body = JSON.stringify(config.body);
    }

    const response = await fetch(`${API_BASE}/${endpoint}`, config);
    const contentType = response.headers.get("content-type") || "";
    const data = contentType.includes("application/json") ? await response.json() : await response.text();

    if (!response.ok) {
      const message = typeof data === "object" && data.mensagem ? data.mensagem : "Nao foi possivel concluir a solicitacao.";
      throw new Error(message);
    }

    return data;
  }

  window.RifasAPI = {
    login(payload) {
      return request("login.php", { method: "POST", body: payload });
    },
    logout() {
      return request("logout.php", { method: "POST" });
    },
    registrar(payload) {
      return request("usuarios.php", { method: "POST", body: payload });
    },
    recuperarSenha(payload) {
      return request("recuperar-senha.php", { method: "POST", body: payload });
    },
    usuarioAtual() {
      return request("me.php");
    },
    listarRifas() {
      return request("rifas.php");
    },
    buscarRifa(id) {
      return request(`rifa.php?id=${encodeURIComponent(id)}`);
    },
    criarRifa(payload) {
      return request("rifas.php", { method: "POST", body: payload });
    },
    reservarNumeros(payload) {
      return request("reservas.php", { method: "POST", body: payload });
    },
    compartilhar(payload) {
      return request("compartilhar.php", { method: "POST", body: payload });
    }
  };

  window.RifasUI = {
    initIcons() {
      if (window.lucide) {
        window.lucide.createIcons();
      }
    },
    money(value) {
      return Number(value || 0).toLocaleString("pt-BR", {
        style: "currency",
        currency: "BRL"
      });
    },
    toast(message) {
      const toast = document.getElementById("toast");
      if (!toast) return;
      toast.textContent = message;
      toast.classList.add("show");
      window.clearTimeout(window.__toastTimer);
      window.__toastTimer = window.setTimeout(() => toast.classList.remove("show"), 3200);
    },
    formData(form) {
      return Object.fromEntries(new FormData(form).entries());
    }
  };
})();
