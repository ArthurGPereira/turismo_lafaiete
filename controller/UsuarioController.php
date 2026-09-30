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

        if (!isset($dados['imagem'])) {
            $dados['imagem'] = null;
        }

        return $this->usuarioDAO->cadastrar($dados);
    }

    public function buscarPorId($id)
    {
        return $this->usuarioDAO->buscarPorId($id);
    }

    public function editarPerfil($dados)
    {
        return $this->usuarioDAO->editarPerfil($dados);
    }
    public function listar()
    {
        return $this->usuarioDAO->listar();
    }

    public function editar($dados)
    {
        return $this->usuarioDAO->editar($dados);
    }

    public function excluir($id)
    {
        return $this->usuarioDAO->excluir($id);
    }
}
?>
