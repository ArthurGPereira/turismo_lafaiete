<?php

require_once __DIR__ . '/../config/Conexao.php';
require_once __DIR__ . '/../model/Cidade.php';

class CidadeDAO
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
            FROM cidade
            WHERE status = 'ativo'
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
            FROM cidade
            WHERE id_cidade = :id_cidade
        ";

        $stmt = $this->conexao->prepare($sql);

        $stmt->bindValue(
            ':id_cidade',
            $id,
            PDO::PARAM_INT
        );

        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function cadastrar($dados)
    {
        $sql = "
            INSERT INTO cidade
            (
                nome,
                estado,
                descricao,
                historia,
                status
            )
            VALUES
            (
                :nome,
                :estado,
                :descricao,
                :historia,
                :status
            )
        ";

        $stmt = $this->conexao->prepare($sql);

        $stmt->bindValue(':nome', $dados['nome']);
        $stmt->bindValue(':estado', $dados['estado']);
        $stmt->bindValue(':descricao', $dados['descricao']);
        $stmt->bindValue(':historia', $dados['historia']);
        $stmt->bindValue(
            ':status',
            $dados['status'] ?? 'ativo'
        );

        return $stmt->execute();
    }

    public function editar($dados)
    {
        $sql = "
            UPDATE cidade
            SET
                nome = :nome,
                estado = :estado,
                descricao = :descricao,
                historia = :historia,
                status = :status
            WHERE id_cidade = :id_cidade
        ";

        $stmt = $this->conexao->prepare($sql);

        $stmt->bindValue(':nome', $dados['nome']);
        $stmt->bindValue(':estado', $dados['estado']);
        $stmt->bindValue(':descricao', $dados['descricao']);
        $stmt->bindValue(':historia', $dados['historia']);
        $stmt->bindValue(
            ':status',
            $dados['status']
        );

        $stmt->bindValue(
            ':id_cidade',
            $dados['id_cidade'],
            PDO::PARAM_INT
        );

        return $stmt->execute();
    }

    public function excluir($id)
    {
        $sql = "
            DELETE FROM cidade
            WHERE id_cidade = :id_cidade
        ";

        $stmt = $this->conexao->prepare($sql);

        $stmt->bindValue(
            ':id_cidade',
            $id,
            PDO::PARAM_INT
        );

        return $stmt->execute();
    }
}
?>
