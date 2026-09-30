<?php

require_once __DIR__ . '/../dao/CidadeDAO.php';

class CidadeController
{
    private $cidadeDAO;

    public function __construct()
    {
        $this->cidadeDAO = new CidadeDAO();
    }

    public function listar()
    {
        return $this->cidadeDAO->listar();
    }

    public function buscarPorId($id)
    {
        return $this->cidadeDAO->buscarPorId($id);
    }

    public function cadastrar($dados)
    {
        return $this->cidadeDAO->cadastrar($dados);
    }

    public function editar($dados)
    {
        return $this->cidadeDAO->editar($dados);
    }

    public function excluir($id)
    {
        return $this->cidadeDAO->excluir($id);
    }
}
?>
