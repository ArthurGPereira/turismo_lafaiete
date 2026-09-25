<?php

require_once __DIR__ . '/../config/Conexao.php';
require_once __DIR__ . '/../model/Categoria.php';

class CategoriaDAO
{
    private $conexao;

    public function __construct()
    {
        $this->conexao = Conexao::conectar();
    }

    public function listar()
    {
        $sql = "SELECT * FROM categoria ORDER BY nome";

        $stmt = $this->conexao->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
