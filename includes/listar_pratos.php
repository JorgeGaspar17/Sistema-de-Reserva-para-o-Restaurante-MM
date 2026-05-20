<?php

require_once __DIR__ . '/../config/conexao.php';

header('Content-Type: text/html; charset=utf-8');

$uploadUrl = dirname(dirname($_SERVER['SCRIPT_NAME'])) . '/uploads/';

/* =====================================
   BUSCAR CATEGORIAS
===================================== */

$categoriasStmt = $conn->prepare(
    "SELECT DISTINCT categoria FROM pratos ORDER BY categoria ASC"
);
$categoriasStmt->execute();
$categorias = $categoriasStmt->get_result();

/* =====================================
   MOSTRAR CADA CATEGORIA
===================================== */

if($categorias->num_rows > 0){

    while($cat = $categorias->fetch_assoc()){

        $categoria = $cat['categoria'];

        ?>

        <section class="categoria-section">

            <div class="categoria-topo">

                <h2>
                    <?php echo htmlspecialchars($categoria); ?>
                </h2>

            </div>

            <div class="categoria-grid">

        <?php

        /* =====================================
           BUSCAR PRATOS DA CATEGORIA
        ===================================== */

        $stmt = $conn->prepare("
            SELECT *
            FROM pratos
            WHERE categoria=?
            ORDER BY id DESC
        ");

        $stmt->bind_param("s", $categoria);

        $stmt->execute();

        $pratos = $stmt->get_result();

        while($row = $pratos->fetch_assoc()){

            ?>

            <div class="menu-item">

                <div class="imagem-box">

                    <img
                    src="<?= htmlspecialchars($uploadUrl . $row['imagem'], ENT_QUOTES, 'UTF-8') ?>"
                    class="item-image"
                    alt="<?= htmlspecialchars($row['nome'], ENT_QUOTES, 'UTF-8') ?>"
                    onerror="this.onerror=null;this.src='data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'320\' height=\'220\' viewBox=\'0 0 320 220\'%3E%3Crect width=\'320\' height=\'220\' fill=\'%23f3f3f3\'/%3E%3Ctext x=\'50%25\' y=\'50%25\' dominant-baseline=\'middle\' text-anchor=\'middle\' fill=\'%23666\' font-size=\'18\' font-family=\'Arial, sans-serif\'%3EImagem indisponível%3C/text%3E%3C/svg%3E';">

                </div>

                <div class="item-details">

                    <h3>
                        <?php echo htmlspecialchars($row['nome']); ?>
                    </h3>

                    <p>
                        <?php echo htmlspecialchars($row['descricao']); ?>
                    </p>

                    <span class="item-price">

                        <?php
                        echo number_format(
                            $row['preco']
                        );
                        ?>

                        Kz

                    </span>

                </div>

            </div>

            <?php
        }

        ?>

            </div>

        </section>

        <?php
    }

}else{

    echo "

    <div class='sem-pratos'>

        <h2>Nenhum prato cadastrado ainda.</h2>

    </div>

    ";
}
?>