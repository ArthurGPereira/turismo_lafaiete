<?php

require_once __DIR__ . '/../dao/EmpresaDAO.php';
require_once __DIR__ . '/../dao/UsuarioDAO.php';

class EmpresaController
{
    private $empresaDAO;
    private $usuarioDAO;

    public function __construct()
    {
        $this->empresaDAO = new EmpresaDAO();
        $this->usuarioDAO = new UsuarioDAO();
    }

    private function validarCnpj(string $cnpj): bool
    {
        $cnpj = preg_replace('/\D+/', '', $cnpj);

        if (strlen($cnpj) !== 14) {
            return false;
        }

        if (preg_match('/^(\d)\\1+$/', $cnpj)) {
            return false;
        }

        $soma = 0;
        $multiplicador = 5;

        for ($i = 0; $i < 12; $i++) {
            $soma += (int) $cnpj[$i] * $multiplicador;
            $multiplicador--;

            if ($multiplicador < 2) {
                $multiplicador = 9;
            }
        }

        $digito1 = $soma % 11;
        $digito1 = $digito1 < 2 ? 0 : 11 - $digito1;

        if ((int) $cnpj[12] !== $digito1) {
            return false;
        }

        $soma = 0;
        $multiplicador = 6;

        for ($i = 0; $i < 13; $i++) {
            $soma += (int) $cnpj[$i] * $multiplicador;
            $multiplicador--;

            if ($multiplicador < 2) {
                $multiplicador = 9;
            }
        }

        $digito2 = $soma % 11;
        $digito2 = $digito2 < 2 ? 0 : 11 - $digito2;

        return (int) $cnpj[13] === $digito2;
    }

    public function cadastrar(array $dados): bool
    {
        $nome = trim((string) ($dados['nome'] ?? ''));
        $email = trim((string) ($dados['email'] ?? ''));
        $senha = (string) ($dados['senha'] ?? '');
        $confirmarSenha = (string) ($dados['confirmar_senha'] ?? '');

        $nomeFantasia = trim((string) ($dados['nome_fantasia'] ?? ''));
        $razaoSocial = trim((string) ($dados['razao_social'] ?? ''));
        $documento = preg_replace('/\D+/', '', (string) ($dados['documento'] ?? ''));
        $descricao = trim((string) ($dados['descricao'] ?? ''));
        $telefone = preg_replace('/\D+/', '', (string) ($dados['telefone'] ?? ''));
        $emailContato = trim((string) ($dados['email_contato'] ?? ''));

        if ($nome === '' || $email === '' || $senha === '' || $confirmarSenha === '') {
            throw new InvalidArgumentException('Preencha todos os campos obrigatórios.');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException('Informe um e-mail válido para a conta da empresa.');
        }

        if ($senha !== $confirmarSenha) {
            throw new InvalidArgumentException('As senhas não coincidem.');
        }

        if (strlen($senha) < 6) {
            throw new InvalidArgumentException('A senha deve ter no mínimo 6 caracteres.');
        }

        if ($nomeFantasia === '') {
            throw new InvalidArgumentException('Informe o nome fantasia da empresa.');
        }

        if ($razaoSocial === '') {
            throw new InvalidArgumentException('Informe a razão social da empresa.');
        }

        if (!$this->validarCnpj($documento)) {
            throw new InvalidArgumentException('Informe um CNPJ válido com 14 dígitos.');
        }

        if ($emailContato !== '' && !filter_var($emailContato, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException('Informe um e-mail de contato válido.');
        }

        if ($this->usuarioDAO->buscarPorEmail($email)) {
            throw new InvalidArgumentException('Já existe uma conta com este e-mail.');
        }

        if ($this->empresaDAO->buscarPorDocumento($documento)) {
            throw new InvalidArgumentException('Já existe uma empresa cadastrada com este CNPJ.');
        }

        $senhaHash = password_hash($senha, PASSWORD_DEFAULT);

        $usuarioDados = [
            'nome' => $nome,
            'email' => $email,
            'senha' => $senhaHash,
            'tipo' => 'empresa',
            'imagem' => null,
        ];

        $conexao = Conexao::conectar();
        $this->usuarioDAO->setConexao($conexao);
        $this->empresaDAO->setConexao($conexao);

        $conexao->beginTransaction();

        try {
            $resultadoUsuario = $this->usuarioDAO->cadastrar($usuarioDados);

            if ($resultadoUsuario === false) {
                throw new RuntimeException('Erro ao cadastrar a conta da empresa.');
            }

            $usuario = $this->usuarioDAO->buscarPorEmail($email);

            if ($usuario === false) {
                throw new RuntimeException('Não foi possível recuperar a conta cadastrada.');
            }

            $empresaDados = [
                'id_usuario' => (int) $usuario['id_usuario'],
                'nome_fantasia' => $nomeFantasia,
                'razao_social' => $razaoSocial,
                'documento' => $documento,
                'descricao' => $descricao,
                'telefone' => $telefone !== '' ? $telefone : null,
                'email_contato' => $emailContato !== '' ? $emailContato : null,
                'status' => 'pendente',
                'autorizado_editar_regras' => 0,
            ];

            $resultadoEmpresa = $this->empresaDAO->cadastrar($empresaDados);

            if ($resultadoEmpresa === false) {
                throw new RuntimeException('Erro ao cadastrar os dados da empresa.');
            }

            $conexao->commit();
            return true;
        } catch (Throwable $e) {
            if ($conexao->inTransaction()) {
                $conexao->rollBack();
            }
            throw $e;
        }
    }

    public function listar($status = null)
    {
        if ($status !== null) {
            $status = strtolower(trim((string) $status));

            if (!in_array($status, ['pendente', 'ativa', 'suspensa', 'recusada'], true)) {
                return [];
            }

            return $this->empresaDAO->listarPorStatus($status);
        }

        return $this->empresaDAO->listar();
    }

    public function buscarPorId($idEmpresa)
    {
        $idEmpresa = (int) $idEmpresa;

        if ($idEmpresa <= 0) {
            return null;
        }

        return $this->empresaDAO->buscarPorId($idEmpresa);
    }

    public function validarTransicaoStatus($statusAtual, $statusDestino): bool
    {
        $statusAtual = strtolower(trim((string) $statusAtual));
        $statusDestino = strtolower(trim((string) $statusDestino));

        if ($statusAtual === '' || $statusDestino === '') {
            return false;
        }

        $transicoesPermitidas = [
            'pendente' => ['ativa', 'recusada'],
            'ativa' => ['suspensa'],
            'suspensa' => ['ativa'],
            'recusada' => ['ativa'],
        ];

        if (!isset($transicoesPermitidas[$statusAtual])) {
            return false;
        }

        return in_array($statusDestino, $transicoesPermitidas[$statusAtual], true);
    }

    public function alterarStatus($idEmpresa, $statusDestino)
    {
        $idEmpresa = (int) $idEmpresa;
        $statusDestino = strtolower(trim((string) $statusDestino));

        if ($idEmpresa <= 0) {
            throw new InvalidArgumentException('Identificador da empresa inválido.');
        }

        if (!in_array($statusDestino, ['pendente', 'ativa', 'suspensa', 'recusada'], true)) {
            throw new InvalidArgumentException('Status de destino inválido.');
        }

        $empresa = $this->empresaDAO->buscarPorId($idEmpresa);

        if (!$empresa) {
            throw new RuntimeException('Empresa não encontrada.');
        }

        $statusAtual = strtolower(trim((string) ($empresa['status'] ?? '')));

        if ($statusAtual === '') {
            throw new RuntimeException('Esta empresa não possui um status válido para alteração.');
        }

        if (!$this->validarTransicaoStatus($statusAtual, $statusDestino)) {
            throw new RuntimeException('Transição de status não permitida para esta empresa.');
        }

        $resultado = $this->empresaDAO->atualizarStatus($idEmpresa, $statusDestino);

        if ($resultado === false) {
            throw new RuntimeException('Não foi possível atualizar o status da empresa.');
        }

        return true;
    }

    public function obterResumoPorStatus(): array
    {
        $statusDisponiveis = ['pendente', 'ativa', 'suspensa', 'recusada'];
        $resumo = [];

        foreach ($statusDisponiveis as $status) {
            $resumo[$status] = $this->empresaDAO->contarPorStatus($status);
        }

        return $resumo;
    }
}
