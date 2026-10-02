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

    /*
     * Lista todas as fotos de um determinado local
     */
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


    /*
     * Busca uma foto pelo ID
     */
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


    /*
     * Cadastra uma nova foto
     */
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


    /*
     * Exclui uma foto do banco e
     * também remove o arquivo físico
     */
    public function excluir($id)
    {
        // Primeiro busca o caminho da imagem
        $sqlBusca = "
            SELECT caminho
            FROM foto
            WHERE id_foto = :id_foto
        ";

        $stmtBusca = $this->conexao->prepare($sqlBusca);

        $stmtBusca->bindValue(
            ':id_foto',
            $id,
            PDO::PARAM_INT
        );

        $stmtBusca->execute();

        $foto = $stmtBusca->fetch(PDO::FETCH_ASSOC);


        // Foto não encontrada
        if (!$foto) {
            return false;
        }


        // Exclui o registro da tabela foto
        $sqlExcluir = "
            DELETE FROM foto
            WHERE id_foto = :id_foto
        ";

        $stmtExcluir = $this->conexao->prepare($sqlExcluir);

        $stmtExcluir->bindValue(
            ':id_foto',
            $id,
            PDO::PARAM_INT
        );


        // Se não conseguiu excluir do banco
        if (!$stmtExcluir->execute()) {
            return false;
        }


        // Caminho físico do arquivo
        $arquivo = __DIR__ . '/../' . $foto['caminho'];


        // Remove o arquivo da pasta uploads
        if (file_exists($arquivo)) {
            unlink($arquivo);
        }


        return true;
    }
}

?>
