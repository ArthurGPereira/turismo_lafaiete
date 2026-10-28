
<?php

require_once __DIR__ . '/../dao/EventoDAO.php';
require_once __DIR__ . '/../model/Evento.php';

class EventoController
{
    private $eventoDAO;

    public function __construct()
    {
        $this->eventoDAO = new EventoDAO();
    }

    public function listar()
    {
        return $this->eventoDAO->listarAtivos();
    }

    public function listarTodos()
    {
        return $this->eventoDAO->listarTodos();
    }

    public function cadastrar(Evento $evento)
    {
        if (
            trim($evento->getTitulo()) === '' ||
            trim($evento->getDescricao()) === '' ||
            trim($evento->getLocalEvento()) === '' ||
            trim($evento->getEndereco()) === '' ||
            trim($evento->getDataInicio()) === ''
        ) {
            throw new InvalidArgumentException(
                'Preencha todos os campos obrigatórios.'
            );
        }

        if (!in_array($evento->getStatus(), ['ativo', 'inativo'], true)) {
            throw new InvalidArgumentException('Status inválido.');
        }

        $inicio = strtotime($evento->getDataInicio());

        $fim = $evento->getDataFim()
            ? strtotime($evento->getDataFim())
            : null;

        if ($inicio === false || ($fim !== null && $fim < $inicio)) {
            throw new InvalidArgumentException(
                'Confira as datas do evento.'
            );
        }

        return $this->eventoDAO->cadastrar($evento);
    }

    public function buscarPorId($idEvento)
    {
        if (
            !filter_var($idEvento, FILTER_VALIDATE_INT) ||
            $idEvento < 1
        ) {
            return null;
        }

        return $this->eventoDAO->buscarPorId($idEvento);
    }
}
