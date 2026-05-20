
// TROCAR SEÇÕES

function showSection(sec) {
    document.getElementById("dashboard").classList.add("hidden");
    document.getElementById("reservas").classList.add("hidden");
    document.getElementById("cardapio").classList.add("hidden");

    document.getElementById(sec).classList.remove("hidden");
}

// LOGOUT
function logout() {
    window.location.href = ".../pages/gerente_login.php";
}

// 🔥 CARREGAR RESERVAS DO PHP
function loadReservas() {
    fetch("buscar_reservas.php")
        .then(res => res.json())
        .then(data => {
            const lista = document.getElementById("listaReservas");
            lista.innerHTML = "";

            data.forEach(r => {
                lista.innerHTML += `
            <tr>
                <td>${r.nome}</td>
                <td>${r.data_reserva}</td>
                <td>${r.num_pessoa}</td>
            </tr>`;
            });

            document.getElementById("totalReservas").innerText = data.length;
        });
}

// 🔥 ADICIONAR PRATO COM IMAGEM
function addPrato() {
    const nome = document.getElementById("nomePrato").value;
    const preco = document.getElementById("precoPrato").value;
    const imagem = document.getElementById("imagemPrato").files[0];

    if (nome === "" || preco === "" || !imagem) {
        alert("Preencha todos campos!");
        return;
    }

    let formData = new FormData();
    formData.append("nome", nome);
    formData.append("preco", preco);
    formData.append("imagem", imagem);

    fetch("add_prato.php", {
        method: "POST",
        body: formData
    })
        .then(res => res.text())
        .then(data => {
            if (data === "success") {
                alert("Prato adicionado!");
                loadPratos();
            } else {
                alert("Erro!");
            }
        });
}

// 🔥 CARREGAR PRATOS DO BANCO
function loadPratos() {
    fetch("listar_pratos.php")
        .then(res => res.json())
        .then(data => {
            const lista = document.getElementById("listaPratos");
            lista.innerHTML = "";

            data.forEach(p => {
                lista.innerHTML += `
            <tr>
                <td>${p.nome}</td>
                <td>${p.preco} Kz</td>
                <td>
                    <img src="uploads/${p.imagem}" width="60">
                </td>
            </tr>`;
            });

            document.getElementById("totalPratos").innerText = data.length;
        });
}

fetch('listar_pratos.php')
    .then(res => res.text())
    .then(data => {
        document.getElementById("pratos-reserva").innerHTML = data;
    });

// INIT
loadReservas();
loadPratos();