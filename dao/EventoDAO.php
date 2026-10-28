
<?php

require_once __DIR__ . '/../config/Conexao.php';
require_once __DIR__ . '/../model/Evento.php';

class EventoDAO
{
    private $conexao;

    public function __construct()
    {
        $this->conexao = Conexao::conectar();
    }

    public function cadastrar(Evento $evento)
    {
        $sql = "INSERT INTO evento
            (titulo, descricao, local_evento, endereco,
             data_inicio, data_fim, imagem, status)
            VALUES
            (:titulo, :descricao, :local_evento, :endereco,
             :data_inicio, :data_fim, :imagem, :status)";

        $stmt = $this->conexao->prepare($sql);

        return $stmt->execute([
            ':titulo' => $evento->getTitulo(),
            ':descricao' => $evento->getDescricao(),
            ':local_evento' => $evento->getLocalEvento(),
            ':endereco' => $evento->getEndereco(),
            ':data_inicio' => $evento->getDataInicio(),
            ':data_fim' => $evento->getDataFim(),
            ':imagem' => $evento->getImagem(),
            ':status' => $evento->getStatus()
        ]);
    }

    public function listarAtivos()
    {
        $sql = "SELECT * FROM evento
                WHERE status = 'ativo'
                ORDER BY data_inicio ASC";

        return $this->conexao->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function listarTodos()
    {
        $sql = "SELECT * FROM evento ORDER BY data_inicio DESC";

        return $this->conexao->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }
    
public function buscarPorId($idEvento)
{
    $sql = "SELECT * FROM evento
            WHERE id_evento = :id
            LIMIT 1";

    $stmt = $this->conexao->prepare($sql);
    $stmt->execute([':id' => $idEvento]);

    return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
}

}
