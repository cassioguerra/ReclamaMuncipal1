<?php

/** @var yii\web\View $this */
/** @var string $content */

use app\assets\AppAsset;
use app\widgets\Alert;
use yii\bootstrap5\Breadcrumbs;
use yii\bootstrap5\Html;
use yii\bootstrap5\Nav;
use yii\bootstrap5\NavBar;
use yii\helpers\Url;

AppAsset::register($this);

$this->registerCssFile('https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css', [
    'position' => \yii\web\View::POS_HEAD,
]);

$this->registerCsrfMetaTags();
$this->registerMetaTag(['charset' => Yii::$app->charset], 'charset');
$this->registerMetaTag(['name' => 'viewport', 'content' => 'width=device-width, initial-scale=1, shrink-to-fit=no']);
$this->registerMetaTag(['name' => 'description', 'content' => $this->params['meta_description'] ?? 'Sistema de Reclamações Municipais — registre e acompanhe suas solicitações ao município.']);
$this->registerLinkTag(['rel' => 'icon', 'type' => 'image/x-icon', 'href' => Yii::getAlias('@web/favicon.ico')]);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="pt-BR" class="h-100">
<head>
    <title><?= Html::encode($this->title) ?> — ReclamaMunicipal</title>
    <?php $this->head() ?>
</head>
<body class="d-flex flex-column h-100">
<?php $this->beginBody() ?>

<!-- ════════════════════════════════════════
     CABEÇALHO FIXO  (barra + navbar em um único container)
     ════════════════════════════════════════ -->
<header id="site-header">

    <!-- Barra Superior -->
    <div class="barra-superior">
        <div class="container d-flex justify-content-between align-items-center">
            <span>
                <i class="bi bi-geo-alt-fill me-1"></i>
                Sistema de Reclamações Municipais
            </span>
            <span>
                <?php if (!Yii::$app->user->isGuest): ?>
                    <i class="bi bi-person-check-fill me-1"></i>
                    Cidadão: <strong><?= Html::encode(Yii::$app->user->identity->username) ?></strong>
                <?php else: ?>
                    <i class="bi bi-shield-fill-check me-1"></i> Acesso Seguro
                <?php endif ?>
            </span>
        </div>
    </div>

    <!-- Navbar Principal (sem fixed-top — o pai #site-header é fixo) -->
    <?php
    NavBar::begin([
        'brandLabel' => '<span class="brand-icone"><i class="bi bi-building-fill"></i></span>'
                      . '<span class="brand-nome">Reclama<strong>Municipal</strong></span>',
        'brandUrl'   => Yii::$app->homeUrl,
        'options'    => ['class' => 'navbar-expand-lg navbar-dark navbar-principal'],
    ]);
    echo Nav::widget([
        'options'       => ['class' => 'navbar-nav ms-auto align-items-lg-center gap-lg-1'],
        'encodeLabels'  => false,
        'items' => [
            ['label' => '<i class="bi bi-house-fill me-1"></i>Início',            'url' => ['/site/index'],     'encode' => false],
            ['label' => '<i class="bi bi-megaphone-fill me-1"></i>Reclamar',      'url' => ['/site/reclamar'],  'encode' => false],
            ['label' => '<i class="bi bi-search me-1"></i>Consultar',             'url' => ['/site/consultar'], 'encode' => false],
            ['label' => '<i class="bi bi-newspaper me-1"></i>Blog',               'url' => ['/blog/index'],     'encode' => false],
            ['label' => '<i class="bi bi-person-lines-fill me-1"></i>Contatos',   'url' => ['/contato/index'],  'encode' => false],
            ['label' => '<i class="bi bi-envelope-fill me-1"></i>Fale Conosco',   'url' => ['/site/fale-conosco'], 'encode' => false],
            Yii::$app->user->isGuest
                ? '<li class="nav-item ms-lg-3">'
                    . Html::a(
                        '<i class="bi bi-person-fill me-1"></i>Entrar',
                        ['/site/login'],
                        ['class' => 'btn-nav-entrar']
                    )
                    . '</li>'
                : '<li class="nav-item ms-lg-3 dropdown">'
                    . '<button class="btn-nav-usuario dropdown-toggle" type="button"'
                    . ' data-bs-toggle="dropdown" aria-expanded="false">'
                    . '<span class="nav-avatar">'
                    . strtoupper(substr(Yii::$app->user->identity->username, 0, 1))
                    . '</span>'
                    . '<span class="ms-2">' . Html::encode(Yii::$app->user->identity->username) . '</span>'
                    . '</button>'
                    . '<ul class="dropdown-menu dropdown-menu-end dropdown-nav-usuario">'
                    . '<li class="dropdown-header-item">'
                    . '<div class="dropdown-avatar">'
                    . strtoupper(substr(Yii::$app->user->identity->username, 0, 1))
                    . '</div>'
                    . '<div><strong>' . Html::encode(Yii::$app->user->identity->username) . '</strong>'
                    . '<br><small class="text-muted">Cidadão</small></div>'
                    . '</li>'
                    . '<li><hr class="dropdown-divider"></li>'
                    . '<li>'
                    . Html::a(
                        '<i class="bi bi-person-circle me-2"></i>Meu Perfil',
                        ['/site/perfil'],
                        ['class' => 'dropdown-item']
                    )
                    . '</li>'
                    . '<li>'
                    . Html::a(
                        '<i class="bi bi-megaphone me-2"></i>Minhas Reclamações',
                        ['/site/consultar'],
                        ['class' => 'dropdown-item']
                    )
                    . '</li>'
                    . '<li><hr class="dropdown-divider"></li>'
                    . '<li>'
                    . Html::beginForm(['/site/logout'])
                    . Html::submitButton(
                        '<i class="bi bi-box-arrow-left me-2"></i>Sair da Conta',
                        ['class' => 'dropdown-item dropdown-item-danger border-0 bg-transparent w-100 text-start']
                    )
                    . Html::endForm()
                    . '</li>'
                    . '</ul>'
                    . '</li>',
        ],
    ]);
    NavBar::end();
    ?>

</header>

<!-- ════════════════════════════════════════
     CONTEÚDO PRINCIPAL
     ════════════════════════════════════════ -->
<main id="main" class="flex-shrink-0" role="main">
    <?php if (!empty($this->params['breadcrumbs'])): ?>
        <div class="breadcrumb-wrapper">
            <div class="container">
                <?= Breadcrumbs::widget([
                    'links'               => $this->params['breadcrumbs'],
                    'options'             => ['class' => 'breadcrumb mb-0 py-2'],
                    'homeLink'            => ['label' => '<i class="bi bi-house-fill me-1"></i>Início', 'url' => Yii::$app->homeUrl, 'encode' => false],
                    'itemTemplate'        => "<li class=\"breadcrumb-item\">{link}</li>\n",
                    'activeItemTemplate'  => "<li class=\"breadcrumb-item active\">{link}</li>\n",
                ]) ?>
            </div>
        </div>
    <?php endif ?>

    <?= Alert::widget() ?>
    <?= $content ?>
</main>

<!-- ════════════════════════════════════════
     RODAPÉ
     ════════════════════════════════════════ -->
<footer id="footer" class="mt-auto footer-principal">
    <div class="footer-top">
        <div class="container">
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="footer-brand">
                        <i class="bi bi-building-fill footer-brand-icone"></i>
                        <span>Reclama<strong>Municipal</strong></span>
                    </div>
                    <p class="footer-texto mt-3">
                        Sistema oficial de registro e acompanhamento de reclamações municipais.
                        Seu direito de ser ouvido, garantido.
                    </p>
                </div>
                <div class="col-md-4">
                    <h5 class="footer-titulo">Acesso Rápido</h5>
                    <ul class="footer-links">
                        <li><a href="<?= Yii::$app->homeUrl ?>"><i class="bi bi-house me-2"></i>Início</a></li>
                        <li><a href="<?= Url::to(['/site/reclamar']) ?>"><i class="bi bi-megaphone me-2"></i>Registrar Reclamação</a></li>
                        <li><a href="<?= Url::to(['/site/consultar']) ?>"><i class="bi bi-search me-2"></i>Consultar Status</a></li>
                        <li><a href="<?= Url::to(['/blog/index']) ?>"><i class="bi bi-newspaper me-2"></i>Blog</a></li>
                        <li><a href="<?= Url::to(['/site/fale-conosco']) ?>"><i class="bi bi-envelope me-2"></i>Fale Conosco</a></li>
                    </ul>
                </div>
                <div class="col-md-4">
                    <h5 class="footer-titulo">Informações</h5>
                    <ul class="footer-links">
                        <li><i class="bi bi-clock me-2"></i>Atendimento: 08h às 17h</li>
                        <li><i class="bi bi-calendar-check me-2"></i>Segunda a Sexta</li>
                        <li><i class="bi bi-shield-check me-2"></i>Dados protegidos — LGPD</li>
                        <li><i class="bi bi-lightning-charge me-2"></i>Resposta em até 72h</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <div class="footer-bottom">
        <div class="container d-flex flex-column flex-md-row justify-content-between align-items-center">
            <span>&copy; <?= date('Y') ?> ReclamaMunicipal &mdash; Todos os direitos reservados</span>
            <span class="mt-2 mt-md-0 footer-powered"><?= Yii::powered() ?></span>
        </div>
    </div>
</footer>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
