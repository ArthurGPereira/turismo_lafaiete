<?php

session_start();

if (!isset($_SESSION['id_usuario'])) {
    header('Location: ../../login.php');
    exit;
}

if ($_SESSION['tipo_usuario'] !== 'administrador') {
    header('Location: ../../../index.php');
    exit;
}

require_once '../../../controller/CategoriaController.php';

if (!isset($_GET['id'])) {
    header('Location: index.php');
    exit;
}

$id = $_GET['id'];

$categoriaController = new CategoriaController();

try {

    $categoriaController->excluir($id);

    header('Location: index.php');
    exit;

} catch (PDOException $e) {

    echo '<h2>Não foi possível excluir a categoria.</h2>';

    echo '<p>';
    echo 'Essa categoria está sendo usada por um ou mais locais.';
    echo '</p>';

    echo '<a href="index.php">Voltar para categorias</a>';
}
?>
