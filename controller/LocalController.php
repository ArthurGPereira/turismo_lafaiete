<?php

require_once __DIR__ . '/../dao/LocalDAO.php';

class LocalController
{
    private $localDAO;

    public function __construct()
    {
        $this->localDAO = new LocalDAO();
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

    private function validarCnpj(?string $cnpj): bool
    {
        if ($cnpj === null || trim((string) $cnpj) === '') {
            return true;
        }

        $cnpj = preg_replace('/\D+/', '', (string) $cnpj);

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

    public function cadastrar($dados)
    {
        return $this->localDAO->inserir($dados);
    }

    public function listar()
    {
        return $this->localDAO->listar();
    }

    public function buscarPorId($id)
    {
        return $this->localDAO->buscarPorId($id);
    }

    public function editar($dados)
    {
        return $this->localDAO->editar($dados);
    }

    public function excluir($id)
    {
        return $this->localDAO->excluir($id);
    }

    public function listarPorEmpresa($idEmpresa)
    {
        $idEmpresa = (int) $idEmpresa;

        if ($idEmpresa <= 0) {
            return [];
        }

        return $this->localDAO->listarPorEmpresa($idEmpresa);
    }

    public function buscarPorIdEmpresa($idLocal, $idEmpresa)
    {
        $idLocal = (int) $idLocal;
        $idEmpresa = (int) $idEmpresa;

        if ($idLocal <= 0 || $idEmpresa <= 0) {
            return null;
        }

        return $this->localDAO->buscarPorIdEmpresa($idLocal, $idEmpresa);
    }

    public function cadastrarParaEmpresa(array $dados, ?int $idEmpresaAutenticado = null)
    {
        $idEmpresa = (int) ($idEmpresaAutenticado ?? 0);

        if ($idEmpresa <= 0) {
            return false;
        }

        unset($dados['id_empresa']);
        $dados['id_empresa'] = $idEmpresa;
        $dados['cnpj'] = $this->normalizarCnpj($dados['cnpj'] ?? null);

        if (isset($dados['cnpj']) && !$this->validarCnpj($dados['cnpj'])) {
            throw new InvalidArgumentException('Informe um CNPJ de estabelecimento válido.');
        }

        try {
            return $this->localDAO->inserirComEmpresa($dados);
        } catch (RuntimeException $e) {
            throw $e;
        }
    }

    public function editarPorEmpresa(array $dados, ?int $idEmpresaAutenticado = null)
    {
        $idEmpresa = (int) ($idEmpresaAutenticado ?? 0);

        if ($idEmpresa <= 0) {
            return false;
        }

        unset($dados['id_empresa']);
        $dados['id_empresa'] = $idEmpresa;
        $dados['cnpj'] = $this->normalizarCnpj($dados['cnpj'] ?? null);

        if (isset($dados['cnpj']) && !$this->validarCnpj($dados['cnpj'])) {
            throw new InvalidArgumentException('Informe um CNPJ de estabelecimento válido.');
        }

        $localAtual = $this->localDAO->buscarPorIdEmpresa((int) ($dados['id_local'] ?? 0), $idEmpresa);

        if (!$localAtual) {
            return false;
        }

        try {
            return $this->localDAO->editarPorEmpresa($dados);
        } catch (RuntimeException $e) {
            throw $e;
        }
    }

    public function excluirPorEmpresa($idLocal, $idEmpresaAutenticado)
    {
        $idLocal = (int) $idLocal;
        $idEmpresa = (int) $idEmpresaAutenticado;

        if ($idLocal <= 0 || $idEmpresa <= 0) {
            return false;
        }

        $localAtual = $this->localDAO->buscarPorIdEmpresa($idLocal, $idEmpresa);

        if (!$localAtual) {
            return false;
        }

        return $this->localDAO->excluirPorEmpresa($idLocal, $idEmpresa);
    }
}
?>
