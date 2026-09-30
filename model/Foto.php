<?php

class Foto
{
    private $id_foto;
    private $id_local;
    private $caminho;
    private $descricao;

    public function getIdFoto()
    {
        return $this->id_foto;
    }

    public function setIdFoto($id_foto)
    {
        $this->id_foto = $id_foto;
    }

    public function getIdLocal()
    {
        return $this->id_local;
    }

    public function setIdLocal($id_local)
    {
        $this->id_local = $id_local;
    }

    public function getCaminho()
    {
        return $this->caminho;
    }

    public function setCaminho($caminho)
    {
        $this->caminho = $caminho;
    }

    public function getDescricao()
    {
        return $this->descricao;
    }

    public function setDescricao($descricao)
    {
        $this->descricao = $descricao;
    }
}

?>
