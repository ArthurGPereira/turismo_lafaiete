<?php

require_once '../dao/CategoriaDAO.php';
require_once '../config/Conexao.php';

$categoriaDAO = new CategoriaDAO();
$categorias = $categoriaDAO->listar();

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>

    <meta charset="UTF-8">

    <title>Cadastrar Local - ArTour</title>

</head>

<body>

    <h1>ArTour</h1>

    <h2>Cadastrar Local</h2>

    <form action="../index.php" method="POST">

        <label>Nome:</label>
        <br>
        <input type="text" name="nome" required>

        <br><br>

        <label>Categoria:</label>
        <br>

        <select name="id_categoria" required>

            <option value="">Selecione</option>

            <?php foreach ($categorias as $categoria): ?>

                <option value="<?= $categoria['id_categoria'] ?>">
                    <?= htmlspecialchars($categoria['nome']) ?>
                </option>

            <?php endforeach; ?>

        </select>

        <br><br>

        <label>Descrição:</label>
        <br>
        <textarea name="descricao"></textarea>

        <br><br>

        <label>Endereço:</label>
        <br>
        <input type="text" name="endereco" required>

        <br><br>

        <label>Telefone:</label>
        <br>
        <input type="text" name="telefone">

        <br><br>

        <label>E-mail:</label>
        <br>
        <input type="email" name="email">

        <br><br>

        <label>Horário de funcionamento:</label>
        <br>
        <input type="text" name="horario_funcionamento">

        <br><br>

        <label>Preço:</label>
        <br>
        <input type="number" name="preco" step="0.01">

        <br><br>

        <button type="submit" name="cadastrar_local">
            Cadastrar
        </button>

    </form>

</body>

</html>
