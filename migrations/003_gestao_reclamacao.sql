-- ============================================================
-- 003_gestao_reclamacao.sql
-- Adiciona coluna VALOR_GASTO a RECLAMACAO
-- Cria tabela RECLAMACAO_EVIDENCIA (fotos enviadas pelo gestor)
-- Execute como C##CASSIO no SQL Developer
-- ============================================================

-- 1. Coluna de custo na reclamação
ALTER TABLE RECLAMACAO ADD (VALOR_GASTO NUMBER(12,2) DEFAULT NULL);

-- 2. Tabela de evidências do gestor
CREATE TABLE RECLAMACAO_EVIDENCIA (
    ID            NUMBER         GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    RECLAMACAO_ID NUMBER         NOT NULL,
    CAMINHO       VARCHAR2(500)  NOT NULL,
    DESCRICAO     VARCHAR2(300),
    CREATED_AT    TIMESTAMP      DEFAULT SYSTIMESTAMP,
    CONSTRAINT fk_evidencia_rec FOREIGN KEY (RECLAMACAO_ID)
        REFERENCES RECLAMACAO(ID) ON DELETE CASCADE
);

CREATE INDEX idx_evidencia_rec ON RECLAMACAO_EVIDENCIA (RECLAMACAO_ID);

COMMIT;
