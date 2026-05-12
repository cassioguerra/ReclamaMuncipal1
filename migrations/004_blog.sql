-- ============================================================
-- Migration 004 — Módulo Blog
-- Executar como C##CASSIO no SQL Developer
-- ============================================================

-- Tabela principal de posts do blog
CREATE TABLE BLOG (
    ID          NUMBER          GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    TITULO      VARCHAR2(200)   NOT NULL,
    RESUMO      VARCHAR2(500)   NOT NULL,
    CONTEUDO    CLOB            NOT NULL,
    FOTO_CAPA   VARCHAR2(500)   NOT NULL,
    FOTO_BANNER VARCHAR2(500)   NOT NULL,
    CIDADAO_ID  NUMBER          NOT NULL,
    ATIVO       NUMBER(1)       DEFAULT 1 NOT NULL,
    CREATED_AT  TIMESTAMP       DEFAULT SYSTIMESTAMP NOT NULL,
    UPDATED_AT  TIMESTAMP       DEFAULT SYSTIMESTAMP NOT NULL,
    CONSTRAINT fk_blog_cidadao FOREIGN KEY (CIDADAO_ID) REFERENCES CIDADAO(ID)
);

CREATE INDEX idx_blog_ativo_created ON BLOG (ATIVO, CREATED_AT DESC);
CREATE INDEX idx_blog_cidadao       ON BLOG (CIDADAO_ID);

COMMIT;
