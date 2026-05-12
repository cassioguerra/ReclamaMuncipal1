-- ============================================================
-- ReclamaMunicipal — Migration: Módulo de Contatos
-- Executar como C##CASSIO no banco Oracle
-- Data: 2026-03-21
-- ============================================================

-- 1. Adicionar coluna PERMISSAO na tabela CIDADAO
--    1 = Cidadão normal (padrão)
--    2 = Gestor (pode criar / editar / excluir contatos)
ALTER TABLE CIDADAO ADD (PERMISSAO NUMBER(1) DEFAULT 1 NOT NULL);

COMMENT ON COLUMN CIDADAO.PERMISSAO IS '1=Cidadão, 2=Gestor (gerencia contatos)';

-- Promover o usuário admin existente para Gestor
UPDATE CIDADAO SET PERMISSAO = 2 WHERE USERNAME = 'admin';

COMMIT;

-- ============================================================
-- 2. Tabela CONTATO
-- ============================================================
CREATE TABLE CONTATO (
    ID           NUMBER         GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    NOME         VARCHAR2(200)  NOT NULL,
    CARGO        VARCHAR2(150),
    EMAIL        VARCHAR2(150),
    TELEFONE     VARCHAR2(30),
    DESCRICAO    VARCHAR2(500),
    FOTO_CAMINHO VARCHAR2(500),
    ATIVO        NUMBER(1)      DEFAULT 1  NOT NULL,
    ORDEM        NUMBER(4)      DEFAULT 0  NOT NULL,
    CREATED_AT   TIMESTAMP      DEFAULT SYSTIMESTAMP,
    UPDATED_AT   TIMESTAMP      DEFAULT SYSTIMESTAMP
);

CREATE INDEX IDX_CONTATO_ATIVO_ORDEM ON CONTATO (ATIVO, ORDEM);

-- ============================================================
-- 3. Contatos de exemplo
-- ============================================================
INSERT INTO CONTATO (NOME, CARGO, EMAIL, TELEFONE, DESCRICAO, ORDEM) VALUES (
    'Secretaria de Infraestrutura',
    'Gestão de Vias e Obras',
    'infraestrutura@municipio.gov.br',
    '(00) 3300-1100',
    'Responsável por estradas, calçadas, iluminação pública e obras municipais.',
    1
);

INSERT INTO CONTATO (NOME, CARGO, EMAIL, TELEFONE, DESCRICAO, ORDEM) VALUES (
    'Secretaria de Saúde',
    'Gestão de UBS e Campanhas',
    'saude@municipio.gov.br',
    '(00) 3300-1200',
    'Coordena unidades básicas de saúde, campanhas de vacinação e atendimentos.',
    2
);

INSERT INTO CONTATO (NOME, CARGO, EMAIL, TELEFONE, DESCRICAO, ORDEM) VALUES (
    'Secretaria de Educação',
    'Gestão Escolar Municipal',
    'educacao@municipio.gov.br',
    '(00) 3300-1300',
    'Responsável pelas escolas municipais, creches e programas de ensino.',
    3
);

INSERT INTO CONTATO (NOME, CARGO, EMAIL, TELEFONE, DESCRICAO, ORDEM) VALUES (
    'Secretaria de Meio Ambiente',
    'Gestão Ambiental',
    'meioambiente@municipio.gov.br',
    '(00) 3300-1400',
    'Cuidados com parques, áreas verdes, coleta seletiva e licenciamento ambiental.',
    4
);

COMMIT;
