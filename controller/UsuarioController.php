<?php

require_once __DIR__ . '/../dao/UsuarioDAO.php';
require_once __DIR__ . '/../dao/EmpresaDAO.php';

class UsuarioController
{
    private $usuarioDAO;
    private $empresaDAO;

    public function __construct()
    {
        $this->usuarioDAO = new UsuarioDAO();
        $this->empresaDAO = new EmpresaDAO();
    }

    private function normalizarStatusEmpresa($status)
    {
        $status = strtolower(trim((string) $status));

        $mapeamento = [
            'aprovada' => 'ativa',
            'reprovada' => 'recusada',
            'bloqueada' => 'suspensa',
        ];

        return $mapeamento[$status] ?? $status;
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

        if (($usuario['tipo'] ?? '') === 'empresa') {
            $empresa = $this->empresaDAO->buscarPorIdUsuario((int) $usuario['id_usuario']);

            if ($empresa === false || $empresa === null) {
                return [
                    'mensagem' => 'Seu acesso não está habilitado.',
                    'tipo' => 'empresa_sem_registro',
                ];
            }

            $status = $this->normalizarStatusEmpresa($empresa['status'] ?? '');

            switch ($status) {
                case 'ativa':
                    return [
                        'usuario' => $usuario,
                        'empresa' => $empresa,
                        'tipo' => 'empresa_ativa',
                    ];
                case 'pendente':
                    return [
                        'mensagem' => 'Sua empresa ainda está aguardando aprovação.',
                        'tipo' => 'empresa_pendente',
                    ];
                case 'suspensa':
                    return [
                        'mensagem' => 'O acesso da sua empresa está suspenso.',
                        'tipo' => 'empresa_suspensa',
                    ];
                case 'recusada':
                    return [
                        'mensagem' => 'O cadastro da sua empresa foi recusado.',
                        'tipo' => 'empresa_recusada',
                    ];
                default:
                    return [
                        'mensagem' => 'Seu acesso não está habilitado.',
                        'tipo' => 'empresa_sem_registro',
                    ];
            }
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
