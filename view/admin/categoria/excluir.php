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

$categoriaController = new CategoriaController();


/*
|--------------------------------------------------------------------------
| Verifica se o ID foi informado
|--------------------------------------------------------------------------
*/

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: index.php');
    exit;
}

$id = (int) $_GET['id'];


/*
|--------------------------------------------------------------------------
| Tenta excluir a categoria
|--------------------------------------------------------------------------
*/

try {

    $categoriaController->excluir($id);

    header('Location: index.php');
    exit;

} catch (PDOException $e) {

    /*
    |--------------------------------------------------------------------------
    | Categoria está sendo utilizada por algum local
    |--------------------------------------------------------------------------
    */

    echo '<!DOCTYPE html>';

    echo '<html lang="pt-br">';

    echo '<head>';

    echo '<meta charset="UTF-8">';

    echo '<meta name="viewport" content="width=device-width, initial-scale=1.0">';

    echo '<title>Erro - ArTour</title>';

    echo '</head>';

    echo '<body>';

    echo '<main>';

    echo '<h2>Não foi possível excluir a categoria.</h2>';

    echo '<p>';
    echo 'Essa categoria está sendo usada por um ou mais locais.';
    echo '</p>';

    echo '<a href="index.php">';
    echo 'Voltar para categorias';
    echo '</a>';

    echo '</main>';

    echo '</body>';

    echo '</html>';

}

?><?php

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

$categoriaController = new CategoriaController();


/*
|--------------------------------------------------------------------------
| Verifica se o ID foi informado
|--------------------------------------------------------------------------
*/

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: index.php');
    exit;
}

$id = (int) $_GET['id'];


/*
|--------------------------------------------------------------------------
| Tenta excluir a categoria
|--------------------------------------------------------------------------
*/

try {

    $categoriaController->excluir($id);

    header('Location: index.php');
    exit;

} catch (PDOException $e) {

    /*
    |--------------------------------------------------------------------------
    | Categoria está sendo utilizada por algum local
    |--------------------------------------------------------------------------
    */

    echo '<!DOCTYPE html>';

    echo '<html lang="pt-br">';

    echo '<head>';

    echo '<meta charset="UTF-8">';

    echo '<meta name="viewport" content="width=device-width, initial-scale=1.0">';

    echo '<title>Erro - ArTour</title>';

    echo '</head>';

    echo '<body>';

    echo '<main>';

    echo '<h2>Não foi possível excluir a categoria.</h2>';

    echo '<p>';
    echo 'Essa categoria está sendo usada por um ou mais locais.';
    echo '</p>';

    echo '<a href="index.php">';
    echo 'Voltar para categorias';
    echo '</a>';

    echo '</main>';

    echo '</body>';

    echo '</html>';

}

?>
