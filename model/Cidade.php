<?php

class Cidade
{
    private $id_cidade;
    private $nome;
    private $estado;
    private $descricao;
    private $historia;
    private $status;

    public function getIdCidade()
    {
        return $this->id_cidade;
    }

    public function setIdCidade($id_cidade)
    {
        $this->id_cidade = $id_cidade;
    }

    public function getNome()
    {
        return $this->nome;
    }

    public function setNome($nome)
    {
        $this->nome = $nome;
    }

    public function getEstado()
    {
        return $this->estado;
    }

    public function setEstado($estado)
    {
        $this->estado = $estado;
    }

    public function getDescricao()
    {
        return $this->descricao;
    }

    public function setDescricao($descricao)
    {
        $this->descricao = $descricao;
    }

    public function getHistoria()
    {
        return $this->historia;
    }

    public function setHistoria($historia)
    {
        $this->historia = $historia;
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
?>
