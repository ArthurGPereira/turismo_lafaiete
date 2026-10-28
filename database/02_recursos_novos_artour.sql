-- ArTour | Estruturas adicionais para futuras funcionalidades
-- Execute SOMENTE depois de fazer backup do banco turismo_lafaiete.
-- Este arquivo é aditivo: não apaga nem altera tabelas já existentes.
-- As relações com tabelas antigas ficam por IDs para evitar assumir nomes/tipos
-- de colunas que podem variar na versão local do banco.

USE turismo_lafaiete;

CREATE TABLE IF NOT EXISTS empresa (
    id_empresa INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT UNSIGNED NOT NULL,
    nome_fantasia VARCHAR(150) NOT NULL,
    razao_social VARCHAR(180) NULL,
    documento VARCHAR(30) NULL,
    descricao TEXT NULL,
    telefone VARCHAR(30) NULL,
    email_contato VARCHAR(180) NULL,
    status ENUM('pendente','ativa','suspensa','recusada') NOT NULL DEFAULT 'pendente',
    autorizado_editar_regras TINYINT(1) NOT NULL DEFAULT 0,
    criado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    atualizado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uq_empresa_usuario (id_usuario),
    KEY idx_empresa_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS evento (
    id_evento INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_cidade INT UNSIGNED NOT NULL,
    id_usuario_criador INT UNSIGNED NULL,
    titulo VARCHAR(180) NOT NULL,
    descricao TEXT NULL,
    local_evento VARCHAR(180) NULL,
    endereco VARCHAR(255) NULL,
    data_inicio DATETIME NOT NULL,
    data_fim DATETIME NULL,
    imagem VARCHAR(255) NULL,
    status ENUM('rascunho','publicado','cancelado') NOT NULL DEFAULT 'rascunho',
    criado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    KEY idx_evento_cidade_data (id_cidade, data_inicio),
    KEY idx_evento_status_data (status, data_inicio)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS cupom (
    id_cupom INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_empresa INT UNSIGNED NOT NULL,
    id_local INT UNSIGNED NULL,
    id_cidade INT UNSIGNED NOT NULL,
    titulo VARCHAR(160) NOT NULL,
    descricao TEXT NULL,
    codigo VARCHAR(60) NOT NULL,
    tipo_desconto ENUM('percentual','valor_fixo','oferta') NOT NULL DEFAULT 'oferta',
    valor_desconto DECIMAL(10,2) NULL,
    regras TEXT NULL,
    limite_total INT UNSIGNED NULL,
    limite_por_usuario INT UNSIGNED NOT NULL DEFAULT 1,
    total_resgates INT UNSIGNED NOT NULL DEFAULT 0,
    inicio_validade DATETIME NOT NULL,
    fim_validade DATETIME NOT NULL,
    status ENUM('rascunho','pendente','ativo','pausado','encerrado','recusado') NOT NULL DEFAULT 'pendente',
    aprovado_por INT UNSIGNED NULL,
    aprovado_em DATETIME NULL,
    criado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    atualizado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uq_cupom_codigo (codigo),
    KEY idx_cupom_cidade_status_validade (id_cidade, status, inicio_validade, fim_validade),
    KEY idx_cupom_empresa (id_empresa)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS cupom_resgate (
    id_resgate BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_cupom INT UNSIGNED NOT NULL,
    id_usuario INT UNSIGNED NOT NULL,
    codigo_resgate VARCHAR(80) NOT NULL,
    status ENUM('reservado','utilizado','cancelado','expirado') NOT NULL DEFAULT 'reservado',
    reservado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    utilizado_em DATETIME NULL,
    confirmado_por INT UNSIGNED NULL,
    observacao VARCHAR(255) NULL,
    UNIQUE KEY uq_resgate_codigo (codigo_resgate),
    KEY idx_resgate_usuario_status (id_usuario, status),
    KEY idx_resgate_cupom_status (id_cupom, status),
    KEY idx_resgate_data (reservado_em)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS favorito (
    id_favorito BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT UNSIGNED NOT NULL,
    id_local INT UNSIGNED NOT NULL,
    criado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_favorito_usuario_local (id_usuario, id_local),
    KEY idx_favorito_local (id_local)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS avaliacao (
    id_avaliacao BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT UNSIGNED NOT NULL,
    id_local INT UNSIGNED NOT NULL,
    nota TINYINT UNSIGNED NOT NULL,
    comentario TEXT NULL,
    status ENUM('publicada','oculta') NOT NULL DEFAULT 'publicada',
    criado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    atualizado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uq_avaliacao_usuario_local (id_usuario, id_local),
    KEY idx_avaliacao_local_status (id_local, status),
    CONSTRAINT chk_avaliacao_nota CHECK (nota BETWEEN 1 AND 5)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS configuracao_integracao (
    id_config INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    provedor VARCHAR(40) NOT NULL,
    ativo TINYINT(1) NOT NULL DEFAULT 0,
    modelo VARCHAR(120) NULL,
    observacoes VARCHAR(255) NULL,
    atualizado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uq_config_provedor (provedor)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO configuracao_integracao (provedor, ativo, modelo, observacoes)
VALUES ('groq', 0, 'llama-3.3-70b-versatile', 'Configure a chave em variável de ambiente no servidor; nunca grave a chave da API nesta tabela.')
ON DUPLICATE KEY UPDATE provedor = VALUES(provedor);

-- IMPORTANTE:
-- 1) Confirme se o banco já possui tabelas chamadas favorito e avaliacao.
--    Se já existirem, CREATE TABLE IF NOT EXISTS não as modifica; confira suas colunas.
-- 2) O código da aplicação deve validar permissões, datas e limites no servidor.
-- 3) Para marcar cupom como utilizado, faça confirmação transacional e valide
--    validade/status/limite antes de atualizar total_resgates.
