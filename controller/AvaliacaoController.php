<?php

require_once __DIR__ . '/../dao/AvaliacaoDAO.php';

class AvaliacaoController
{
    private $avaliacaoDAO;

    public function __construct()
    {
        $this->avaliacaoDAO = new AvaliacaoDAO();
    }

    public function avaliarLocal($dados)
    {
        $idUsuario = (int) ($dados['id_usuario'] ?? 0);
        $idLocal = (int) ($dados['id_local'] ?? 0);
        $nota = isset($dados['nota']) ? (int) $dados['nota'] : 0;
        $comentario = isset($dados['comentario']) ? trim((string) $dados['comentario']) : '';

        if ($idUsuario <= 0 || $idLocal <= 0) {
            return ['sucesso' => false, 'mensagem' => 'Usuário ou local inválido.'];
        }

        if ($nota < 1 || $nota > 5) {
            return ['sucesso' => false, 'mensagem' => 'A nota deve estar entre 1 e 5 estrelas.'];
        }

        if ($this->avaliacaoDAO->verificarAvaliacaoExistente($idUsuario, $idLocal)) {
            return ['sucesso' => false, 'mensagem' => 'Você já avaliou este local.'];
        }

        $dadosSalvar = [
            'id_usuario' => $idUsuario,
            'id_local' => $idLocal,
            'nota' => $nota,
            'comentario' => $comentario !== '' ? $comentario : null,
        ];

        try {
            $resultado = $this->avaliacaoDAO->inserir($dadosSalvar);

            if ($resultado) {
                return ['sucesso' => true, 'mensagem' => 'Avaliação enviada com sucesso!'];
            }

            return ['sucesso' => false, 'mensagem' => 'Não foi possível registrar a avaliação.'];
        } catch (Exception $e) {
            return ['sucesso' => false, 'mensagem' => 'Não foi possível registrar a avaliação. Tente novamente mais tarde.'];
        }
    }

    public function listarPorLocal($idLocal)
    {
        if (empty($idLocal)) {
            return [];
        }

        return $this->avaliacaoDAO->listarPorLocal($idLocal);
    }

    public function mediaPorLocal($idLocal)
    {
        if (empty($idLocal)) {
            return ['media' => 0, 'total' => 0];
        }

        $dados = $this->avaliacaoDAO->mediaPorLocal($idLocal);

        if (!$dados) {
            return ['media' => 0, 'total' => 0];
        }

        return [
            'media' => (float) ($dados['media'] ?? 0),
            'total' => (int) ($dados['total'] ?? 0),
        ];
    }
}
