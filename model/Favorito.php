<?php

class Favorito
{
    private $id_favorito;
    private $id_usuario;
    private $id_local;
    private $criado_em;

    public function getIdFavorito()
    {
        return $this->id_favorito;
    }

    public function setIdFavorito($id_favorito)
    {
        $this->id_favorito = $id_favorito;
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

    public function getCriadoEm()
    {
        return $this->criado_em;
    }

    public function setCriadoEm($criado_em)
    {
        $this->criado_em = $criado_em;
    }
}
