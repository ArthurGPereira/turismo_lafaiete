<?php

require_once __DIR__ . '/../dao/CategoriaDAO.php';

class CategoriaController
{
    private $categoriaDAO;

    public function __construct()
    {
        $this->categoriaDAO = new CategoriaDAO();
    }

    public function listar()
    {
        return $this->categoriaDAO->listar();
    }

    public function buscarPorId($id)
    {
        return $this->categoriaDAO->buscarPorId($id);
    }

    public function cadastrar($dados)
    {
        return $this->categoriaDAO->cadastrar($dados);
    }

    public function editar($dados)
    {
        return $this->categoriaDAO->editar($dados);
    }

    public function excluir($id)
    {
        return $this->categoriaDAO->excluir($id);
    }
}

?>
