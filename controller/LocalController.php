<?php

require_once __DIR__ . '/../dao/LocalDAO.php';

class LocalController
{
    private $localDAO;

    public function __construct()
    {
        $this->localDAO = new LocalDAO();
    }

    public function cadastrar($dados)
    {
        return $this->localDAO->inserir($dados);
    }

    public function listar()
    {
        return $this->localDAO->listar();
    }

    public function editar($dados)
    {
        return $this->localDAO->editar($dados);
    }

    public function excluir($id)
    {
        return $this->localDAO->excluir($id);
    }
}
?>
