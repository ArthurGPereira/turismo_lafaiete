<?php

class Avaliacao
{
    private $id_avaliacao;
    private $id_usuario;
    private $id_local;
    private $nota;
    private $comentario;
    private $status;
    private $criado_em;
    private $atualizado_em;

    public function getIdAvaliacao()
    {
        return $this->id_avaliacao;
    }

    public function setIdAvaliacao($id_avaliacao)
    {
        $this->id_avaliacao = $id_avaliacao;
    }

    public function getIdUsuario()
    {
        return $this->id_usuario;
    }

    public function setIdUsuario($id_usuario)
    {
        $this->id_usuario = $id_usuario;
    }

    public function getIdLocal()
    {
        return $this->id_local;
    }

    public function setIdLocal($id_local)
    {
        $this->id_local = $id_local;
    }

    public function getNota()
    {
        return $this->nota;
    }

    public function setNota($nota)
    {
        $this->nota = $nota;
    }

    public function getComentario()
    {
        return $this->comentario;
    }

    public function setComentario($comentario)
    {
        $this->comentario = $comentario;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function setStatus($status)
    {
        $this->status = $status;
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
