<?php

$conn = new mysqli("localhost", "root", "", "restaurante");

if ($conn->connect_error) {
    die("Erro de conexão: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");

/* =====================================
   BUSCAR CATEGORIAS
===================================== */

$categorias = $conn->query("
    SELECT DISTINCT categoria
    FROM pratos
    ORDER BY categoria ASC
");

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
                    src="uploads/<?php echo htmlspecialchars($row['imagem']); ?>"
                    class="item-image">

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