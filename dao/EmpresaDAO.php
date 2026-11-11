<?php

require_once __DIR__ . '/../config/Conexao.php';
require_once __DIR__ . '/../model/Empresa.php';

class EmpresaDAO
{
    private $conexao;

    public function __construct()
    {
        $this->conexao = Conexao::conectar();
    }

    public function setConexao(PDO $conexao): void
    {
        $this->conexao = $conexao;
    }

    public function getConexao(): PDO
    {
        return $this->conexao;
    }

    public function buscarPorIdUsuario($idUsuario)
    {
        $sql = "
            SELECT *
            FROM empresa
            WHERE id_usuario = :id_usuario
            LIMIT 1
        ";

        $stmt = $this->conexao->prepare($sql);
        $stmt->bindValue(':id_usuario', $idUsuario, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function buscarPorDocumento($documento)
    {
        $sql = "
            SELECT *
            FROM empresa
            WHERE cnpj = :documento
            LIMIT 1
        ";

        $stmt = $this->conexao->prepare($sql);
        $stmt->bindValue(':documento', $documento);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function listar()
    {
        $sql = "
            SELECT
                e.*,
                u.nome AS responsavel_nome,
                u.email AS responsavel_email
            FROM empresa e
            LEFT JOIN usuario u ON u.id_usuario = e.id_usuario
            ORDER BY e.nome_fantasia ASC
        ";

        $stmt = $this->conexao->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function listarPorStatus($status)
    {
        $sql = "
            SELECT
                e.*,
                u.nome AS responsavel_nome,
                u.email AS responsavel_email
            FROM empresa e
            LEFT JOIN usuario u ON u.id_usuario = e.id_usuario
            WHERE e.status = :status
            ORDER BY e.nome_fantasia ASC
        ";

        $stmt = $this->conexao->prepare($sql);
        $stmt->bindValue(':status', $status);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function buscarPorId($idEmpresa)
    {
        $sql = "
            SELECT
                e.*,
                u.nome AS responsavel_nome,
                u.email AS responsavel_email
            FROM empresa e
            LEFT JOIN usuario u ON u.id_usuario = e.id_usuario
            WHERE e.id_empresa = :id_empresa
            LIMIT 1
        ";

        $stmt = $this->conexao->prepare($sql);
        $stmt->bindValue(':id_empresa', $idEmpresa, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function cadastrar($dados)
    {
        $sql = "
            INSERT INTO empresa (
                id_usuario,
                nome_fantasia,
                razao_social,
                cnpj,
                descricao,
                telefone,
                email_contato,
                status
            ) VALUES (
                :id_usuario,
                :nome_fantasia,
                :razao_social,
                :documento,
                :descricao,
                :telefone,
                :email_contato,
                :status
            )
        ";

        $stmt = $this->conexao->prepare($sql);

        $stmt->bindValue(':id_usuario', $dados['id_usuario'], PDO::PARAM_INT);
        $stmt->bindValue(':nome_fantasia', $dados['nome_fantasia']);
        $stmt->bindValue(':razao_social', $dados['razao_social']);
        $stmt->bindValue(':documento', $dados['documento']);
        $stmt->bindValue(':descricao', $dados['descricao'] ?? null);
        $stmt->bindValue(':telefone', $dados['telefone'] ?? null);
        $stmt->bindValue(':email_contato', $dados['email_contato'] ?? null);
        $stmt->bindValue(':status', $dados['status'] ?? 'pendente');

        return $stmt->execute();
    }

    public function atualizarStatus($idEmpresa, $status)
    {
        $sql = "
            UPDATE empresa
            SET status = :status,
                atualizado_em = CURRENT_TIMESTAMP
            WHERE id_empresa = :id_empresa
        ";

        $stmt = $this->conexao->prepare($sql);
        $stmt->bindValue(':status', $status);
        $stmt->bindValue(':id_empresa', $idEmpresa, PDO::PARAM_INT);

        return $stmt->execute();
    }

    public function contarPorStatus($status)
    {
        $sql = "
            SELECT COUNT(*) AS total
            FROM empresa
            WHERE status = :status
        ";

        $stmt = $this->conexao->prepare($sql);
        $stmt->bindValue(':status', $status);
        $stmt->execute();

        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

        return (int) ($resultado['total'] ?? 0);
    }
}
