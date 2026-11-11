<?php

require_once __DIR__ . '/../config/Conexao.php';

class FavoritoDAO
{
    private $conexao;

    public function __construct()
    {
        $this->conexao = Conexao::conectar();
    }

    public function adicionar($idUsuario, $idLocal)
    {
        $sql = "
            INSERT INTO favorito (id_usuario, id_local)
            VALUES (:id_usuario, :id_local)
        ";

        $stmt = $this->conexao->prepare($sql);
        $stmt->bindValue(':id_usuario', (int) $idUsuario, PDO::PARAM_INT);
        $stmt->bindValue(':id_local', (int) $idLocal, PDO::PARAM_INT);

        return $stmt->execute();
    }

    public function remover($idUsuario, $idLocal)
    {
        $sql = "
            DELETE FROM favorito
            WHERE id_usuario = :id_usuario
              AND id_local = :id_local
        ";

        $stmt = $this->conexao->prepare($sql);
        $stmt->bindValue(':id_usuario', (int) $idUsuario, PDO::PARAM_INT);
        $stmt->bindValue(':id_local', (int) $idLocal, PDO::PARAM_INT);

        return $stmt->execute();
    }

    public function listarPorUsuario($idUsuario)
    {
        $sql = "
            SELECT
                f.id_favorito,
                f.id_local,
                f.criado_em,
                l.nome AS nome_local,
                l.descricao,
                l.endereco,
                c.nome AS nome_cidade,
                c.estado,
                cat.nome AS nome_categoria,
                (
                    SELECT foto.caminho
                    FROM foto
                    WHERE foto.id_local = l.id_local
                    ORDER BY foto.id_foto ASC
                    LIMIT 1
                ) AS foto
            FROM favorito f
            INNER JOIN `local` l ON l.id_local = f.id_local
            INNER JOIN cidade c ON c.id_cidade = l.id_cidade
            INNER JOIN categoria cat ON cat.id_categoria = l.id_categoria
            WHERE f.id_usuario = :id_usuario
            ORDER BY f.criado_em DESC
        ";

        $stmt = $this->conexao->prepare($sql);
        $stmt->bindValue(':id_usuario', (int) $idUsuario, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function verificarFavorito($idUsuario, $idLocal)
    {
        $sql = "
            SELECT 1
            FROM favorito
            WHERE id_usuario = :id_usuario
              AND id_local = :id_local
            LIMIT 1
        ";

        $stmt = $this->conexao->prepare($sql);
        $stmt->bindValue(':id_usuario', (int) $idUsuario, PDO::PARAM_INT);
        $stmt->bindValue(':id_local', (int) $idLocal, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchColumn() !== false;
    }
}
