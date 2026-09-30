<?php

require_once __DIR__ . '/../config/Conexao.php';
require_once __DIR__ . '/../model/Usuario.php';

class UsuarioDAO
{
    private $conexao;

    public function __construct()
    {
        $this->conexao = Conexao::conectar();
    }

    public function buscarPorEmail($email)
    {
        $sql = "
            SELECT *
            FROM usuario
            WHERE email = :email
            LIMIT 1
        ";

        $stmt = $this->conexao->prepare($sql);
        $stmt->bindValue(':email', $email);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function buscarPorId($id)
    {
        $sql = "
            SELECT *
            FROM usuario
            WHERE id_usuario = :id_usuario
            LIMIT 1
        ";

        $stmt = $this->conexao->prepare($sql);
        $stmt->bindValue(':id_usuario', $id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function listar()
    {
        $sql = "
            SELECT
                id_usuario,
                nome,
                email,
                tipo,
                imagem
            FROM usuario
            ORDER BY nome
        ";

        $stmt = $this->conexao->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function cadastrar($dados)
    {
        $sql = "
            INSERT INTO usuario
            (
                nome,
                email,
                senha,
                tipo,
                imagem
            )
            VALUES
            (
                :nome,
                :email,
                :senha,
                :tipo,
                :imagem
            )
        ";

        $stmt = $this->conexao->prepare($sql);

        $stmt->bindValue(':nome', $dados['nome']);
        $stmt->bindValue(':email', $dados['email']);
        $stmt->bindValue(':senha', $dados['senha']);
        $stmt->bindValue(':tipo', $dados['tipo']);
        $stmt->bindValue(':imagem', $dados['imagem'] ?? null);

        return $stmt->execute();
    }

    public function editarPerfil($dados)
    {
        $sql = "
            UPDATE usuario
            SET
                nome = :nome,
                email = :email,
                imagem = :imagem
            WHERE id_usuario = :id_usuario
        ";

        $stmt = $this->conexao->prepare($sql);

        $stmt->bindValue(':nome', $dados['nome']);
        $stmt->bindValue(':email', $dados['email']);
        $stmt->bindValue(':imagem', $dados['imagem']);
        $stmt->bindValue(
            ':id_usuario',
            $dados['id_usuario'],
            PDO::PARAM_INT
        );

        return $stmt->execute();
    }

    public function editar($dados)
    {
        $sql = "
            UPDATE usuario
            SET
                nome = :nome,
                email = :email,
                tipo = :tipo
            WHERE id_usuario = :id_usuario
        ";

        $stmt = $this->conexao->prepare($sql);

        $stmt->bindValue(':nome', $dados['nome']);
        $stmt->bindValue(':email', $dados['email']);
        $stmt->bindValue(':tipo', $dados['tipo']);
        $stmt->bindValue(
            ':id_usuario',
            $dados['id_usuario'],
            PDO::PARAM_INT
        );

        return $stmt->execute();
    }

    public function excluir($id)
    {
        $sql = "
            DELETE FROM usuario
            WHERE id_usuario = :id_usuario
        ";

        $stmt = $this->conexao->prepare($sql);

        $stmt->bindValue(
            ':id_usuario',
            $id,
            PDO::PARAM_INT
        );

        return $stmt->execute();
    }
}
?>
