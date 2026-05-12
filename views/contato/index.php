<?php

/** @var yii\web\View $this */
/** @var app\models\Contato[] $contatos */

use app\models\Contato;
use yii\bootstrap5\Html;
use yii\helpers\Url;

$this->title = 'Contatos';
$this->params['breadcrumbs'][] = $this->title;

$isGestor = !Yii::$app->user->isGuest && Yii::$app->user->identity->isGestor();
?>

<!-- ── Cabeçalho da página ─────────────────────────────────────── -->
<div class="contatos-hero">
    <div class="container py-5">
        <div class="row align-items-center">
            <div class="col-lg-7">
                <span class="section-tag">Prefeitura Municipal</span>
                <h1 class="section-titulo mt-2">Fale com a Prefeitura</h1>
                <p class="section-subtitulo mb-0">
                    Encontre os contatos das secretarias e departamentos municipais.
                    Estamos prontos para atender você.
                </p>
            </div>
            <?php if ($isGestor): ?>
            <div class="col-lg-5 text-lg-end mt-4 mt-lg-0">
                <?= Html::a(
                    '<i class="bi bi-plus-circle-fill me-2"></i>Novo Contato',
                    Url::to(['/contato/criar']),
                    ['class' => 'btn btn-verde-principal btn-lg']
                ) ?>
            </div>
            <?php endif ?>
        </div>
    </div>
</div>

<!-- ── Cards de contatos ──────────────────────────────────────── -->
<div class="container py-5">

    <?php if (empty($contatos)): ?>
    <div class="text-center py-5">
        <i class="bi bi-person-lines-fill text-muted" style="font-size:3.5rem;opacity:.35"></i>
        <p class="text-muted mt-3 mb-1 fw-semibold">Nenhum contato cadastrado.</p>
        <?php if ($isGestor): ?>
        <p class="text-muted small mb-4">Adicione o primeiro contato para exibir aqui.</p>
        <?= Html::a(
            '<i class="bi bi-plus-circle-fill me-2"></i>Criar Contato',
            Url::to(['/contato/criar']),
            ['class' => 'btn btn-verde-principal']
        ) ?>
        <?php endif ?>
    </div>

    <?php else: ?>
    <div class="row g-4">
        <?php foreach ($contatos as $c): ?>
        <?php
            $inicial  = strtoupper(substr($c->nome, 0, 1));
            $fotoUrl  = $c->getFotoUrl();
        ?>
        <div class="col-sm-6 col-lg-4">
            <div class="card contato-card h-100 border-0 shadow-sm">

                <!-- Imagem / Placeholder -->
                <?php if ($fotoUrl): ?>
                <img src="<?= Html::encode($fotoUrl) ?>"
                     class="card-img-top contato-foto"
                     alt="Foto de <?= Html::encode($c->nome) ?>">
                <?php else: ?>
                <div class="contato-foto-placeholder">
                    <span class="contato-placeholder-inicial"><?= Html::encode($inicial) ?></span>
                </div>
                <?php endif ?>

                <!-- Corpo do card -->
                <div class="card-body d-flex flex-column">

                    <h5 class="card-title fw-bold mb-1" style="color:#1a5c2a;">
                        <?= Html::encode($c->nome) ?>
                    </h5>

                    <?php if ($c->cargo): ?>
                    <p class="text-muted small mb-2 fw-semibold">
                        <i class="bi bi-briefcase me-1" style="color:#237a38;"></i>
                        <?= Html::encode($c->cargo) ?>
                    </p>
                    <?php endif ?>

                    <?php if ($c->descricao): ?>
                    <p class="card-text text-muted small mb-3">
                        <?= Html::encode($c->descricao) ?>
                    </p>
                    <?php endif ?>

                    <div class="mt-auto">
                        <?php if ($c->email): ?>
                        <a href="mailto:<?= Html::encode($c->email) ?>"
                           class="d-flex align-items-center gap-2 text-muted small mb-1 contato-link">
                            <i class="bi bi-envelope-fill" style="color:#237a38;width:16px"></i>
                            <?= Html::encode($c->email) ?>
                        </a>
                        <?php endif ?>

                        <?php if ($c->telefone): ?>
                        <a href="tel:<?= Html::encode(preg_replace('/\D/', '', $c->telefone)) ?>"
                           class="d-flex align-items-center gap-2 text-muted small mb-2 contato-link">
                            <i class="bi bi-telephone-fill" style="color:#237a38;width:16px"></i>
                            <?= Html::encode($c->telefone) ?>
                        </a>
                        <?php endif ?>
                    </div>

                    <!-- Botões de gestão (apenas gestor) -->
                    <?php if ($isGestor): ?>
                    <div class="d-flex gap-2 mt-3 pt-3 border-top">
                        <?= Html::a(
                            '<i class="bi bi-pencil-fill me-1"></i>Editar',
                            Url::to(['/contato/editar', 'id' => $c->id]),
                            ['class' => 'btn btn-sm btn-outline-success flex-grow-1']
                        ) ?>
                        <?= Html::beginForm(['/contato/deletar', 'id' => $c->id], 'post', [
                            'style' => 'flex:1',
                            'onsubmit' => "return confirm('Excluir o contato \"" . addslashes(Html::encode($c->nome)) . "\"? Esta ação não pode ser desfeita.');",
                        ]) ?>
                        <?= Html::submitButton(
                            '<i class="bi bi-trash-fill me-1"></i>Excluir',
                            ['class' => 'btn btn-sm btn-outline-danger w-100']
                        ) ?>
                        <?= Html::endForm() ?>
                    </div>
                    <?php endif ?>

                </div>
            </div>
        </div>
        <?php endforeach ?>
    </div>
    <?php endif ?>

</div>

<style>
/* ── Contatos ──────────────────────────────────────────────── */
.contatos-hero {
    background: linear-gradient(135deg, var(--verde-escuro) 0%, var(--verde-medio) 100%);
    color: #fff;
    margin-top: -1px; /* elimina gap sob o header fixo */
}
.contatos-hero .section-tag  { background:rgba(255,255,255,.15); color:#fff; }
.contatos-hero .section-titulo   { color:#fff; font-size:2rem; }
.contatos-hero .section-subtitulo{ color:rgba(255,255,255,.82); }

.contato-card {
    border-radius: 14px !important;
    overflow: hidden;
    transition: transform .2s, box-shadow .2s;
}
.contato-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 32px rgba(26,92,42,.18) !important;
}

/* Foto real */
.contato-foto {
    width: 100%;
    height: 180px;
    object-fit: cover;
    object-position: center top;
}

/* Placeholder colorido */
.contato-foto-placeholder {
    height: 180px;
    background: linear-gradient(135deg, var(--verde-escuro) 0%, var(--verde-medio) 100%);
    display: flex;
    align-items: center;
    justify-content: center;
}
.contato-placeholder-inicial {
    font-size: 4rem;
    font-weight: 800;
    color: rgba(255,255,255,.50);
    line-height: 1;
    user-select: none;
}

.contato-link { text-decoration:none; transition:.15s; }
.contato-link:hover { color: var(--verde-principal) !important; }
.contato-link:hover i { color: var(--verde-escuro) !important; }
</style>
