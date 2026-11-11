<?php

class Empresa
{
    private $id_empresa;
    private $id_usuario;
    private $nome_fantasia;
    private $razao_social;
    private $documento;
    private $descricao;
    private $telefone;
    private $email_contato;
    private $status;
    private $autorizado_editar_regras;
    private $criado_em;
    private $atualizado_em;

    public function getIdEmpresa()
    {
        return $this->id_empresa;
    }

    public function setIdEmpresa($id_empresa)
    {
        $this->id_empresa = $id_empresa;
    }

    public function getIdUsuario()
    {
        return $this->id_usuario;
    }

    public function setIdUsuario($id_usuario)
    {
        $this->id_usuario = $id_usuario;
    }

    public function getNomeFantasia()
    {
        return $this->nome_fantasia;
    }

    public function setNomeFantasia($nome_fantasia)
    {
        $this->nome_fantasia = $nome_fantasia;
    }

    public function getRazaoSocial()
    {
        return $this->razao_social;
    }

    public function setRazaoSocial($razao_social)
    {
        $this->razao_social = $razao_social;
    }

    public function getDocumento()
    {
        return $this->documento;
    }

    public function setDocumento($documento)
    {
        $this->documento = $documento;
    }

    public function getDescricao()
    {
        return $this->descricao;
    }

    public function setDescricao($descricao)
    {
        $this->descricao = $descricao;
    }

    public function getTelefone()
    {
        return $this->telefone;
    }

    public function setTelefone($telefone)
    {
        $this->telefone = $telefone;
    }

    public function getEmailContato()
    {
        return $this->email_contato;
    }

    public function setEmailContato($email_contato)
    {
        $this->email_contato = $email_contato;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function setStatus($status)
    {
        $this->status = $status;
    }

    public function getAutorizadoEditarRegras()
    {
        return $this->autorizado_editar_regras;
    }

    public function setAutorizadoEditarRegras($autorizado_editar_regras)
    {
        $this->autorizado_editar_regras = $autorizado_editar_regras;
    }

    public function getCriadoEm()
    {
        return $this->criado_em;
    }

    public function setCriadoEm($criado_em)
    {
        $this->criado_em = $criado_em;
    }

    public function getAtualizadoEm()
    {
        return $this->atualizado_em;
    }

    public function setAtualizadoEm($atualizado_em)
    {
        $this->atualizado_em = $atualizado_em;
    }
}
