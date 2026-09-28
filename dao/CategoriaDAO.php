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
        $sql = "
            SELECT *
            FROM categoria
            ORDER BY nome
        ";

        $stmt = $this->conexao->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function buscarPorId($id)
    {
        $sql = "
            SELECT *
            FROM categoria
            WHERE id_categoria = :id_categoria
        ";

        $stmt = $this->conexao->prepare($sql);

        $stmt->bindValue(
            ':id_categoria',
            $id,
            PDO::PARAM_INT
        );

        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function cadastrar($dados)
    {
        $sql = "
            INSERT INTO categoria
            (
                nome,
                descricao
            )
            VALUES
            (
                :nome,
                :descricao
            )
        ";

        $stmt = $this->conexao->prepare($sql);

        $stmt->bindValue(':nome', $dados['nome']);
        $stmt->bindValue(':descricao', $dados['descricao']);

        return $stmt->execute();
    }

    public function editar($dados)
    {
        $sql = "
            UPDATE categoria
            SET
                nome = :nome,
                descricao = :descricao
            WHERE id_categoria = :id_categoria
        ";

        $stmt = $this->conexao->prepare($sql);

        $stmt->bindValue(':nome', $dados['nome']);
        $stmt->bindValue(':descricao', $dados['descricao']);

        $stmt->bindValue(
            ':id_categoria',
            $dados['id_categoria'],
            PDO::PARAM_INT
        );

        return $stmt->execute();
    }

    public function excluir($id)
    {
        $sql = "
            DELETE FROM categoria
            WHERE id_categoria = :id_categoria
        ";

        $stmt = $this->conexao->prepare($sql);

        $stmt->bindValue(
            ':id_categoria',
            $id,
            PDO::PARAM_INT
        );

        return $stmt->execute();
    }
}
?>
