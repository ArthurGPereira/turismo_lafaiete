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

require_once '../../../controller/CidadeController.php';

if (!isset($_GET['id'])) {
    header('Location: index.php');
    exit;
}

$id = (int) $_GET['id'];

$cidadeController = new CidadeController();

try {

    $cidadeController->excluir($id);

    header('Location: index.php');
    exit;

} catch (PDOException $e) {

    echo '<h2>Não foi possível excluir a cidade.</h2>';

    echo '<p>Essa cidade está sendo usada por um ou mais locais cadastrados.</p>';

    echo '<a href="index.php">Voltar para cidades</a>';
}
