
<?php

class Evento
{
    private $idEvento;
    private $titulo;
    private $descricao;
    private $localEvento;
    private $endereco;
    private $dataInicio;
    private $dataFim;
    private $imagem;
    private $status;

    public function __construct(
        $titulo = '',
        $descricao = '',
        $localEvento = '',
        $endereco = '',
        $dataInicio = '',
        $dataFim = null,
        $imagem = null,
        $status = 'ativo',
        $idEvento = null
    ) {
        $this->idEvento = $idEvento;
        $this->titulo = $titulo;
        $this->descricao = $descricao;
        $this->localEvento = $localEvento;
        $this->endereco = $endereco;
        $this->dataInicio = $dataInicio;
        $this->dataFim = $dataFim ?: null;
        $this->imagem = $imagem ?: null;
        $this->status = $status;
    }

    public function getIdEvento() { return $this->idEvento; }
    public function getTitulo() { return $this->titulo; }
    public function getDescricao() { return $this->descricao; }
    public function getLocalEvento() { return $this->localEvento; }
    public function getEndereco() { return $this->endereco; }
    public function getDataInicio() { return $this->dataInicio; }
    public function getDataFim() { return $this->dataFim; }
    public function getImagem() { return $this->imagem; }
    public function getStatus() { return $this->status; }
}
