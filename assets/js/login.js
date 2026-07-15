document.addEventListener("DOMContentLoaded", () => {
  RifasUI.initIcons();

  const loginForm = document.getElementById("loginForm");
  const registerModal = document.getElementById("registerModal");
  const registerForm = document.getElementById("registerForm");
  const openRegister = document.getElementById("openRegister");
  const passwordInput = document.getElementById("passwordInput");
  const togglePassword = document.getElementById("togglePassword");

  openRegister.addEventListener("click", () => registerModal.showModal());

  document.querySelectorAll("[data-close-modal]").forEach((button) => {
    button.addEventListener("click", () => button.closest("dialog").close());
  });

  togglePassword.addEventListener("click", () => {
    const showPassword = passwordInput.type === "password";
    passwordInput.type = showPassword ? "text" : "password";
    togglePassword.innerHTML = showPassword ? '<i data-lucide="eye"></i>' : '<i data-lucide="eye-off"></i>';
    togglePassword.setAttribute("aria-label", showPassword ? "Ocultar senha" : "Mostrar senha");
    RifasUI.initIcons();
  });

  loginForm.addEventListener("submit", async (event) => {
    event.preventDefault();
    if (!loginForm.reportValidity()) return;

    const payload = RifasUI.formData(loginForm);

    try {
      const usuario = await RifasAPI.login(payload);
      localStorage.setItem("rifas_user", JSON.stringify(usuario));
      window.location.href = "dashboard.php";
    } catch (error) {
      RifasUI.toast(error.message);
    }
  });

  registerForm.addEventListener("submit", async (event) => {
    event.preventDefault();
    if (!registerForm.reportValidity()) return;

    try {
      await RifasAPI.registrar(RifasUI.formData(registerForm));
      registerModal.close();
      RifasUI.toast("Conta criada com sucesso.");
    } catch (error) {
      RifasUI.toast("Cadastro pronto no front. Crie o endpoint api/usuarios.php para salvar.");
    }
  });

  document.querySelector("[data-open-recovery]").addEventListener("click", async (event) => {
    event.preventDefault();
    const email = loginForm.email.value.trim();
    if (!email) {
      RifasUI.toast("Digite seu e-mail para recuperar a senha.");
      return;
    }

    try {
      await RifasAPI.recuperarSenha({ email });
      RifasUI.toast("Enviamos as instrucoes para seu e-mail.");
    } catch (error) {
      RifasUI.toast("Crie api/recuperar-senha.php para ativar esta funcao.");
    }
  });
});
