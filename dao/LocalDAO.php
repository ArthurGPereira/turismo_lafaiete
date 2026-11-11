<?php

require_once __DIR__ . '/../config/Conexao.php';
require_once __DIR__ . '/../model/Local.php';

class LocalDAO
{
    private $conexao;

    public function __construct()
    {
        $this->conexao = Conexao::conectar();
    }

    private function normalizarCnpj(?string $cnpj): ?string
    {
        if ($cnpj === null) {
            return null;
        }

        $cnpj = trim((string) $cnpj);

        if ($cnpj === '') {
            return null;
        }

        $cnpj = preg_replace('/\D+/', '', $cnpj);

        return $cnpj !== '' ? $cnpj : null;
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

    public function listar()
    {
        $sql = "
            SELECT
                l.*,
                c.nome AS cidade,
                c.estado AS estado,
                cat.nome AS categoria,
                (
                    SELECT f.caminho
                    FROM foto f
                    WHERE f.id_local = l.id_local
                    ORDER BY f.id_foto ASC
                    LIMIT 1
                ) AS foto
            FROM `local` l
            INNER JOIN cidade c
                ON l.id_cidade = c.id_cidade
            INNER JOIN categoria cat
                ON l.id_categoria = cat.id_categoria
            ORDER BY l.nome
        ";

        $stmt = $this->conexao->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function buscarPorId($id)
    {
        $sql = "
            SELECT
                l.*,
                c.nome AS cidade,
                c.estado AS estado,
                cat.nome AS categoria
            FROM `local` l
            INNER JOIN cidade c
                ON l.id_cidade = c.id_cidade
            INNER JOIN categoria cat
                ON l.id_categoria = cat.id_categoria
            WHERE l.id_local = :id_local
            LIMIT 1
        ";

        $stmt = $this->conexao->prepare($sql);
        $stmt->bindValue(':id_local', $id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function listarPorEmpresa($idEmpresa)
    {
        $sql = "
            SELECT
                l.*,
                c.nome AS cidade,
                c.estado AS estado,
                cat.nome AS categoria,
                (
                    SELECT f.caminho
                    FROM foto f
                    WHERE f.id_local = l.id_local
                    ORDER BY f.id_foto ASC
                    LIMIT 1
                ) AS foto
            FROM `local` l
            INNER JOIN cidade c
                ON l.id_cidade = c.id_cidade
            INNER JOIN categoria cat
                ON l.id_categoria = cat.id_categoria
            WHERE l.id_empresa = :id_empresa
            ORDER BY l.nome
        ";

        $stmt = $this->conexao->prepare($sql);
        $stmt->bindValue(':id_empresa', $idEmpresa, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function buscarPorIdEmpresa($idLocal, $idEmpresa)
    {
        $sql = "
            SELECT
                l.*,
                c.nome AS cidade,
                c.estado AS estado,
                cat.nome AS categoria
            FROM `local` l
            INNER JOIN cidade c
                ON l.id_cidade = c.id_cidade
            INNER JOIN categoria cat
                ON l.id_categoria = cat.id_categoria
            WHERE l.id_local = :id_local
              AND l.id_empresa = :id_empresa
            LIMIT 1
        ";

        $stmt = $this->conexao->prepare($sql);
        $stmt->bindValue(':id_local', $idLocal, PDO::PARAM_INT);
        $stmt->bindValue(':id_empresa', $idEmpresa, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function buscarPorCnpj($cnpj, $idEmpresa = null)
    {
        $cnpjNormalizado = $this->normalizarCnpj((string) $cnpj);

        if ($cnpjNormalizado === null) {
            return null;
        }

        $sql = "
            SELECT *
            FROM `local`
            WHERE cnpj = :cnpj
        ";

        if ($idEmpresa !== null) {
            $sql .= " AND id_empresa = :id_empresa";
        }

        $sql .= " LIMIT 1";

        $stmt = $this->conexao->prepare($sql);
        $stmt->bindValue(':cnpj', $cnpjNormalizado);

        if ($idEmpresa !== null) {
            $stmt->bindValue(':id_empresa', (int) $idEmpresa, PDO::PARAM_INT);
        }

        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function inserirComEmpresa($local)
    {
        $local['id_empresa'] = (int) ($local['id_empresa'] ?? 0);
        $local['cnpj'] = $this->normalizarCnpj($local['cnpj'] ?? null);

        if ($local['id_empresa'] <= 0) {
            return false;
        }

        if (isset($local['cnpj']) && !$this->validarCnpj($local['cnpj'])) {
            throw new InvalidArgumentException('Informe um CNPJ de estabelecimento válido.');
        }

        if (isset($local['cnpj']) && $this->buscarPorCnpj($local['cnpj'], $local['id_empresa'])) {
            throw new RuntimeException('Já existe um estabelecimento com este CNPJ para esta empresa.');
        }

        $sql = "
            INSERT INTO `local`
            (
                id_empresa,
                id_cidade,
                id_categoria,
                nome,
                descricao,
                endereco,
                telefone,
                email,
                horario_funcionamento,
                preco,
                latitude,
                longitude,
                cnpj,
                status
            )
            VALUES
            (
                :id_empresa,
                :id_cidade,
                :id_categoria,
                :nome,
                :descricao,
                :endereco,
                :telefone,
                :email,
                :horario_funcionamento,
                :preco,
                :latitude,
                :longitude,
                :cnpj,
                :status
            )
        ";

        $stmt = $this->conexao->prepare($sql);

        $stmt->bindValue(':id_empresa', $local['id_empresa'], PDO::PARAM_INT);
        $stmt->bindValue(':id_cidade', $local['id_cidade'], PDO::PARAM_INT);
        $stmt->bindValue(':id_categoria', $local['id_categoria'], PDO::PARAM_INT);
        $stmt->bindValue(':nome', $local['nome']);
        $stmt->bindValue(':descricao', $local['descricao']);
        $stmt->bindValue(':endereco', $local['endereco']);
        $stmt->bindValue(':telefone', $local['telefone']);
        $stmt->bindValue(':email', $local['email']);
        $stmt->bindValue(':horario_funcionamento', $local['horario_funcionamento']);
        $stmt->bindValue(':preco', $local['preco']);
        $stmt->bindValue(':latitude', $local['latitude']);
        $stmt->bindValue(':longitude', $local['longitude']);
        $stmt->bindValue(':cnpj', $local['cnpj'] ?? null);
        $stmt->bindValue(':status', $local['status']);

        try {
            if ($stmt->execute()) {
                return $this->conexao->lastInsertId();
            }
        } catch (PDOException $e) {
            if (strpos((string) $e->getMessage(), 'uq_local_cnpj') !== false) {
                throw new RuntimeException('Já existe um estabelecimento com este CNPJ cadastrado.');
            }

            throw $e;
        }

        return false;
    }

    public function editarPorEmpresa($local)
    {
        $local['id_empresa'] = (int) ($local['id_empresa'] ?? 0);
        $local['id_local'] = (int) ($local['id_local'] ?? 0);
        $local['cnpj'] = $this->normalizarCnpj($local['cnpj'] ?? null);

        if ($local['id_empresa'] <= 0 || $local['id_local'] <= 0) {
            return false;
        }

        if (isset($local['cnpj']) && !$this->validarCnpj($local['cnpj'])) {
            throw new InvalidArgumentException('Informe um CNPJ de estabelecimento válido.');
        }

        if (isset($local['cnpj']) && $this->buscarPorCnpj($local['cnpj'], $local['id_empresa'])) {
            $localExistente = $this->buscarPorIdEmpresa($local['id_local'], $local['id_empresa']);
            if (!$localExistente || ($localExistente['cnpj'] ?? null) !== $local['cnpj']) {
                throw new RuntimeException('Já existe um estabelecimento com este CNPJ para esta empresa.');
            }
        }

        $sql = "
            UPDATE `local`
            SET
                id_cidade = :id_cidade,
                id_categoria = :id_categoria,
                nome = :nome,
                descricao = :descricao,
                endereco = :endereco,
                telefone = :telefone,
                email = :email,
                horario_funcionamento = :horario_funcionamento,
                preco = :preco,
                latitude = :latitude,
                longitude = :longitude,
                cnpj = :cnpj,
                status = :status
            WHERE id_local = :id_local
              AND id_empresa = :id_empresa
        ";

        $stmt = $this->conexao->prepare($sql);

        $stmt->bindValue(':id_cidade', $local['id_cidade'], PDO::PARAM_INT);
        $stmt->bindValue(':id_categoria', $local['id_categoria'], PDO::PARAM_INT);
        $stmt->bindValue(':nome', $local['nome']);
        $stmt->bindValue(':descricao', $local['descricao']);
        $stmt->bindValue(':endereco', $local['endereco']);
        $stmt->bindValue(':telefone', $local['telefone']);
        $stmt->bindValue(':email', $local['email']);
        $stmt->bindValue(':horario_funcionamento', $local['horario_funcionamento']);
        $stmt->bindValue(':preco', $local['preco']);
        $stmt->bindValue(':latitude', $local['latitude']);
        $stmt->bindValue(':longitude', $local['longitude']);
        $stmt->bindValue(':cnpj', $local['cnpj'] ?? null);
        $stmt->bindValue(':status', $local['status']);
        $stmt->bindValue(':id_local', $local['id_local'], PDO::PARAM_INT);
        $stmt->bindValue(':id_empresa', $local['id_empresa'], PDO::PARAM_INT);

        try {
            return $stmt->execute();
        } catch (PDOException $e) {
            if (strpos((string) $e->getMessage(), 'uq_local_cnpj') !== false) {
                throw new RuntimeException('Já existe um estabelecimento com este CNPJ cadastrado.');
            }

            throw $e;
        }
    }

    public function excluirPorEmpresa($idLocal, $idEmpresa)
    {
        $sql = "
            DELETE FROM `local`
            WHERE id_local = :id_local
              AND id_empresa = :id_empresa
        ";

        $stmt = $this->conexao->prepare($sql);
        $stmt->bindValue(':id_local', $idLocal, PDO::PARAM_INT);
        $stmt->bindValue(':id_empresa', $idEmpresa, PDO::PARAM_INT);

        return $stmt->execute();
    }

    public function inserir($local)
    {
        $sql = "
            INSERT INTO `local`
            (
                id_empresa,
                id_cidade,
                id_categoria,
                nome,
                descricao,
                endereco,
                telefone,
                email,
                horario_funcionamento,
                preco,
                latitude,
                longitude,
                cnpj,
                status
            )
            VALUES
            (
                :id_empresa,
                :id_cidade,
                :id_categoria,
                :nome,
                :descricao,
                :endereco,
                :telefone,
                :email,
                :horario_funcionamento,
                :preco,
                :latitude,
                :longitude,
                :cnpj,
                :status
            )
        ";

        $stmt = $this->conexao->prepare($sql);

        $stmt->bindValue(':id_empresa', $local['id_empresa'] ?? null, PDO::PARAM_INT);
        $stmt->bindValue(':id_cidade', $local['id_cidade'], PDO::PARAM_INT);
        $stmt->bindValue(':id_categoria', $local['id_categoria'], PDO::PARAM_INT);
        $stmt->bindValue(':nome', $local['nome']);
        $stmt->bindValue(':descricao', $local['descricao']);
        $stmt->bindValue(':endereco', $local['endereco']);
        $stmt->bindValue(':telefone', $local['telefone']);
        $stmt->bindValue(':email', $local['email']);
        $stmt->bindValue(':horario_funcionamento', $local['horario_funcionamento']);
        $stmt->bindValue(':preco', $local['preco']);
        $stmt->bindValue(':latitude', $local['latitude']);
        $stmt->bindValue(':longitude', $local['longitude']);
        $stmt->bindValue(':cnpj', $this->normalizarCnpj($local['cnpj'] ?? null));
        $stmt->bindValue(':status', $local['status']);

        if ($stmt->execute()) {
            return $this->conexao->lastInsertId();
        }

        return false;
    }

    public function editar($local)
    {
        $sql = "
            UPDATE `local`
            SET
                id_empresa = :id_empresa,
                id_cidade = :id_cidade,
                id_categoria = :id_categoria,
                nome = :nome,
                descricao = :descricao,
                endereco = :endereco,
                telefone = :telefone,
                email = :email,
                horario_funcionamento = :horario_funcionamento,
                preco = :preco,
                latitude = :latitude,
                longitude = :longitude,
                cnpj = :cnpj,
                status = :status
            WHERE id_local = :id_local
        ";

        $stmt = $this->conexao->prepare($sql);

        $stmt->bindValue(':id_empresa', $local['id_empresa'] ?? null, PDO::PARAM_INT);
        $stmt->bindValue(':id_cidade', $local['id_cidade'], PDO::PARAM_INT);
        $stmt->bindValue(':id_categoria', $local['id_categoria'], PDO::PARAM_INT);
        $stmt->bindValue(':nome', $local['nome']);
        $stmt->bindValue(':descricao', $local['descricao']);
        $stmt->bindValue(':endereco', $local['endereco']);
        $stmt->bindValue(':telefone', $local['telefone']);
        $stmt->bindValue(':email', $local['email']);
        $stmt->bindValue(':horario_funcionamento', $local['horario_funcionamento']);
        $stmt->bindValue(':preco', $local['preco']);
        $stmt->bindValue(':latitude', $local['latitude']);
        $stmt->bindValue(':longitude', $local['longitude']);
        $stmt->bindValue(':cnpj', $this->normalizarCnpj($local['cnpj'] ?? null));
        $stmt->bindValue(':status', $local['status']);
        $stmt->bindValue(':id_local', $local['id_local'], PDO::PARAM_INT);

        return $stmt->execute();
    }

    public function excluir($id)
    {
        $sql = "
            DELETE FROM `local`
            WHERE id_local = :id_local
        ";

        $stmt = $this->conexao->prepare($sql);
        $stmt->bindValue(':id_local', $id, PDO::PARAM_INT);

        return $stmt->execute();
    }
}
?>
