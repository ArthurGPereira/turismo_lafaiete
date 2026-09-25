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

    public function cadastrar($dados)
    {
        $sql = "
            INSERT INTO usuario
            (
                nome,
                email,
                senha,
                tipo
            )
            VALUES
            (
                :nome,
                :email,
                :senha,
                :tipo
            )
        ";

        $stmt = $this->conexao->prepare($sql);

        $stmt->bindValue(':nome', $dados['nome']);
        $stmt->bindValue(':email', $dados['email']);
        $stmt->bindValue(':senha', $dados['senha']);
        $stmt->bindValue(':tipo', $dados['tipo']);

        return $stmt->execute();
    }
}
