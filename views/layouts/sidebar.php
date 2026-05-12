<?php

/** @var yii\web\View $this */
/** @var string $content */

use app\assets\AppAsset;
use app\widgets\Alert;
use yii\bootstrap5\Html;
use yii\helpers\Url;

AppAsset::register($this);

$this->registerCssFile('https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css', [
    'position' => \yii\web\View::POS_HEAD,
]);

$this->registerCsrfMetaTags();
$this->registerMetaTag(['charset' => Yii::$app->charset], 'charset');
$this->registerMetaTag(['name' => 'viewport', 'content' => 'width=device-width, initial-scale=1, shrink-to-fit=no']);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <title><?= Html::encode($this->title) ?> — ReclamaMunicipal</title>
    <?php $this->head() ?>
    <style>
    /* ── Sidebar Layout ─────────────────────────────────── */
    :root {
        --sb-width: 260px;
        --sb-bg:    #1a5c2a;
        --sb-hover: rgba(255,255,255,0.10);
        --sb-active:#2d9e4a;
        --sb-text:  rgba(255,255,255,0.88);
        --sb-muted: rgba(255,255,255,0.50);
    }
    body { overflow-x: hidden; background: #f4f6f9; }

    /* ── SIDEBAR ─────────────────────────────────────────── */
    #sidebar {
        position: fixed;
        top: 0; left: 0; bottom: 0;
        width: var(--sb-width);
        background: var(--sb-bg);
        display: flex;
        flex-direction: column;
        z-index: 200;
        transition: transform .25s ease;
    }

    /* Brand */
    .sb-brand {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 22px 20px 20px;
        border-bottom: 1px solid rgba(255,255,255,0.10);
        text-decoration: none !important;
    }
    .sb-brand-icon {
        width: 38px; height: 38px;
        background: rgba(255,255,255,0.15);
        border: 1.5px solid rgba(255,255,255,0.30);
        border-radius: 10px;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.1rem; color: #fff; flex-shrink: 0;
    }
    .sb-brand-text { font-size: 1rem; font-weight: 600; color: #fff; }
    .sb-brand-text strong { font-weight: 800; }

    /* Nav */
    .sb-nav { flex: 1; padding: 14px 12px; overflow-y: auto; }
    .sb-section-title {
        font-size: 0.68rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 1px;
        color: var(--sb-muted);
        padding: 16px 10px 8px;
    }
    .sb-link {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 10px 12px;
        border-radius: 8px;
        color: var(--sb-text) !important;
        text-decoration: none !important;
        font-size: 0.875rem;
        font-weight: 500;
        transition: background .15s, color .15s;
        margin-bottom: 2px;
    }
    .sb-link:hover { background: var(--sb-hover); color: #fff !important; }
    .sb-link.active {
        background: var(--sb-active);
        color: #fff !important;
        font-weight: 600;
    }
    .sb-link .bi { font-size: 1rem; flex-shrink: 0; width: 20px; text-align: center; }
    .sb-badge {
        margin-left: auto;
        background: rgba(255,255,255,0.20);
        color: #fff;
        font-size: 0.7rem;
        font-weight: 700;
        padding: 1px 7px;
        border-radius: 50px;
    }

    /* User footer */
    .sb-footer {
        border-top: 1px solid rgba(255,255,255,0.10);
        padding: 14px 16px;
    }
    .sb-user {
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .sb-avatar {
        width: 36px; height: 36px;
        background: rgba(255,255,255,0.20);
        border: 1.5px solid rgba(255,255,255,0.30);
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-size: 0.9rem; font-weight: 800; color: #fff;
        flex-shrink: 0;
    }
    .sb-user-info { flex: 1; min-width: 0; }
    .sb-user-name {
        font-size: 0.82rem; font-weight: 700; color: #fff;
        white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
    }
    .sb-user-role { font-size: 0.72rem; color: var(--sb-muted); }
    .sb-logout {
        background: none; border: none; padding: 6px;
        color: var(--sb-muted); cursor: pointer;
        border-radius: 6px; transition: .15s;
        font-size: 1rem;
    }
    .sb-logout:hover { background: rgba(255,255,255,0.10); color: #fff; }

    /* ── ÁREA DE CONTEÚDO ────────────────────────────────── */
    #sb-content {
        margin-left: var(--sb-width);
        min-height: 100vh;
        display: flex;
        flex-direction: column;
    }

    /* Topbar */
    .sb-topbar {
        background: #fff;
        border-bottom: 1px solid #e2e8f0;
        padding: 14px 24px;
        display: flex;
        align-items: center;
        gap: 12px;
        position: sticky;
        top: 0;
        z-index: 100;
    }
    .sb-topbar-title { font-size: 1.05rem; font-weight: 700; color: #1e293b; flex: 1; }
    .sb-topbar-btn {
        display: none; /* visível só no mobile */
        background: none; border: none; padding: 4px 8px;
        font-size: 1.3rem; color: #475569; cursor: pointer;
    }
    .sb-page-content { padding: 28px 24px; flex: 1; }

    /* ── MOBILE ─────────────────────────────────────────── */
    @media (max-width: 991px) {
        #sidebar { transform: translateX(-100%); }
        #sidebar.open { transform: translateX(0); box-shadow: 4px 0 30px rgba(0,0,0,0.3); }
        #sb-content { margin-left: 0; }
        .sb-topbar-btn { display: flex; align-items: center; }
        .sb-overlay {
            display: none;
            position: fixed; inset: 0;
            background: rgba(0,0,0,0.4);
            z-index: 199;
        }
        .sb-overlay.open { display: block; }
    }
    </style>
</head>
<body>
<?php $this->beginBody() ?>

<!-- Overlay mobile -->
<div class="sb-overlay" id="sbOverlay" onclick="closeSidebar()"></div>

<!-- ════════════════════════════════════════
     SIDEBAR
     ════════════════════════════════════════ -->
<nav id="sidebar">

    <!-- Brand -->
    <a class="sb-brand" href="<?= Yii::$app->homeUrl ?>">
        <span class="sb-brand-icon"><i class="bi bi-building-fill"></i></span>
        <span class="sb-brand-text">Reclama<strong>Municipal</strong></span>
    </a>

    <!-- Nav links -->
    <div class="sb-nav">
        <div class="sb-section-title">Menu</div>

        <a href="<?= Url::to(['/site/dashboard']) ?>"
           class="sb-link <?= Yii::$app->controller->action->id === 'dashboard' ? 'active' : '' ?>">
            <i class="bi bi-speedometer2"></i> Dashboard
        </a>

        <a href="<?= Url::to(['/site/reclamar']) ?>"
           class="sb-link <?= Yii::$app->controller->action->id === 'reclamar' ? 'active' : '' ?>">
            <i class="bi bi-megaphone-fill"></i> Nova Reclamação
        </a>

        <a href="<?= Url::to(['/site/consultar']) ?>"
           class="sb-link <?= Yii::$app->controller->action->id === 'consultar' ? 'active' : '' ?>">
            <i class="bi bi-search"></i> Minhas Reclamações
        </a>

        <div class="sb-section-title">Conta</div>

        <a href="<?= Url::to(['/site/perfil']) ?>"
           class="sb-link <?= Yii::$app->controller->action->id === 'perfil' ? 'active' : '' ?>">
            <i class="bi bi-person-circle"></i> Meu Perfil
        </a>

        <div class="sb-section-title">Site</div>

        <a href="<?= Url::to(['/contato/index']) ?>"
           class="sb-link <?= Yii::$app->controller->id === 'contato' ? 'active' : '' ?>">
            <i class="bi bi-person-lines-fill"></i> Contatos
        </a>

        <a href="<?= Url::to(['/site/contact']) ?>" class="sb-link">
            <i class="bi bi-envelope-fill"></i> Fale Conosco
        </a>

        <?php if (!Yii::$app->user->isGuest && Yii::$app->user->identity->isGestor()): ?>
        <div class="sb-section-title">Gestão</div>
        <a href="<?= Url::to(['/gestao/index']) ?>"
           class="sb-link <?= Yii::$app->controller->id === 'gestao' ? 'active' : '' ?>">
            <i class="bi bi-clipboard2-check-fill"></i> Reclamações
        </a>
        <a href="<?= Url::to(['/contato/criar']) ?>"
           class="sb-link <?= Yii::$app->controller->id === 'contato' && Yii::$app->controller->action->id === 'criar' ? 'active' : '' ?>">
            <i class="bi bi-plus-circle-fill"></i> Novo Contato
        </a>
        <div class="sb-section-title">Blog</div>
        <a href="<?= Url::to(['/blog/index']) ?>"
           class="sb-link <?= Yii::$app->controller->id === 'blog' && Yii::$app->controller->action->id === 'index' ? 'active' : '' ?>">
            <i class="bi bi-newspaper"></i> Ver Posts
        </a>
        <a href="<?= Url::to(['/blog/create']) ?>"
           class="sb-link <?= Yii::$app->controller->id === 'blog' && Yii::$app->controller->action->id === 'create' ? 'active' : '' ?>">
            <i class="bi bi-plus-circle-fill"></i> Novo Post
        </a>
        <?php endif ?>
    </div>

    <!-- User footer -->
    <div class="sb-footer">
        <?php if (!Yii::$app->user->isGuest): ?>
        <?php $identity = Yii::$app->user->identity; ?>
        <div class="sb-user">
            <div class="sb-avatar">
                <?= strtoupper(substr((string) $identity->username, 0, 1)) ?>
            </div>
            <div class="sb-user-info">
                <div class="sb-user-name"><?= Html::encode($identity->nome_completo ?: $identity->username) ?></div>
                <div class="sb-user-role">Cidadão</div>
            </div>
            <?= Html::beginForm(['/site/logout']) ?>
            <?= Html::submitButton('<i class="bi bi-box-arrow-right"></i>', [
                'class' => 'sb-logout',
                'title' => 'Sair',
            ]) ?>
            <?= Html::endForm() ?>
        </div>
        <?php else: ?>
        <div class="sb-user">
            <div class="sb-avatar" style="background:#6c757d;">
                <i class="bi bi-person" style="font-size:.9rem;"></i>
            </div>
            <div class="sb-user-info">
                <div class="sb-user-name">Visitante</div>
                <div class="sb-user-role">Não autenticado</div>
            </div>
            <?= Html::a('<i class="bi bi-box-arrow-in-right"></i>', ['/site/login'], [
                'class' => 'sb-logout',
                'title' => 'Entrar',
            ]) ?>
        </div>
        <?php endif ?>

</nav>

<!-- ════════════════════════════════════════
     ÁREA DE CONTEÚDO
     ════════════════════════════════════════ -->
<div id="sb-content">

    <!-- Topbar -->
    <div class="sb-topbar">
        <button class="sb-topbar-btn" onclick="openSidebar()" aria-label="Abrir menu">
            <i class="bi bi-list"></i>
        </button>
        <span class="sb-topbar-title"><?= Html::encode($this->title) ?></span>

        <?php if (!empty($this->params['topbar_actions'])): ?>
            <?= $this->params['topbar_actions'] ?>
        <?php endif ?>
    </div>

    <!-- Flash + conteúdo -->
    <div class="sb-page-content">
        <?= Alert::widget() ?>
        <?= $content ?>
    </div>

</div>

<script>
function openSidebar()  {
    document.getElementById('sidebar').classList.add('open');
    document.getElementById('sbOverlay').classList.add('open');
}
function closeSidebar() {
    document.getElementById('sidebar').classList.remove('open');
    document.getElementById('sbOverlay').classList.remove('open');
}
</script>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
