<?php

require_once __DIR__ . '/../dao/FotoDAO.php';

class FotoController
{
    private $fotoDAO;

    public function __construct()
    {
        $this->fotoDAO = new FotoDAO();
    }

    public function listarPorLocal($id_local)
    {
        return $this->fotoDAO->listarPorLocal($id_local);
    }

    public function buscarPorId($id)
    {
        return $this->fotoDAO->buscarPorId($id);
    }

    public function cadastrar($dados)
    {
        return $this->fotoDAO->cadastrar($dados);
    }

    public function excluir($id)
    {
        return $this->fotoDAO->excluir($id);
    }
}
?>
