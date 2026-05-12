-- ============================================================
-- ReclamaMunicipal — Correção de dados
-- Execute como C##CASSIO no SQL Developer
-- ============================================================

-- ── 1. Corrigir encoding dos contatos (dados corrompidos pelo NLS) ───
-- Remove os contatos com encoding errado e re-insere com UNISTR correto
DELETE FROM CONTATO;

INSERT INTO CONTATO (NOME, CARGO, EMAIL, TELEFONE, DESCRICAO, ATIVO, ORDEM) VALUES (
    'Secretaria de Infraestrutura',
    UNISTR('Gest\00E3o de Vias e Obras'),
    'infraestrutura@municipio.gov.br',
    '(00) 3300-1100',
    UNISTR('Respons\00E1vel por estradas, cal\00E7adas, ilumina\00E7\00E3o p\00FAblica e obras municipais.'),
    1, 1
);

INSERT INTO CONTATO (NOME, CARGO, EMAIL, TELEFONE, DESCRICAO, ATIVO, ORDEM) VALUES (
    UNISTR('Secretaria de Sa\00FAde'),
    UNISTR('Gest\00E3o de UBS e Campanhas'),
    'saude@municipio.gov.br',
    '(00) 3300-1200',
    UNISTR('Coordena unidades b\00E1sicas de sa\00FAde, campanhas de vacina\00E7\00E3o e atendimentos.'),
    1, 2
);

INSERT INTO CONTATO (NOME, CARGO, EMAIL, TELEFONE, DESCRICAO, ATIVO, ORDEM) VALUES (
    UNISTR('Secretaria de Educa\00E7\00E3o'),
    UNISTR('Gest\00E3o Escolar Municipal'),
    'educacao@municipio.gov.br',
    '(00) 3300-1300',
    UNISTR('Respons\00E1vel pelas escolas municipais, creches e programas de ensino.'),
    1, 3
);

INSERT INTO CONTATO (NOME, CARGO, EMAIL, TELEFONE, DESCRICAO, ATIVO, ORDEM) VALUES (
    'Meio Ambiente',
    UNISTR('Gest\00E3o Ambiental'),
    'meioambiente@municipio.gov.br',
    '(00) 3300-1400',
    UNISTR('Cuidados com parques, \00E1reas verdes, coleta seletiva e licenciamento ambiental.'),
    1, 4
);

COMMIT;

-- ── 2. Remover reclamações duplicadas (geradas pelo bug do duplo-clique) ─
-- Mantém apenas a PRIMEIRA ocorrência (menor ID) de cada conteúdo duplicado
DELETE FROM RECLAMACAO_FOTO
WHERE RECLAMACAO_ID IN (
    SELECT MAX(ID)
    FROM RECLAMACAO
    WHERE CIDADAO_ID = 2
    GROUP BY CIDADAO_ID, TITULO, DESCRICAO, BAIRRO, URGENCIA
    HAVING COUNT(*) > 1
);

DELETE FROM RECLAMACAO
WHERE ID IN (
    SELECT MAX(ID)
    FROM RECLAMACAO
    WHERE CIDADAO_ID = 2
    GROUP BY CIDADAO_ID, TITULO, DESCRICAO, BAIRRO, URGENCIA
    HAVING COUNT(*) > 1
);

COMMIT;
