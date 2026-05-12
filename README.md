<div align="center">

<img src="https://img.shields.io/badge/PHP-8.0+-777BB4?style=for-the-badge&logo=php&logoColor=white"/>
<img src="https://img.shields.io/badge/Yii2-Framework-007ACC?style=for-the-badge&logo=yii&logoColor=white"/>
<img src="https://img.shields.io/badge/MySQL-Database-4479A1?style=for-the-badge&logo=mysql&logoColor=white"/>
<img src="https://img.shields.io/badge/Bootstrap-5-7952B3?style=for-the-badge&logo=bootstrap&logoColor=white"/>
<img src="https://img.shields.io/badge/License-BSD--3--Clause-green?style=for-the-badge"/>

<br/><br/>

# 🏛️ ReclamaMunicipal

### *Sua voz transforma o seu município*

**Sistema web para registro, acompanhamento e gestão de reclamações municipais — conectando cidadãos e prefeitura de forma 100% online.**

<br/>

[🚀 Funcionalidades](#-funcionalidades) •
[🛠️ Tecnologias](#%EF%B8%8F-tecnologias) •
[📁 Estrutura](#-estrutura-do-projeto) •
[⚙️ Instalação](#%EF%B8%8F-instalação) •
[🗄️ Banco de Dados](#%EF%B8%8F-banco-de-dados) •
[👥 Perfis de Acesso](#-perfis-de-acesso)

</div>

---

## 📸 Preview

> Interface moderna com tema verde, responsiva e acessível.

| Página Inicial | Áreas de Atuação |
|:-:|:-:|
| Hero com estatísticas em tempo real | 8 categorias de reclamação disponíveis |

---

## 🚀 Funcionalidades

### 👤 Área do Cidadão
| Funcionalidade | Descrição |
|---|---|
| 📝 Registrar Reclamação | Formulário completo com categoria, urgência, endereço e fotos |
| 🔍 Consultar Status | Acompanhe o andamento via número de protocolo |
| 🖼️ Enviar Fotos | Upload de evidências fotográficas junto à reclamação |
| 👤 Perfil Pessoal | Gerencie seus dados e veja seu histórico de reclamações |
| 📊 Dashboard | Painel pessoal com resumo das reclamações enviadas |

### 🏛️ Área do Gestor
| Funcionalidade | Descrição |
|---|---|
| 📋 Listar Reclamações | Visualização completa com filtro por status |
| ✏️ Atualizar Status | Mova reclamações: pendente → andamento → resolvida → arquivada |
| 📎 Adicionar Evidências | Gestor pode anexar fotos da resolução |
| 💰 Registrar Custo | Campo para informar o valor gasto na resolução |
| 📜 Histórico | Linha do tempo completa de todas as atualizações |

### 📰 Blog Municipal
- Criação e edição de posts por gestores
- Foto de capa e banner por post
- Listagem pública acessível a todos os cidadãos

### 📞 Secretarias & Contatos
- Cadastro de secretarias municipais com foto, cargo, e-mail e telefone
- Gerenciamento por ordem de exibição
- Página pública de contatos institucionais

### 🌐 Portal Público
- Estatísticas em tempo real (total de reclamações, % resolvidas, tempo médio)
- 8 categorias de áreas de atuação
- Seção "Como funciona" com explicação em 3 passos
- Seção "Pronto para fazer a diferença?" com CTA

---

## 🗂️ Categorias de Reclamação

| Ícone | Categoria | Exemplos |
|:---:|---|---|
| 🚧 | **Infraestrutura** | Buracos, calçadas, iluminação pública |
| 💧 | **Saneamento** | Esgoto, água, limpeza urbana |
| ❤️ | **Saúde** | Postos, UBS, atendimento médico |
| 🎓 | **Educação** | Escolas, creches, estrutura escolar |
| 🚌 | **Transporte** | Ônibus, terminais, mobilidade |
| 🛡️ | **Segurança** | Iluminação, câmeras, fiscalização |
| 🌿 | **Meio Ambiente** | Poda, resíduos, área verde |
| 📋 | **Outros Serviços** | Demais serviços municipais |

---

## 📊 Status e Urgência das Reclamações

**Status disponíveis:**

```
🟡 Pendente  →  🔵 Em Andamento  →  🟢 Resolvida  →  ⚫ Arquivada
```

**Níveis de urgência:**

| Nível | Indicação |
|:---:|---|
| 🟢 Baixa | Problema de baixo impacto no cotidiano |
| 🟡 Média | Impacto moderado, atenção necessária |
| 🔴 Alta | Impacto crítico, resolução urgente |

---

## 🛠️ Tecnologias

| Camada | Tecnologia | Versão |
|---|---|:---:|
| **Backend** | PHP | `≥ 8.0` |
| **Framework** | Yii2 Basic | `~2.0.14` |
| **Frontend** | Bootstrap | `5` |
| **Banco de Dados** | MySQL | `5.7+` / `8.0+` |
| **JavaScript** | jQuery | `3.7.*` |
| **Testes** | Codeception | `^5.0` |
| **Dependências** | Composer | `2.x` |

---

## 📁 Estrutura do Projeto

```
ReclamaMunicipal/
│
├── 📂 assets/              → AssetBundles (CSS/JS via Yii)
├── 📂 components/          → Componentes customizados (ex: OracleSchema)
├── 📂 config/              → Configurações da aplicação
│   ├── web.php             → Configuração principal web
│   ├── db.php              → Conexão com o banco de dados
│   └── params.php          → Parâmetros globais
│
├── 📂 controllers/         → Controllers HTTP
│   ├── SiteController.php  → Página inicial, reclamações, perfil, login
│   ├── GestaoController.php→ Painel do gestor municipal
│   ├── BlogController.php  → CRUD do blog
│   └── ContatoController.php → Gestão de secretarias
│
├── 📂 migrations/          → Scripts SQL de migração
│   ├── 001_contato.sql     → Módulo de contatos e permissões
│   ├── 002_fix_encoding_duplicates.sql
│   ├── 003_gestao_reclamacao.sql → Evidências e valor gasto
│   └── 004_blog.sql        → Módulo blog
│
├── 📂 models/              → Modelos ActiveRecord
│   ├── Cidadao.php         → Usuário / Identidade (IdentityInterface)
│   ├── Reclamacao.php      → Entidade principal
│   ├── ReclamacaoFoto.php  → Fotos da reclamação
│   ├── ReclamacaoEvidencia.php → Evidências do gestor
│   ├── ReclamacaoHistorico.php → Histórico de atualizações
│   ├── Categoria.php       → Categorias de reclamação
│   ├── Contato.php         → Secretarias municipais
│   ├── Blog.php            → Posts do blog
│   └── LoginForm.php       → Formulário de autenticação
│
├── 📂 views/               → Views PHP + layouts
│   ├── layouts/            → Layout principal e sidebar
│   ├── site/               → Views públicas e do cidadão
│   ├── gestao/             → Views do painel do gestor
│   ├── blog/               → Views do blog
│   └── contato/            → Views de contatos
│
├── 📂 web/                 → Document root (entry point)
│   ├── index.php           → Entrada da aplicação
│   └── css/                → CSS customizados
│
├── 📂 widgets/             → Widgets reutilizáveis
└── 📂 sql/                 → Scripts de setup inicial do banco
```

---

## ⚙️ Instalação

### Pré-requisitos

- PHP `>= 8.0` com extensões: `pdo_mysql`, `mbstring`, `gd`, `fileinfo`
- MySQL `5.7+` ou `8.0+`
- Composer `2.x`
- Servidor web (Apache via XAMPP ou Nginx)

### Passo a passo

**1. Clone o repositório**
```bash
git clone https://github.com/seu-usuario/ReclamaMunicipal.git
cd ReclamaMunicipal
```

**2. Instale as dependências PHP**
```bash
composer install
```

**3. Configure o banco de dados**

Edite o arquivo `config/db.php`:
```php
return [
    'class'    => 'yii\db\Connection',
    'dsn'      => 'mysql:host=localhost;dbname=reclama_municipal',
    'username' => 'seu_usuario',
    'password' => 'sua_senha',
    'charset'  => 'utf8',
];
```

**4. Execute as migrações SQL**
```bash
# Execute os scripts na pasta migrations/ na ordem:
# 001_contato.sql → 002_fix_encoding_duplicates.sql → 003_gestao_reclamacao.sql → 004_blog.sql
# Ou use o script unificado:
mysql -u root -p reclama_municipal < migrations/run_all.sql
```

**5. Configure as permissões de escrita**
```bash
chmod -R 755 runtime/
chmod -R 755 web/assets/
chmod -R 755 web/uploads/   # Se existir
```

**6. Configure o Document Root**

Aponte o virtual host do Apache/Nginx para o diretório `web/`:
```apache
DocumentRoot "/caminho/para/ReclamaMunicipal/web"
```

**7. Acesse a aplicação**
```
http://localhost/ReclamaMunicipal/web/
```

---

## 🗄️ Banco de Dados

### Diagrama de entidades (simplificado)

```
CIDADAO (1) ──────────────── (N) RECLAMACAO
    │                               │
    │                          ┌────┴────────────────────┐
    │                          │            │             │
    └── (N) BLOG         RECLAMACAO_  RECLAMACAO_  RECLAMACAO_
                           FOTO        HISTORICO    EVIDENCIA

CATEGORIA (1) ──────────── (N) RECLAMACAO

CONTATO  (independente — secretarias municipais)
```

### Tabelas principais

| Tabela | Descrição |
|---|---|
| `cidadao` | Usuários do sistema (cidadãos e gestores) |
| `reclamacao` | Reclamações registradas com protocolo único |
| `reclamacao_foto` | Fotos enviadas pelo cidadão |
| `reclamacao_evidencia` | Fotos de resolução enviadas pelo gestor |
| `reclamacao_historico` | Linha do tempo de atualizações |
| `categoria` | 8 categorias de reclamação |
| `contato` | Secretarias e contatos municipais |
| `blog` | Posts do blog municipal |

---

## 👥 Perfis de Acesso

### 🙍 Cidadão (`permissao = 1`)
- Cadastro e login
- Registrar e acompanhar reclamações próprias
- Enviar fotos como evidência
- Visualizar blog e contatos

### 🏛️ Gestor (`permissao = 2`)
- Tudo do cidadão +
- Gerenciar **todas** as reclamações (qualquer cidadão)
- Atualizar status e adicionar comentários
- Gerenciar blog (criar, editar, excluir posts)
- Gerenciar secretarias/contatos
- Visualizar valor gasto por reclamação

> Para promover um usuário a gestor, execute:
> ```sql
> UPDATE cidadao SET permissao = 2 WHERE username = 'seu_usuario';
> ```

---

## 🧪 Testes

O projeto usa **Codeception** para testes automatizados:

```bash
# Rodar todos os testes
php vendor/bin/codecept run

# Rodar apenas testes unitários
php vendor/bin/codecept run unit

# Rodar testes funcionais
php vendor/bin/codecept run functional
```

---

## 🔒 Segurança

- ✅ Senhas armazenadas com `password_hash()` (bcrypt)
- ✅ Proteção contra CSRF em todos os formulários
- ✅ Controle de acesso por `AccessControl` do Yii2
- ✅ Validação e sanitização de inputs com `ActiveForm`
- ✅ Upload de arquivos com validação de MIME type
- ✅ Proteção contra XSS via `Html::encode()`

---

## 📌 Convenções do Projeto

| Elemento | Padrão |
|---|---|
| Classes PHP | `PascalCase` |
| Métodos | `camelCase` |
| Tabelas DB | `snake_case` |
| Colunas DB | `snake_case` |
| Views | `kebab-case.php` |
| Controllers | `PascalCaseController` |
| Actions | `actionNomeAcao()` |

---

## 📄 Licença

Distribuído sob a licença **BSD-3-Clause**.  
Consulte o arquivo `LICENSE` para mais detalhes.

---

<div align="center">

**ReclamaMunicipal** — *Conectando cidadãos e governo por uma cidade melhor* 🏙️

Desenvolvido com ❤️ usando [Yii2](https://www.yiiframework.com/) + [Bootstrap 5](https://getbootstrap.com/)

</div>
