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

require_once '../../../controller/FotoController.php';

$fotoController = new FotoController();

$id_foto = $_GET['id'] ?? null;

if (!$id_foto) {
    header('Location: ../local/index.php');
    exit;
}

$foto = $fotoController->buscarPorId($id_foto);

if (!$foto) {
    header('Location: ../local/index.php');
    exit;
}

$id_local = $foto['id_local'];

$caminhoArquivo = '../../../' . $foto['caminho'];

if (file_exists($caminhoArquivo)) {
    unlink($caminhoArquivo);
}

$fotoController->excluir($id_foto);

header('Location: index.php?id_local=' . $id_local);
exit;
