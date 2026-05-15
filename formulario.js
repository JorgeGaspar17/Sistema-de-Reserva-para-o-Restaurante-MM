/* =========================
   FORMULÁRIO DE RESERVA
========================= */

const formReserva = document.getElementById("formReserva");

/* =========================
   CAMPOS
========================= */

const nome = document.querySelector('input[name="nome"]');
const email = document.querySelector('input[name="email"]');
const telefone = document.querySelector('input[name="telefone"]');
const dataReserva = document.querySelector('input[name="data_reserva"]');
const horaReserva = document.querySelector('input[name="hora_reserva"]');
const numPessoa = document.querySelector('input[name="num_pessoa"]');
const mesa = document.querySelector('select[name="mesa"]');

/* =========================
   DATA MÍNIMA = HOJE
========================= */

const hoje = new Date().toISOString().split("T")[0];
dataReserva.min = hoje;

/* =========================
   MÁSCARA TELEFONE
========================= */

telefone.addEventListener("input", () => {

    telefone.value = telefone.value
    .replace(/\D/g, "")
    .replace(/(\d{3})(\d)/, "$1 $2")
    .replace(/(\d{3})(\d)/, "$1 $2")
    .replace(/(\d{3})(\d{1,3})$/, "$1-$2");

});

/* =========================
   VALIDAÇÃO FORM
========================= */

formReserva.addEventListener("submit", function(event){

    /* PREVENIR ENVIO */
    event.preventDefault();

    /* PEGAR VALORES */
    const nomeValue = nome.value.trim();
    const emailValue = email.value.trim();
    const telefoneValue = telefone.value.trim();
    const dataValue = dataReserva.value;
    const horaValue = horaReserva.value;
    const pessoasValue = parseInt(numPessoa.value);
    const mesaValue = mesa.value;

    /* =========================
       VALIDAÇÃO NOME
    ========================= */

    if(nomeValue.length < 3){

        mostrarMensagem(
            "Digite um nome válido com pelo menos 3 letras.",
            "erro"
        );

        nome.focus();
        return;
    }

    /* =========================
       VALIDAR EMAIL
    ========================= */

    const emailRegex =
    /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

    if(!emailRegex.test(emailValue)){

        mostrarMensagem(
            "Digite um e-mail válido.",
            "erro"
        );

        email.focus();
        return;
    }

    /* =========================
       VALIDAR TELEFONE
    ========================= */

    if(telefoneValue.length < 9){

        mostrarMensagem(
            "Digite um telefone válido.",
            "erro"
        );

        telefone.focus();
        return;
    }

    /* =========================
       VALIDAR DATA
    ========================= */

    const dataSelecionada =
    new Date(dataValue);

    const dataAtual =
    new Date();

    dataAtual.setHours(0,0,0,0);

    if(dataSelecionada < dataAtual){

        mostrarMensagem(
            "A data da reserva não pode ser anterior ao dia atual.",
            "erro"
        );

        return;
    }

    /* =========================
       VALIDAR HORÁRIO
    ========================= */

    const hora = parseInt(horaValue.split(":")[0]);

    if(hora < 8 || hora > 22){

        mostrarMensagem(
            "O restaurante funciona das 08h às 22h.",
            "erro"
        );

        horaReserva.focus();
        return;
    }

    /* =========================
       VALIDAR PESSOAS
    ========================= */

    if(pessoasValue < 1 || pessoasValue > 10){

        mostrarMensagem(
            "A reserva deve ter entre 1 e 10 pessoas.",
            "erro"
        );

        numPessoa.focus();
        return;
    }

    /* =========================
       VALIDAR MESA
    ========================= */

    if(mesaValue === ""){

        mostrarMensagem(
            "Selecione uma mesa.",
            "erro"
        );

        mesa.focus();
        return;
    }

    /* =========================
       BOTÃO LOADING
    ========================= */

    const btnEnviar =
    document.querySelector(".btn-enviar");

    btnEnviar.innerHTML =
    "Processando...";

    btnEnviar.disabled = true;

    /* =========================
       SIMULAÇÃO ENVIO
    ========================= */

    setTimeout(() => {

        mostrarMensagem(
            "Reserva enviada com sucesso!",
            "sucesso"
        );

        formReserva.submit();

    },1500);

});

/* =========================
   MENSAGEM PERSONALIZADA
========================= */

function mostrarMensagem(texto, tipo){

    /* REMOVER ANTIGA */
    const antiga =
    document.querySelector(".mensagem-alerta");

    if(antiga){
        antiga.remove();
    }

    /* NOVA DIV */
    const mensagem =
    document.createElement("div");

    mensagem.classList.add(
        "mensagem-alerta"
    );

    mensagem.innerText = texto;

    /* ESTILO */
    mensagem.style.position = "fixed";
    mensagem.style.top = "20px";
    mensagem.style.right = "20px";
    mensagem.style.padding = "15px 20px";
    mensagem.style.borderRadius = "10px";
    mensagem.style.color = "white";
    mensagem.style.fontWeight = "600";
    mensagem.style.zIndex = "99999";
    mensagem.style.boxShadow =
    "0 5px 20px rgba(0,0,0,0.2)";
    mensagem.style.animation =
    "aparecer 0.4s ease";

    if(tipo === "sucesso"){

        mensagem.style.background =
        "#16a34a";

    }else{

        mensagem.style.background =
        "#dc2626";
    }

    document.body.appendChild(mensagem);

    /* REMOVER */
    setTimeout(() => {

        mensagem.remove();

    },3000);
}

/* =========================
   ANIMAÇÃO ALERTA
========================= */

const style =
document.createElement("style");

style.innerHTML = `
@keyframes aparecer{

    from{
        opacity:0;
        transform:translateY(-20px);
    }

    to{
        opacity:1;
        transform:translateY(0);
    }
}
`;

document.head.appendChild(style);