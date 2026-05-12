---
description: Regras globais para o projeto ReclamaMunicipal — Yii2, PHP, MySQL, Bootstrap 5, CSS e Testes
applyTo: "**/*.php,**/*.css,**/*.js,**/*.sql,**/*.yml,**/*.yaml,**/*.json"
alwaysApply: true
---

# Regras Globais — ReclamaMunicipal

Projeto web baseado em **Yii2 Basic Template** com PHP 7.4+, MySQL, Bootstrap 5 e Codeception para testes.  
Siga obrigatoriamente estas regras em todos os arquivos do projeto.

---

## 1. ESTRUTURA DO PROJETO

```
ReclamaMunicipal/
├── assets/          → AssetBundles (CSS/JS registrados via Yii)
├── commands/        → Controllers de console (CLI)
├── config/          → Arquivos de configuração da aplicação
├── controllers/     → Controllers HTTP (herdam yii\web\Controller)
├── mail/            → Templates de e-mail (layouts html e text)
├── models/          → Models (herdam yii\base\Model ou ActiveRecord)
├── runtime/         → Gerado em runtime (NÃO versionar)
├── tests/           → Testes Codeception (unit, functional, acceptance)
├── views/           → Views PHP + layouts
├── web/             → Document root (index.php, assets compilados)
└── widgets/         → Widgets reutilizáveis (herdam yii\base\Widget)
```

**Regras de estrutura:**
- Nunca coloque lógica de negócio em views.
- Nunca coloque SQL direto em controllers.
- Nunca versione o diretório `runtime/` ou `web/assets/`.
- Coloque toda lógica reutilizável em Models ou Widgets.

---

## 2. PHP — CONVENÇÕES GERAIS

### 2.1 Versão e configuração
- PHP mínimo: **7.4** (tipagem forte quando possível).
- Sempre usar `declare(strict_types=1)` em arquivos de classes novas.
- Charset padrão: **UTF-8** em todos os arquivos.

### 2.2 Namespaces e carregamento automático
```php
// Padrão de namespace para cada diretório:
namespace app\controllers;  // controllers/
namespace app\models;       // models/
namespace app\commands;     // commands/
namespace app\assets;       // assets/
namespace app\widgets;      // widgets/
// Views NÃO possuem namespace
```

### 2.3 Nomenclatura
| Elemento          | Convenção    | Exemplo                    |
|-------------------|--------------|----------------------------|
| Classes           | PascalCase   | `ReclamacaoController`     |
| Métodos           | camelCase    | `actionCriarReclamacao()`  |
| Propriedades pub. | camelCase    | `$nomeUsuario`             |
| Propriedades priv.| camelCase    | `$_usuarioCached`          |
| Constantes        | UPPER_SNAKE  | `STATUS_PENDENTE`          |
| Variáveis locais  | camelCase    | `$totalReclamacoes`        |
| Tabelas DB        | snake_case   | `reclamacao`, `categoria`  |
| Colunas DB        | snake_case   | `created_at`, `usuario_id` |
| Views             | kebab-case   | `criar-reclamacao.php`     |
| Config array keys | camelCase    | `'basePath'`, `'cookieValidationKey'` |

### 2.4 Estrutura de classes
```php
<?php

declare(strict_types=1);

namespace app\models;

use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;

/**
 * Model Reclamacao.
 *
 * @property int $id
 * @property string $titulo
 * @property string $descricao
 * @property int $status
 * @property int $created_at
 * @property int $updated_at
 */
class Reclamacao extends ActiveRecord
{
    // Constantes de status
    const STATUS_PENDENTE  = 0;
    const STATUS_EM_ANALISE = 1;
    const STATUS_RESOLVIDA  = 2;

    // Propriedades públicas adicionais (não mapeadas para DB)
    public string $arquivo = '';

    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'reclamacao';
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors(): array
    {
        return [
            TimestampBehavior::class,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['titulo', 'descricao'], 'required'],
            [['titulo'], 'string', 'max' => 255],
            [['descricao'], 'string'],
            [['status'], 'integer'],
            [['status'], 'default', 'value' => self::STATUS_PENDENTE],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'id'         => 'ID',
            'titulo'     => 'Título',
            'descricao'  => 'Descrição',
            'status'     => 'Status',
            'created_at' => 'Criado em',
            'updated_at' => 'Atualizado em',
        ];
    }
}
```

---

## 3. YII2 — CONTROLLERS

### 3.1 Web Controllers
```php
<?php

declare(strict_types=1);

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use app\models\Reclamacao;

class ReclamacaoController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors(): array
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow'   => true,
                        'actions' => ['index', 'view'],
                        'roles'   => ['?', '@'],
                    ],
                    [
                        'allow'   => true,
                        'actions' => ['create', 'update', 'delete'],
                        'roles'   => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class'   => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    // Ações sempre prefixadas com "action"
    public function actionIndex(): string
    {
        // ...
    }
}
```

### 3.2 Regras de Controllers
- Nomes: `NomeController.php` em `controllers/`.
- Sempre definir `behaviors()` com `AccessControl` e `VerbFilter`.
- Operações destrutivas (delete) somente via `POST`.
- Nunca instanciar modelos fora de controllers/actions.
- Use `Yii::$app->session->setFlash()` para mensagens de feedback.
- Use `$this->redirect()` após POST bem-sucedido (PRG pattern).

---

## 4. YII2 — MODELS

### 4.1 ActiveRecord (banco de dados)
```php
// Sempre definir tableName(), rules(), attributeLabels()
// Sempre usar behaviors para timestamps:
public function behaviors(): array
{
    return [TimestampBehavior::class];
}

// Queries via escopos estáticos:
public static function findPendentes(): ActiveQuery
{
    return static::find()->where(['status' => self::STATUS_PENDENTE]);
}
```

### 4.2 Form Models (sem banco)
```php
// Herda yii\base\Model
// Define regras de validação completas
// Não contém lógica de persistência — delega ao ActiveRecord
```

### 4.3 Regras de Models
- Sempre validar TODOS os campos recebidos pelo usuário.
- Nunca usar `$model->save()` sem verificar o retorno.
- Usar `$model->load(Yii::$app->request->post())` para popular dados.
- Nunca confiar em dados do usuário sem passar pelas `rules()`.
- Usar `scenarios()` para diferenciar regras por contexto.

---

## 5. YII2 — VIEWS

### 5.1 Estrutura padrão de view
```php
<?php
/** @var yii\web\View $this */
/** @var app\models\Reclamacao $model */

use yii\bootstrap5\Html;
use yii\bootstrap5\ActiveForm;

$this->title = 'Nova Reclamação';
$this->params['breadcrumbs'][] = ['label' => 'Reclamações', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="reclamacao-create">
    <h1><?= Html::encode($this->title) ?></h1>
    <?= $this->render('_form', ['model' => $model]) ?>
</div>
```

### 5.2 Regras de Views
- **SEMPRE** usar `Html::encode()` ao exibir dados do usuário (prevenção de XSS).
- Nunca usar `echo $variavel` diretamente sem sanitização.
- Usar `<?=` somente com dados já tratados ou com `Html::encode()`.
- Formulários sempre via `ActiveForm` do Bootstrap 5.
- Botões de ação destrutiva sempre com confirmação JavaScript.
- Breadcrumbs sempre definidos em `$this->params['breadcrumbs']`.
- Prefixar partials com underscore: `_form.php`, `_search.php`.

### 5.3 Bootstrap 5 obrigatório
```php
// Usar sempre as classes yii2-bootstrap5
use yii\bootstrap5\Html;
use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Nav;
use yii\bootstrap5\NavBar;
use yii\bootstrap5\Modal;
// NÃO usar yii\helpers\Html diretamente (usar a versão bootstrap5)
```

### 5.4 Formulários
```php
<?php $form = ActiveForm::begin([
    'id'      => 'reclamacao-form',
    'options' => ['enctype' => 'multipart/form-data'], // somente com upload
]) ?>
    <?= $form->field($model, 'titulo')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'descricao')->textarea(['rows' => 6]) ?>
    <div class="form-group">
        <?= Html::submitButton('Salvar', ['class' => 'btn btn-success']) ?>
    </div>
<?php ActiveForm::end() ?>
```

---

## 6. YII2 — LAYOUTS

### 6.1 Layout principal (`views/layouts/main.php`)
- Sempre registrar `AppAsset`.
- Sempre incluir meta CSRF: `Html::csrfMetaTags()`.
- Sempre incluir `Alert` widget para flash messages.
- Footer com ano dinâmico: `<?= date('Y') ?>`.

### 6.2 Flash Messages
```php
// No controller:
Yii::$app->session->setFlash('success', 'Reclamação criada com sucesso!');
Yii::$app->session->setFlash('error', 'Ocorreu um erro. Tente novamente.');

// No layout (já gerenciado pelo widget Alert):
<?= \app\widgets\Alert::widget() ?>
```

---

## 7. BANCO DE DADOS — MySQL

### 7.1 Configuração
```php
// config/db.php
return [
    'class'    => 'yii\db\Connection',
    'dsn'      => 'mysql:host=localhost;dbname=reclamaMunicipal',
    'username' => 'root',
    'password' => '',    // Usar variáveis de ambiente em produção
    'charset'  => 'utf8mb4',  // utf8mb4 suporta emojis e caracteres especiais
];
```

### 7.2 Migrações
```bash
# Criar migration:
php yii migrate/create create_reclamacao_table

# Executar:
php yii migrate

# Reverter:
php yii migrate/down
```

### 7.3 Estrutura de migration
```php
<?php

use yii\db\Migration;

class m260321_000000_create_reclamacao_table extends Migration
{
    public function safeUp(): void
    {
        $this->createTable('reclamacao', [
            'id'          => $this->primaryKey(),
            'titulo'      => $this->string(255)->notNull(),
            'descricao'   => $this->text()->notNull(),
            'status'      => $this->smallInteger()->notNull()->defaultValue(0),
            'usuario_id'  => $this->integer()->notNull(),
            'created_at'  => $this->integer()->notNull(),
            'updated_at'  => $this->integer()->notNull(),
        ]);

        $this->addForeignKey(
            'fk-reclamacao-usuario_id',
            'reclamacao', 'usuario_id',
            'user', 'id',
            'CASCADE'
        );

        $this->createIndex('idx-reclamacao-status', 'reclamacao', 'status');
    }

    public function safeDown(): void
    {
        $this->dropForeignKey('fk-reclamacao-usuario_id', 'reclamacao');
        $this->dropTable('reclamacao');
    }
}
```

### 7.4 Queries
```php
// CORRETO — usar ActiveRecord ou Query Builder
$reclamacoes = Reclamacao::find()
    ->where(['status' => Reclamacao::STATUS_PENDENTE])
    ->orderBy(['created_at' => SORT_DESC])
    ->all();

// CORRETO — Query Builder para queries complexas
$query = (new \yii\db\Query())
    ->select(['r.id', 'r.titulo', 'u.username'])
    ->from('reclamacao r')
    ->join('INNER JOIN', 'user u', 'u.id = r.usuario_id')
    ->where(['r.status' => 0]);

// ERRADO — nunca SQL direto com interpolação de variáveis (SQL injection)
// $sql = "SELECT * FROM reclamacao WHERE titulo = '$titulo'"; // PROIBIDO
```

---

## 8. CSS

### 8.1 Organização
- Estilos globais em `web/css/site.css`.
- Estilos específicos de módulo em arquivos separados registrados via AssetBundle.
- Nunca usar `style=""` inline (exceto para valores dinâmicos inevitáveis).
- Sempre usar variáveis CSS para cores primárias do projeto.

### 8.2 Convenções de nomenclatura CSS
```css
/* Classes: kebab-case */
.reclamacao-card { }
.status-badge { }
.form-reclamacao { }

/* IDs: somente para âncoras JS, nunca para estilo */
#mapa-reclamacoes { }

/* Variáveis globais */
:root {
    --color-primary:   #2c6fad;
    --color-success:   #28a745;
    --color-danger:    #dc3545;
    --color-warning:   #ffc107;
    --color-secondary: #6c757d;
}
```

### 8.3 Regras CSS
- Priorizar classes utilitárias do Bootstrap 5 antes de estilos customizados.
- Usar `rem` para tamanhos de fonte, `px` para bordas e sombras finas.
- Mobile-first: escrever estilos base para mobile, usar `@media (min-width: ...)` para desktop.

---

## 9. JAVASCRIPT

### 9.1 Padrão
- Preferir JavaScript puro + jQuery (já disponível via Bootstrap/Yii2).
- Não usar frameworks JS pesados (React, Vue) sem justificativa clara.
- Registrar JS via AssetBundle, nunca via `<script>` inline em views.

### 9.2 Asset Bundle para JS customizado
```php
// assets/AppAsset.php
public $js = [
    'js/site.js',  // arquivo em web/js/site.js
];
```

### 9.3 Eventos e DOM
```javascript
// Usar vanilla JS ou jQuery já disponível
$(document).ready(function () {
    // inicializações
});

// Confirmação de deleção
$('[data-confirm]').on('click', function (e) {
    if (!confirm($(this).data('confirm'))) {
        e.preventDefault();
    }
});
```

---

## 10. SEGURANÇA

### 10.1 Regras obrigatórias
- **XSS**: Sempre `Html::encode($variavel)` ao exibir dados de usuário em views.
- **CSRF**: Sempre manter `Html::csrfMetaTags()` no layout. Nunca desabilitar CSRF.
- **SQL Injection**: Nunca concatenar variáveis em SQL. Sempre usar Query Builder ou ActiveRecord.
- **Autenticação**: Sempre usar `AccessControl` em todos os controllers.
- **Senhas**: Nunca armazenar senha em plain text. Usar `Yii::$app->security->generatePasswordHash()`.
- **Tokens**: Nunca commitar `cookieValidationKey` no repositório público.
- **Dados sensíveis**: Credenciais de produção sempre em variáveis de ambiente, nunca no código.

### 10.2 Exemplo correto de hash de senha
```php
// Salvar:
$model->password_hash = Yii::$app->security->generatePasswordHash($plainPassword);

// Verificar:
$isValid = Yii::$app->security->validatePassword($plainPassword, $model->password_hash);
```

### 10.3 Operações permitidas por papel
```php
// Sempre usar AccessControl::behaviors()
'rules' => [
    ['allow' => true, 'roles' => ['?'],  'actions' => ['index', 'view']],
    ['allow' => true, 'roles' => ['@'],  'actions' => ['create', 'update']],
    ['allow' => true, 'roles' => ['admin'], 'actions' => ['delete']],
],
```

---

## 11. TESTES — CODECEPTION

### 11.1 Estrutura
```
tests/
├── unit/           → Testa classes isoladas (Models, helpers)
├── functional/     → Testa fluxos HTTP sem browser real
└── acceptance/     → Testa com browser (Selenium/PhpBrowser)
```

### 11.2 Comandos
```bash
# Rodar todos os testes:
php vendor/bin/codecept run

# Rodar suite específica:
php vendor/bin/codecept run unit
php vendor/bin/codecept run functional

# Rodar arquivo específico:
php vendor/bin/codecept run unit models/ReclamacaoTest.php

# Gerar novo Cest:
php vendor/bin/codecept generate:cest unit models/Reclamacao
```

### 11.3 Exemplo de Unit Test
```php
<?php

namespace tests\unit\models;

use app\models\Reclamacao;
use Codeception\Test\Unit;

class ReclamacaoTest extends Unit
{
    public function testValidacaoTituloObrigatorio(): void
    {
        $model = new Reclamacao();
        $model->descricao = 'Descrição válida';
        // título não preenchido

        $this->assertFalse($model->validate());
        $this->assertArrayHasKey('titulo', $model->errors);
    }

    public function testStatusPadraoEhPendente(): void
    {
        $model = new Reclamacao();
        $this->assertEquals(Reclamacao::STATUS_PENDENTE, $model->status);
    }
}
```

---

## 12. CONFIGURAÇÃO E AMBIENTE

### 12.1 Ambientes disponíveis
| Env     | Arquivo           | Modo Debug | Banco           |
|---------|-------------------|------------|-----------------|
| dev     | `web/index.php`   | true       | yii2basic       |
| test    | `web/index-test.php` | true    | yii2basic_test  |
| prod    | `web/index.php`   | false      | reclamaMunicipal|

### 12.2 Constantes de ambiente
```php
// Desenvolvimento (padrão)
defined('YII_DEBUG') or define('YII_DEBUG', true);
defined('YII_ENV')   or define('YII_ENV', 'dev');

// Produção (nunca deixar debug true)
defined('YII_DEBUG') or define('YII_DEBUG', false);
defined('YII_ENV')   or define('YII_ENV', 'prod');
```

### 12.3 Módulos Dev (apenas em DEV)
```php
// Habilitados automaticamente quando YII_ENV === 'dev'
// - yii2-debug: barra de depuração
// - yii2-gii: gerador de código
// Nunca habilitar em produção
```

---

## 13. CONSOLE COMMANDS

```php
<?php

declare(strict_types=1);

namespace app\commands;

use Yii;
use yii\console\Controller;
use yii\console\ExitCode;

class ReclamacaoController extends Controller
{
    public string $email = '';

    public function options($actionID): array
    {
        return array_merge(parent::options($actionID), ['email']);
    }

    /**
     * Notifica responsáveis sobre reclamações pendentes.
     */
    public function actionNotificarPendentes(): int
    {
        $total = \app\models\Reclamacao::findPendentes()->count();
        $this->stdout("Total de reclamações pendentes: {$total}\n");
        return ExitCode::OK;
    }
}
```

```bash
# Uso:
php yii reclamacao/notificar-pendentes
php yii reclamacao/notificar-pendentes --email=admin@example.com
```

---

## 14. COMMITS E VERSIONAMENTO

### 14.1 Padrão de mensagens de commit
```
feat: adiciona tela de criação de reclamação
fix: corrige validação de e-mail no formulário de contato
refactor: extrai lógica de notificação para service
test: adiciona testes unitários para model Reclamacao
migration: cria tabela reclamacao com índices
style: ajusta layout responsivo das cards
docs: atualiza README com instruções de instalação
```

### 14.2 .gitignore obrigatório
```
/runtime/
/web/assets/
/vendor/
.env
config/db.php       # em produção, usar variáveis de ambiente
```

---

## 15. PADRÕES PROIBIDOS

| Proibido                                         | Use em vez disso                        |
|--------------------------------------------------|-----------------------------------------|
| `echo $variavel` direto em view                  | `<?= Html::encode($variavel) ?>`        |
| SQL com concatenação de variáveis                | Query Builder / ActiveRecord            |
| Desabilitar CSRF (`enableCsrfValidation = false`)| Manter habilitado                       |
| `var_dump()`, `print_r()` em produção            | `Yii::debug()`, `Yii::error()`          |
| Senha em plain text no banco                     | `security->generatePasswordHash()`      |
| `die()`, `exit()` em Controllers/Models          | Retornar resposta Yii adequada          |
| Classes sem namespace                            | Sempre declarar namespace correto       |
| Estilo inline `style=""`                         | Classes Bootstrap 5 ou CSS externo      |
| Lógica de negócio em views                       | Models ou Services                      |
| `SELECT *` em queries críticas                   | Selecionar apenas colunas necessárias   |
