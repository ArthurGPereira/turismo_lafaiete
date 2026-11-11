<?php

require_once __DIR__ . '/../config/Conexao.php';

class AvaliacaoDAO
{
    private $conexao;

    public function __construct()
    {
        $this->conexao = Conexao::conectar();
    }

    public function inserir($dados)
    {
        $sql = "
            INSERT INTO avaliacao (id_usuario, id_local, nota, comentario)
            VALUES (:id_usuario, :id_local, :nota, :comentario)
        ";

        $stmt = $this->conexao->prepare($sql);
        $stmt->bindValue(':id_usuario', (int) $dados['id_usuario'], PDO::PARAM_INT);
        $stmt->bindValue(':id_local', (int) $dados['id_local'], PDO::PARAM_INT);
        $stmt->bindValue(':nota', (int) $dados['nota'], PDO::PARAM_INT);
        $stmt->bindValue(':comentario', $dados['comentario'] ?? null, PDO::PARAM_STR);

        return $stmt->execute();
    }

    public function listarPorLocal($idLocal)
    {
        $sql = "
            SELECT
                a.id_avaliacao,
                a.id_usuario,
                a.id_local,
                a.nota,
                a.comentario,
                a.data_avaliacao,
                u.nome AS nome_usuario
            FROM avaliacao a
            INNER JOIN usuario u ON u.id_usuario = a.id_usuario
            WHERE a.id_local = :id_local
            ORDER BY a.data_avaliacao DESC
        ";

        $stmt = $this->conexao->prepare($sql);
        $stmt->bindValue(':id_local', (int) $idLocal, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function mediaPorLocal($idLocal)
    {
        $sql = "
            SELECT
                AVG(nota) AS media,
                COUNT(*) AS total
            FROM avaliacao
            WHERE id_local = :id_local
        ";

        $stmt = $this->conexao->prepare($sql);
        $stmt->bindValue(':id_local', (int) $idLocal, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function verificarAvaliacaoExistente($idUsuario, $idLocal)
    {
        $sql = "
            SELECT 1
            FROM avaliacao
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
