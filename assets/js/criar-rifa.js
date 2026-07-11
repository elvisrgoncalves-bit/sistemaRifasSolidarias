document.addEventListener("DOMContentLoaded", () => {
  RifasUI.initIcons();

  const form = document.getElementById("raffleForm");

  form.addEventListener("submit", async (event) => {
    event.preventDefault();
    if (!form.reportValidity()) return;

    const payload = RifasUI.formData(form);
    payload.valorNumero = Number(payload.valorNumero);
    payload.quantidadeNumero = Number(payload.quantidadeNumero);

    try {
      await RifasAPI.criarRifa(payload);
      RifasUI.toast("Rifa criada com sucesso.");
      window.setTimeout(() => window.location.href = "dashboard.html", 700);
    } catch (error) {
      const demoRaffle = {
        ...payload,
        id: Date.now(),
        createdAt: new Date().toISOString()
      };
      localStorage.setItem("rifas_demo_last", JSON.stringify(demoRaffle));
      RifasUI.toast("Formulario pronto. Crie api/rifas.php para salvar no banco.");
      window.setTimeout(() => window.location.href = "dashboard.html", 900);
    }
  });
});
