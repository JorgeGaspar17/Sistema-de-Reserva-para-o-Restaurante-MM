let buffer = "";
const segredo = "mm";

document.addEventListener("keydown", function (e) {

    if (e.target.tagName === "INPUT" || e.target.tagName === "TEXTAREA") {
        return;
    }

    buffer += e.key.toLowerCase();

    if (buffer.includes(segredo)) {
        window.location.href = "gerente_login.php";
    }

    setTimeout(() => {
        buffer = "";
    }, 1500);

});