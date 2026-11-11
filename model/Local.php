<?php

class Local
{
    private $id_local;
    private $id_empresa;
    private $id_cidade;
    private $id_categoria;
    private $nome;
    private $descricao;
    private $endereco;
    private $telefone;
    private $email;
    private $horario_funcionamento;
    private $preco;
    private $latitude;
    private $longitude;
    private $cnpj;
    private $status;

    public function getIdLocal()
    {
        return $this->id_local;
    }

    public function setIdLocal($id_local)
    {
        $this->id_local = $id_local;
    }

    public function getIdEmpresa()
    {
        return $this->id_empresa;
    }

    public function setIdEmpresa($id_empresa)
    {
        $this->id_empresa = $id_empresa;
    }

    public function getIdCidade()
    {
        return $this->id_cidade;
    }

    public function setIdCidade($id_cidade)
    {
        $this->id_cidade = $id_cidade;
    }

    public function getIdCategoria()
    {
        return $this->id_categoria;
    }

    public function setIdCategoria($id_categoria)
    {
        $this->id_categoria = $id_categoria;
    }

    public function getNome()
    {
        return $this->nome;
    }

    public function setNome($nome)
    {
        $this->nome = $nome;
    }

    public function getDescricao()
    {
        return $this->descricao;
    }

    public function setDescricao($descricao)
    {
        $this->descricao = $descricao;
    }

    public function getEndereco()
    {
        return $this->endereco;
    }

    public function setEndereco($endereco)
    {
        $this->endereco = $endereco;
    }

    public function getTelefone()
    {
        return $this->telefone;
    }

    public function setTelefone($telefone)
    {
        $this->telefone = $telefone;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function setEmail($email)
    {
        $this->email = $email;
    }

    public function getHorarioFuncionamento()
    {
        return $this->horario_funcionamento;
    }

    public function setHorarioFuncionamento($horario_funcionamento)
    {
        $this->horario_funcionamento = $horario_funcionamento;
    }

    public function getPreco()
    {
        return $this->preco;
    }

    public function setPreco($preco)
    {
        $this->preco = $preco;
    }

    public function getLatitude()
    {
        return $this->latitude;
    }

    public function setLatitude($latitude)
    {
        $this->latitude = $latitude;
    }

    public function getLongitude()
    {
        return $this->longitude;
    }

    public function setLongitude($longitude)
    {
        $this->longitude = $longitude;
    }

    public function getCnpj()
    {
        return $this->cnpj;
    }

    public function setCnpj($cnpj)
    {
        if ($cnpj === null || trim((string) $cnpj) === '') {
            $this->cnpj = null;
            return;
        }

        $cnpj = preg_replace('/\D+/', '', (string) $cnpj);
        $this->cnpj = $cnpj !== '' ? $cnpj : null;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function setStatus($status)
    {
        $this->status = $status;
    }
}
