<?php

require_once __DIR__ . '/../config/Conexao.php';
require_once __DIR__ . '/../model/Local.php';

class LocalDAO
{
    private $conexao;

    public function __construct()
    {
        $this->conexao = Conexao::conectar();
    }

    public function listar()
    {
        $sql = "
            SELECT
                l.*,
                c.nome AS cidade,
                cat.nome AS categoria,
                (
                    SELECT f.caminho
                    FROM foto f
                    WHERE f.id_local = l.id_local
                    ORDER BY f.id_foto ASC
                    LIMIT 1
                ) AS foto
            FROM `local` l
            INNER JOIN cidade c
                ON l.id_cidade = c.id_cidade
            INNER JOIN categoria cat
                ON l.id_categoria = cat.id_categoria
            ORDER BY l.nome
        ";

        $stmt = $this->conexao->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function inserir($local)
    {
        $sql = "
            INSERT INTO `local`
            (
                id_cidade,
                id_categoria,
                nome,
                descricao,
                endereco,
                telefone,
                email,
                horario_funcionamento,
                preco,
                latitude,
                longitude,
                status
            )
            VALUES
            (
                :id_cidade,
                :id_categoria,
                :nome,
                :descricao,
                :endereco,
                :telefone,
                :email,
                :horario_funcionamento,
                :preco,
                :latitude,
                :longitude,
                :status
            )
        ";

        $stmt = $this->conexao->prepare($sql);

        $stmt->bindValue(
            ':id_cidade',
            $local['id_cidade']
        );

        $stmt->bindValue(
            ':id_categoria',
            $local['id_categoria']
        );

        $stmt->bindValue(
            ':nome',
            $local['nome']
        );

        $stmt->bindValue(
            ':descricao',
            $local['descricao']
        );

        $stmt->bindValue(
            ':endereco',
            $local['endereco']
        );

        $stmt->bindValue(
            ':telefone',
            $local['telefone']
        );

        $stmt->bindValue(
            ':email',
            $local['email']
        );

        $stmt->bindValue(
            ':horario_funcionamento',
            $local['horario_funcionamento']
        );

        $stmt->bindValue(
            ':preco',
            $local['preco']
        );

        $stmt->bindValue(
            ':latitude',
            $local['latitude']
        );

        $stmt->bindValue(
            ':longitude',
            $local['longitude']
        );

        $stmt->bindValue(
            ':status',
            $local['status']
        );

        return $stmt->execute();
    }

    public function editar($local)
    {
        $sql = "
            UPDATE `local`
            SET
                id_cidade = :id_cidade,
                id_categoria = :id_categoria,
                nome = :nome,
                descricao = :descricao,
                endereco = :endereco,
                telefone = :telefone,
                email = :email,
                horario_funcionamento = :horario_funcionamento,
                preco = :preco
            WHERE id_local = :id_local
        ";

        $stmt = $this->conexao->prepare($sql);

        $stmt->bindValue(
            ':id_cidade',
            $local['id_cidade'],
            PDO::PARAM_INT
        );

        $stmt->bindValue(
            ':id_categoria',
            $local['id_categoria'],
            PDO::PARAM_INT
        );

        $stmt->bindValue(
            ':nome',
            $local['nome']
        );

        $stmt->bindValue(
            ':descricao',
            $local['descricao']
        );

        $stmt->bindValue(
            ':endereco',
            $local['endereco']
        );

        $stmt->bindValue(
            ':telefone',
            $local['telefone']
        );

        $stmt->bindValue(
            ':email',
            $local['email']
        );

        $stmt->bindValue(
            ':horario_funcionamento',
            $local['horario_funcionamento']
        );

        $stmt->bindValue(
            ':preco',
            $local['preco']
        );

        $stmt->bindValue(
            ':id_local',
            $local['id_local'],
            PDO::PARAM_INT
        );

        return $stmt->execute();
    }

    public function excluir($id)
    {
        $sql = "
            DELETE FROM `local`
            WHERE id_local = :id_local
        ";

        $stmt = $this->conexao->prepare($sql);

        $stmt->bindValue(
            ':id_local',
            $id
        );

        return $stmt->execute();
    }
}
?>
