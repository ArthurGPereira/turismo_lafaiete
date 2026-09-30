<?php

require_once __DIR__ . '/../config/Conexao.php';
require_once __DIR__ . '/../model/Foto.php';

class FotoDAO
{
    private $conexao;

    public function __construct()
    {
        $this->conexao = Conexao::conectar();
    }

    public function listarPorLocal($id_local)
    {
        $sql = "
            SELECT *
            FROM foto
            WHERE id_local = :id_local
            ORDER BY id_foto DESC
        ";

        $stmt = $this->conexao->prepare($sql);

        $stmt->bindValue(
            ':id_local',
            $id_local,
            PDO::PARAM_INT
        );

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function buscarPorId($id)
    {
        $sql = "
            SELECT *
            FROM foto
            WHERE id_foto = :id_foto
        ";

        $stmt = $this->conexao->prepare($sql);

        $stmt->bindValue(
            ':id_foto',
            $id,
            PDO::PARAM_INT
        );

        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function cadastrar($dados)
    {
        $sql = "
            INSERT INTO foto
            (
                id_local,
                caminho,
                descricao
            )
            VALUES
            (
                :id_local,
                :caminho,
                :descricao
            )
        ";

        $stmt = $this->conexao->prepare($sql);

        $stmt->bindValue(
            ':id_local',
            $dados['id_local'],
            PDO::PARAM_INT
        );

        $stmt->bindValue(
            ':caminho',
            $dados['caminho']
        );

        $stmt->bindValue(
            ':descricao',
            $dados['descricao']
        );

        return $stmt->execute();
    }

    public function excluir($id)
    {
        $sql = "
            DELETE FROM foto
            WHERE id_foto = :id_foto
        ";

        $stmt = $this->conexao->prepare($sql);

        $stmt->bindValue(
            ':id_foto',
            $id,
            PDO::PARAM_INT
        );
        return $stmt->execute();
    }
}
?>
