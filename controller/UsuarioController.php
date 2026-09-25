<?php

require_once __DIR__ . '/../dao/UsuarioDAO.php';

class UsuarioController
{
    private $usuarioDAO;

    public function __construct()
    {
        $this->usuarioDAO = new UsuarioDAO();
    }

    public function login($email, $senha)
    {
        $usuario = $this->usuarioDAO->buscarPorEmail($email);

        if ($usuario === false) {
            return false;
        }

        if (!password_verify($senha, $usuario['senha'])) {
            return false;
        }

        return $usuario;
    }

    public function cadastrar($dados)
    {
        $dados['senha'] = password_hash(
            $dados['senha'],
            PASSWORD_DEFAULT
        );

        return $this->usuarioDAO->cadastrar($dados);
    }
}
