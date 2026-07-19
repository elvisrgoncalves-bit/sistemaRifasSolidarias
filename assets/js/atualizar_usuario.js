const formulario = document.getElementById("raffleForm");
const mensagem = document.getElementById("mensagem");

formulario.addEventListener("submit", async function (e) {
    e.preventDefault();

    mensagem.textContent = "Salvando...";

    const dados = new FormData(formulario);

    try {
        const resposta = await fetch("api/atualizar_usuario.php", {
            method: "POST",
            body: dados
        });

        const resultado = await resposta.json();

        if (resultado.sucesso) {
            mensagem.textContent = resultado.mensagem;
            mensagem.style.color = "green";
        } else {
            mensagem.textContent = resultado.mensagem;
            mensagem.style.color = "red";
        }

    } catch (erro) {
        mensagem.textContent = "Erro ao conectar com o servidor.";
        mensagem.style.color = "red";
    }
});