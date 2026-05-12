-- ============================================================
-- ReclamaMunicipal — Oracle DDL
-- Servidor : localhost:1521   Serviço: orcl.intelbras.local
-- Usuário  : C##CASSIO  (CDB common user — já criado)
-- Encoding : AL32UTF8
-- ============================================================


-- ------------------------------------------------------------
-- 1. PERMISSÕES ADICIONAIS  (executar como SYS AS SYSDBA se necessário)
-- ------------------------------------------------------------
-- O usuário C##CASSIO já foi criado. Garantir as grants:

GRANT CONNECT, RESOURCE TO C##CASSIO;
GRANT CREATE SESSION  TO C##CASSIO;
GRANT CREATE TABLE    TO C##CASSIO;
GRANT CREATE SEQUENCE TO C##CASSIO;
GRANT CREATE TRIGGER  TO C##CASSIO;
GRANT CREATE VIEW     TO C##CASSIO;
ALTER USER C##CASSIO QUOTA UNLIMITED ON USERS;

-- A partir daqui, conectar-se como C##CASSIO:
-- CONNECT C##CASSIO/CASSIO@//localhost:1521/orcl.intelbras.local


-- ------------------------------------------------------------
-- 2. REMOVER OBJETOS EXISTENTES  (re-execução segura)
-- ------------------------------------------------------------
BEGIN
    FOR r IN (
        SELECT object_name, object_type
        FROM   user_objects
        WHERE  object_type IN ('TABLE','SEQUENCE','TRIGGER')
        AND    object_name IN (
                   'RECLAMACAO_HISTORICO','RECLAMACAO_FOTO',
                   'RECLAMACAO','CATEGORIA','CIDADAO',
                   'SEQ_PROTOCOLO'
               )
    ) LOOP
        BEGIN
            IF r.object_type = 'TABLE' THEN
                EXECUTE IMMEDIATE 'DROP TABLE ' || r.object_name || ' CASCADE CONSTRAINTS PURGE';
            ELSIF r.object_type = 'SEQUENCE' THEN
                EXECUTE IMMEDIATE 'DROP SEQUENCE ' || r.object_name;
            END IF;
        EXCEPTION
            WHEN OTHERS THEN NULL;
        END;
    END LOOP;
END;
/


-- ------------------------------------------------------------
-- 3. TABELA: CIDADAO
-- Representa os cidadãos cadastrados no sistema.
-- "USER" é reservado em Oracle — usamos CIDADAO.
-- ------------------------------------------------------------
CREATE TABLE CIDADAO (
    ID            NUMBER         GENERATED ALWAYS AS IDENTITY
                                 CONSTRAINT pk_cidadao PRIMARY KEY,
    USERNAME      VARCHAR2(50)   NOT NULL,
    EMAIL         VARCHAR2(150)  NOT NULL,
    NOME_COMPLETO VARCHAR2(200)  NOT NULL,
    TELEFONE      VARCHAR2(20),
    SENHA_HASH    VARCHAR2(255)  NOT NULL,
    AUTH_KEY      VARCHAR2(100)  NOT NULL,
    ACCESS_TOKEN  VARCHAR2(100),
    ATIVO         NUMBER(1)      DEFAULT 1 NOT NULL,
    CREATED_AT    TIMESTAMP      DEFAULT SYSTIMESTAMP NOT NULL,
    UPDATED_AT    TIMESTAMP      DEFAULT SYSTIMESTAMP NOT NULL,
    CONSTRAINT uq_cidadao_username UNIQUE (USERNAME),
    CONSTRAINT uq_cidadao_email    UNIQUE (EMAIL),
    CONSTRAINT ck_cidadao_ativo    CHECK  (ATIVO IN (0, 1))
);

CREATE INDEX idx_cidadao_email    ON CIDADAO (EMAIL);
CREATE INDEX idx_cidadao_username ON CIDADAO (USERNAME);

-- Trigger: atualiza UPDATED_AT automaticamente
CREATE OR REPLACE TRIGGER trg_cidadao_updated
    BEFORE UPDATE ON CIDADAO
    FOR EACH ROW
BEGIN
    :NEW.UPDATED_AT := SYSTIMESTAMP;
END;
/


-- ------------------------------------------------------------
-- 4. TABELA: CATEGORIA
-- Categorias de reclamação (infraestrutura, saúde, etc.)
-- ------------------------------------------------------------
CREATE TABLE CATEGORIA (
    ID       NUMBER        GENERATED ALWAYS AS IDENTITY
                           CONSTRAINT pk_categoria PRIMARY KEY,
    SLUG     VARCHAR2(50)  NOT NULL,
    NOME     VARCHAR2(100) NOT NULL,
    ICONE_BI VARCHAR2(80)  NOT NULL,
    ATIVO    NUMBER(1)     DEFAULT 1 NOT NULL,
    CONSTRAINT uq_categoria_slug UNIQUE (SLUG),
    CONSTRAINT ck_categoria_ativo CHECK (ATIVO IN (0, 1))
);


-- ------------------------------------------------------------
-- 5. TABELA: RECLAMACAO
-- Registro principal de cada reclamação de um cidadão.
-- ------------------------------------------------------------
CREATE TABLE RECLAMACAO (
    ID           NUMBER         GENERATED ALWAYS AS IDENTITY
                                CONSTRAINT pk_reclamacao PRIMARY KEY,
    PROTOCOLO    VARCHAR2(20)   NOT NULL,
    CIDADAO_ID   NUMBER         NOT NULL,
    CATEGORIA_ID NUMBER         NOT NULL,
    TITULO       VARCHAR2(200)  NOT NULL,
    DESCRICAO    CLOB           NOT NULL,
    ENDERECO     VARCHAR2(300),
    BAIRRO       VARCHAR2(100),
    URGENCIA     VARCHAR2(10)   DEFAULT 'baixa'    NOT NULL,
    STATUS_REC   VARCHAR2(15)   DEFAULT 'pendente' NOT NULL,
    CREATED_AT   TIMESTAMP      DEFAULT SYSTIMESTAMP NOT NULL,
    UPDATED_AT   TIMESTAMP      DEFAULT SYSTIMESTAMP NOT NULL,
    CONSTRAINT uq_reclamacao_protocolo  UNIQUE      (PROTOCOLO),
    CONSTRAINT fk_reclamacao_cidadao    FOREIGN KEY (CIDADAO_ID)
                                        REFERENCES  CIDADAO(ID),
    CONSTRAINT fk_reclamacao_categoria  FOREIGN KEY (CATEGORIA_ID)
                                        REFERENCES  CATEGORIA(ID),
    CONSTRAINT ck_reclamacao_urgencia   CHECK (URGENCIA   IN ('baixa','media','alta')),
    CONSTRAINT ck_reclamacao_status     CHECK (STATUS_REC IN ('pendente','andamento','resolvida','arquivada'))
);

CREATE INDEX idx_reclamacao_cidadao   ON RECLAMACAO (CIDADAO_ID);
CREATE INDEX idx_reclamacao_categoria ON RECLAMACAO (CATEGORIA_ID);
CREATE INDEX idx_reclamacao_status    ON RECLAMACAO (STATUS_REC);
CREATE INDEX idx_reclamacao_criado    ON RECLAMACAO (CREATED_AT);

-- Sequence para gerar número do protocolo (AAAA + 6 dígitos)
CREATE SEQUENCE SEQ_PROTOCOLO
    START WITH     1001
    INCREMENT BY   1
    NOCYCLE
    NOCACHE
    ORDER;

-- Trigger: gera PROTOCOLO e atualiza UPDATED_AT
CREATE OR REPLACE TRIGGER trg_reclamacao_before
    BEFORE INSERT OR UPDATE ON RECLAMACAO
    FOR EACH ROW
BEGIN
    IF INSERTING AND (:NEW.PROTOCOLO IS NULL OR :NEW.PROTOCOLO = '') THEN
        :NEW.PROTOCOLO := TO_CHAR(EXTRACT(YEAR FROM SYSTIMESTAMP))
                       || LPAD(TO_CHAR(SEQ_PROTOCOLO.NEXTVAL), 6, '0');
    END IF;
    IF UPDATING THEN
        :NEW.UPDATED_AT := SYSTIMESTAMP;
    END IF;
END;
/


-- ------------------------------------------------------------
-- 6. TABELA: RECLAMACAO_FOTO
-- Fotos anexadas pelo cidadão ao registrar a reclamação.
-- ------------------------------------------------------------
CREATE TABLE RECLAMACAO_FOTO (
    ID            NUMBER         GENERATED ALWAYS AS IDENTITY
                                 CONSTRAINT pk_foto PRIMARY KEY,
    RECLAMACAO_ID NUMBER         NOT NULL,
    CAMINHO       VARCHAR2(500)  NOT NULL,
    NOME_ORIGINAL VARCHAR2(255)  NOT NULL,
    MIME_TYPE     VARCHAR2(80)   DEFAULT 'image/jpeg' NOT NULL,
    TAMANHO_BYTES NUMBER         DEFAULT 0,
    CREATED_AT    TIMESTAMP      DEFAULT SYSTIMESTAMP NOT NULL,
    CONSTRAINT fk_foto_reclamacao FOREIGN KEY (RECLAMACAO_ID)
                                  REFERENCES RECLAMACAO(ID)
                                  ON DELETE CASCADE
);

CREATE INDEX idx_foto_reclamacao ON RECLAMACAO_FOTO (RECLAMACAO_ID);


-- ------------------------------------------------------------
-- 7. TABELA: RECLAMACAO_HISTORICO
-- Registro de mudanças de status (auditoria).
-- ------------------------------------------------------------
CREATE TABLE RECLAMACAO_HISTORICO (
    ID              NUMBER          GENERATED ALWAYS AS IDENTITY
                                    CONSTRAINT pk_historico PRIMARY KEY,
    RECLAMACAO_ID   NUMBER          NOT NULL,
    STATUS_ANTERIOR VARCHAR2(15),
    STATUS_NOVO     VARCHAR2(15)    NOT NULL,
    OBSERVACAO      VARCHAR2(1000),
    GESTOR          VARCHAR2(100),
    CREATED_AT      TIMESTAMP       DEFAULT SYSTIMESTAMP NOT NULL,
    CONSTRAINT fk_hist_reclamacao FOREIGN KEY (RECLAMACAO_ID)
                                  REFERENCES RECLAMACAO(ID)
                                  ON DELETE CASCADE
);

CREATE INDEX idx_hist_reclamacao ON RECLAMACAO_HISTORICO (RECLAMACAO_ID);


-- ------------------------------------------------------------
-- 8. DADOS INICIAIS — Categorias
-- ------------------------------------------------------------
INSERT INTO CATEGORIA (SLUG, NOME, ICONE_BI)
    VALUES ('infraestrutura', 'Infraestrutura',  'bi-cone-striped');
INSERT INTO CATEGORIA (SLUG, NOME, ICONE_BI)
    VALUES ('saneamento',     'Saneamento',      'bi-droplet-fill');
INSERT INTO CATEGORIA (SLUG, NOME, ICONE_BI)
    VALUES ('saude',          'Saúde',           'bi-heart-pulse-fill');
INSERT INTO CATEGORIA (SLUG, NOME, ICONE_BI)
    VALUES ('educacao',       'Educação',        'bi-mortarboard-fill');
INSERT INTO CATEGORIA (SLUG, NOME, ICONE_BI)
    VALUES ('transporte',     'Transporte',      'bi-bus-front-fill');
INSERT INTO CATEGORIA (SLUG, NOME, ICONE_BI)
    VALUES ('seguranca',      'Segurança',       'bi-shield-fill-check');
INSERT INTO CATEGORIA (SLUG, NOME, ICONE_BI)
    VALUES ('meio-ambiente',  'Meio Ambiente',   'bi-tree-fill');
INSERT INTO CATEGORIA (SLUG, NOME, ICONE_BI)
    VALUES ('outros',         'Outros Serviços', 'bi-list-check');

-- Cidadão admin para testes iniciais
-- ATENÇÃO: altere a senha (SENHA_HASH) antes de usar em produção.
-- Hash gerado com: Yii::$app->security->generatePasswordHash('Admin@2026')
INSERT INTO CIDADAO (USERNAME, EMAIL, NOME_COMPLETO, SENHA_HASH, AUTH_KEY, ATIVO)
    VALUES (
        'admin',
        'admin@reclamamunicipial.gov.br',
        'Administrador do Sistema',
        '$2y$13$UsGTlBxOOYr2pKCjjWNaH.c3j3jzYDTvSV.x4NVMwi2bFUmpAkbwm',
        'admin_test_auth_key_2026',
        1
    );

COMMIT;


-- ------------------------------------------------------------
-- 9. VIEW AUXILIAR: v_reclamacao_resumo
-- Facilita consultas de listagem sem joins manuais.
-- ------------------------------------------------------------
CREATE OR REPLACE VIEW v_reclamacao_resumo AS
    SELECT
        r.ID,
        r.PROTOCOLO,
        r.TITULO,
        r.URGENCIA,
        r.STATUS_REC,
        r.BAIRRO,
        r.CREATED_AT,
        c.USERNAME        AS CIDADAO_USERNAME,
        c.NOME_COMPLETO   AS CIDADAO_NOME,
        cat.NOME          AS CATEGORIA_NOME,
        cat.ICONE_BI      AS CATEGORIA_ICONE
    FROM
        RECLAMACAO   r
        JOIN CIDADAO   c   ON c.ID   = r.CIDADAO_ID
        JOIN CATEGORIA cat ON cat.ID = r.CATEGORIA_ID;

COMMIT;
