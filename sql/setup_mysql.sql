-- ============================================================
-- ReclamaMunicipal — MySQL DDL
-- Banco  : reclamamunicipal
-- Charset: utf8mb4 / utf8mb4_unicode_ci
-- Executar: mysql -u root reclamamunicipal < setup_mysql.sql
-- ============================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ------------------------------------------------------------
-- Limpeza (re-execução segura)
-- ------------------------------------------------------------
DROP TABLE IF EXISTS reclamacao_evidencia;
DROP TABLE IF EXISTS reclamacao_historico;
DROP TABLE IF EXISTS reclamacao_foto;
DROP TABLE IF EXISTS reclamacao;
DROP TABLE IF EXISTS blog;
DROP TABLE IF EXISTS contato;
DROP TABLE IF EXISTS categoria;
DROP TABLE IF EXISTS cidadao;

SET FOREIGN_KEY_CHECKS = 1;

-- ------------------------------------------------------------
-- 1. CIDADAO
-- ------------------------------------------------------------
CREATE TABLE cidadao (
    id            INT UNSIGNED  NOT NULL AUTO_INCREMENT,
    username      VARCHAR(50)   NOT NULL,
    email         VARCHAR(150)  NOT NULL,
    nome_completo VARCHAR(200)  NOT NULL,
    telefone      VARCHAR(20)   DEFAULT NULL,
    senha_hash    VARCHAR(255)  NOT NULL,
    auth_key      VARCHAR(100)  NOT NULL,
    access_token  VARCHAR(100)  DEFAULT NULL,
    permissao     TINYINT(1)    NOT NULL DEFAULT 1,
    ativo         TINYINT(1)    NOT NULL DEFAULT 1,
    created_at    DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at    DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_cidadao_username (username),
    UNIQUE KEY uq_cidadao_email    (email),
    KEY idx_cidadao_email    (email),
    KEY idx_cidadao_username (username)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- 2. CATEGORIA
-- ------------------------------------------------------------
CREATE TABLE categoria (
    id       INT UNSIGNED  NOT NULL AUTO_INCREMENT,
    slug     VARCHAR(50)   NOT NULL,
    nome     VARCHAR(100)  NOT NULL,
    icone_bi VARCHAR(80)   NOT NULL,
    ativo    TINYINT(1)    NOT NULL DEFAULT 1,
    PRIMARY KEY (id),
    UNIQUE KEY uq_categoria_slug (slug)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- 3. RECLAMACAO
-- ------------------------------------------------------------
CREATE TABLE reclamacao (
    id           INT UNSIGNED   NOT NULL AUTO_INCREMENT,
    protocolo    VARCHAR(20)    DEFAULT NULL,
    cidadao_id   INT UNSIGNED   NOT NULL,
    categoria_id INT UNSIGNED   NOT NULL,
    titulo       VARCHAR(200)   NOT NULL,
    descricao    LONGTEXT       NOT NULL,
    endereco     VARCHAR(300)   DEFAULT NULL,
    bairro       VARCHAR(100)   DEFAULT NULL,
    urgencia     VARCHAR(10)    NOT NULL DEFAULT 'baixa',
    status_rec   VARCHAR(15)    NOT NULL DEFAULT 'pendente',
    valor_gasto  DECIMAL(12,2)  DEFAULT NULL,
    created_at   DATETIME       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at   DATETIME       NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_reclamacao_protocolo (protocolo),
    KEY idx_reclamacao_cidadao   (cidadao_id),
    KEY idx_reclamacao_categoria (categoria_id),
    KEY idx_reclamacao_status    (status_rec),
    KEY idx_reclamacao_criado    (created_at),
    CONSTRAINT fk_reclamacao_cidadao   FOREIGN KEY (cidadao_id)   REFERENCES cidadao(id),
    CONSTRAINT fk_reclamacao_categoria FOREIGN KEY (categoria_id) REFERENCES categoria(id),
    CONSTRAINT ck_reclamacao_urgencia  CHECK (urgencia   IN ('baixa','media','alta')),
    CONSTRAINT ck_reclamacao_status    CHECK (status_rec IN ('pendente','andamento','resolvida','arquivada'))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- 4. RECLAMACAO_FOTO
-- ------------------------------------------------------------
CREATE TABLE reclamacao_foto (
    id             INT UNSIGNED  NOT NULL AUTO_INCREMENT,
    reclamacao_id  INT UNSIGNED  NOT NULL,
    caminho        VARCHAR(500)  NOT NULL,
    nome_original  VARCHAR(255)  NOT NULL,
    mime_type      VARCHAR(80)   NOT NULL DEFAULT 'image/jpeg',
    tamanho_bytes  INT UNSIGNED  NOT NULL DEFAULT 0,
    created_at     DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_foto_reclamacao (reclamacao_id),
    CONSTRAINT fk_foto_reclamacao FOREIGN KEY (reclamacao_id)
        REFERENCES reclamacao(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- 5. RECLAMACAO_HISTORICO
-- ------------------------------------------------------------
CREATE TABLE reclamacao_historico (
    id              INT UNSIGNED   NOT NULL AUTO_INCREMENT,
    reclamacao_id   INT UNSIGNED   NOT NULL,
    status_anterior VARCHAR(15)    DEFAULT NULL,
    status_novo     VARCHAR(15)    NOT NULL,
    observacao      VARCHAR(1000)  DEFAULT NULL,
    gestor          VARCHAR(100)   DEFAULT NULL,
    created_at      DATETIME       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_hist_reclamacao (reclamacao_id),
    CONSTRAINT fk_hist_reclamacao FOREIGN KEY (reclamacao_id)
        REFERENCES reclamacao(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- 6. RECLAMACAO_EVIDENCIA
-- ------------------------------------------------------------
CREATE TABLE reclamacao_evidencia (
    id            INT UNSIGNED  NOT NULL AUTO_INCREMENT,
    reclamacao_id INT UNSIGNED  NOT NULL,
    caminho       VARCHAR(500)  NOT NULL,
    descricao     VARCHAR(300)  DEFAULT NULL,
    created_at    DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_evidencia_rec (reclamacao_id),
    CONSTRAINT fk_evidencia_rec FOREIGN KEY (reclamacao_id)
        REFERENCES reclamacao(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- 7. CONTATO
-- ------------------------------------------------------------
CREATE TABLE contato (
    id           INT UNSIGNED  NOT NULL AUTO_INCREMENT,
    nome         VARCHAR(200)  NOT NULL,
    cargo        VARCHAR(150)  DEFAULT NULL,
    email        VARCHAR(150)  DEFAULT NULL,
    telefone     VARCHAR(30)   DEFAULT NULL,
    descricao    VARCHAR(500)  DEFAULT NULL,
    foto_caminho VARCHAR(500)  DEFAULT NULL,
    ativo        TINYINT(1)    NOT NULL DEFAULT 1,
    ordem        SMALLINT      NOT NULL DEFAULT 0,
    created_at   DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at   DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_contato_ativo_ordem (ativo, ordem)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- 8. BLOG
-- ------------------------------------------------------------
CREATE TABLE blog (
    id          INT UNSIGNED  NOT NULL AUTO_INCREMENT,
    titulo      VARCHAR(200)  NOT NULL,
    resumo      VARCHAR(500)  NOT NULL,
    conteudo    LONGTEXT      NOT NULL,
    foto_capa   VARCHAR(500)  NOT NULL,
    foto_banner VARCHAR(500)  NOT NULL,
    cidadao_id  INT UNSIGNED  NOT NULL,
    ativo       TINYINT(1)    NOT NULL DEFAULT 1,
    created_at  DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at  DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_blog_ativo_created (ativo, created_at),
    KEY idx_blog_cidadao       (cidadao_id),
    CONSTRAINT fk_blog_cidadao FOREIGN KEY (cidadao_id) REFERENCES cidadao(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- 9. DADOS INICIAIS — Categorias
-- ------------------------------------------------------------
INSERT INTO categoria (slug, nome, icone_bi) VALUES
    ('infraestrutura', 'Infraestrutura',   'bi-cone-striped'),
    ('saneamento',     'Saneamento',        'bi-droplet-fill'),
    ('saude',          'Saúde',             'bi-heart-pulse-fill'),
    ('educacao',       'Educação',          'bi-mortarboard-fill'),
    ('transporte',     'Transporte',        'bi-bus-front-fill'),
    ('seguranca',      'Segurança',         'bi-shield-fill-check'),
    ('meio-ambiente',  'Meio Ambiente',     'bi-tree-fill'),
    ('outros',         'Outros Serviços',   'bi-list-check');

-- ------------------------------------------------------------
-- 10. ADMIN INICIAL  (senha: Admin@2026)
-- ------------------------------------------------------------
INSERT INTO cidadao (username, email, nome_completo, senha_hash, auth_key, permissao, ativo) VALUES (
    'admin',
    'admin@reclamamunicipal.gov.br',
    'Administrador do Sistema',
    '$2y$13$UsGTlBxOOYr2pKCjjWNaH.c3j3jzYDTvSV.x4NVMwi2bFUmpAkbwm',
    'admin_test_auth_key_2026',
    2,
    1
);

-- ------------------------------------------------------------
-- 11. CONTATOS INICIAIS
-- ------------------------------------------------------------
INSERT INTO contato (nome, cargo, email, telefone, descricao, ativo, ordem) VALUES
    ('Secretaria de Infraestrutura', 'Gestão de Vias e Obras',    'infraestrutura@municipio.gov.br', '(00) 3300-1100', 'Responsável por estradas, calçadas, iluminação pública e obras municipais.', 1, 1),
    ('Secretaria de Saúde',          'Gestão de UBS e Campanhas', 'saude@municipio.gov.br',           '(00) 3300-1200', 'Coordena unidades básicas de saúde, campanhas de vacinação e atendimentos.',  1, 2),
    ('Secretaria de Educação',       'Gestão Escolar Municipal',  'educacao@municipio.gov.br',        '(00) 3300-1300', 'Responsável pelas escolas municipais, creches e programas de ensino.',       1, 3),
    ('Meio Ambiente',                'Gestão Ambiental',          'meioambiente@municipio.gov.br',    '(00) 3300-1400', 'Cuidados com parques, áreas verdes, coleta seletiva e licenciamento ambiental.', 1, 4);
