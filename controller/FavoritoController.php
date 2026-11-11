<?php

require_once __DIR__ . '/../dao/FavoritoDAO.php';

class FavoritoController
{
    private $favoritoDAO;

    public function __construct()
    {
        $this->favoritoDAO = new FavoritoDAO();
    }

    public function adicionar($idUsuario, $idLocal)
    {
        if (empty($idUsuario) || empty($idLocal)) {
            return false;
        }

        if ($this->favoritoDAO->verificarFavorito($idUsuario, $idLocal)) {
            return false;
        }

        return $this->favoritoDAO->adicionar($idUsuario, $idLocal);
    }

    public function remover($idUsuario, $idLocal)
    {
        if (empty($idUsuario) || empty($idLocal)) {
            return false;
        }

        return $this->favoritoDAO->remover($idUsuario, $idLocal);
    }

    public function listarPorUsuario($idUsuario)
    {
        if (empty($idUsuario)) {
            return [];
        }

        return $this->favoritoDAO->listarPorUsuario($idUsuario);
    }

    public function verificarFavorito($idUsuario, $idLocal)
    {
        if (empty($idUsuario) || empty($idLocal)) {
            return false;
        }

        return $this->favoritoDAO->verificarFavorito($idUsuario, $idLocal);
    }
}
