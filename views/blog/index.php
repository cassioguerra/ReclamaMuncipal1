<?php

/**
 * @var yii\web\View      $this
 * @var app\models\Blog[] $posts
 * @var bool              $isGestor
 * @var string            $wrapOpen
 * @var string            $wrapClose
 */

use yii\bootstrap5\Html;
use yii\helpers\Url;

$this->title = 'Blog — Acontecimentos da Cidade';
?>

<?= $wrapOpen ?>

<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h2 class="fw-bold mb-1" style="color:#1a5c2a;">
            <i class="bi bi-newspaper me-2" style="color:#237a38;"></i>Blog da Cidade
        </h2>
        <p class="text-muted small mb-0">Notícias e acontecimentos do município</p>
    </div>
    <?php if ($isGestor): ?>
    <?= Html::a(
        '<i class="bi bi-plus-lg me-1"></i>Novo Post',
        Url::to(['/blog/create']),
        ['class' => 'btn btn-success']
    ) ?>
    <?php endif ?>
</div>

<?php if (empty($posts)): ?>
<div class="text-center py-5">
    <i class="bi bi-newspaper" style="font-size:3rem;color:#b8dfc5;"></i>
    <p class="text-muted mt-3 mb-0">Nenhum post publicado ainda.</p>
    <?php if ($isGestor): ?>
    <div class="mt-3">
        <?= Html::a('<i class="bi bi-plus-lg me-1"></i>Criar primeiro post', Url::to(['/blog/create']), ['class' => 'btn btn-success']) ?>
    </div>
    <?php endif ?>
</div>
<?php else: ?>
<div class="row g-4">
    <?php foreach ($posts as $post): ?>
    <div class="col-sm-6 col-lg-4">
        <div class="blog-card h-100">
            <div class="blog-card-img-wrap">
                <img
                    src="<?= Html::encode($post->urlCapa()) ?>"
                    alt="<?= Html::encode($post->titulo) ?>"
                    class="blog-card-img"
                    loading="lazy"
                >
            </div>
            <div class="blog-card-body">
                <p class="blog-card-meta">
                    <i class="bi bi-calendar3 me-1"></i><?= date('d/m/Y', strtotime((string) $post->created_at)) ?>
                    <?php if ($post->cidadao): ?>
                    &nbsp;·&nbsp;<i class="bi bi-person me-1"></i><?= Html::encode($post->cidadao->nome_completo) ?>
                    <?php endif ?>
                </p>
                <h5 class="blog-card-titulo"><?= Html::encode($post->titulo) ?></h5>
                <p class="blog-card-resumo"><?= Html::encode(mb_strimwidth((string) $post->resumo, 0, 120, '…')) ?></p>
                <div class="d-flex align-items-center justify-content-between mt-auto pt-2">
                    <?= Html::a(
                        '<i class="bi bi-arrow-right me-1"></i>Ler mais',
                        Url::to(['/blog/view', 'id' => $post->id]),
                        ['class' => 'btn btn-sm btn-outline-success']
                    ) ?>
                    <?php if ($isGestor): ?>
                    <div class="d-flex gap-2">
                        <?= Html::a('<i class="bi bi-pencil"></i>', Url::to(['/blog/update', 'id' => $post->id]), ['class' => 'btn btn-sm btn-outline-secondary', 'title' => 'Editar']) ?>
                        <?= Html::beginForm(Url::to(['/blog/delete', 'id' => $post->id]), 'post') ?>
                        <?= Html::submitButton('<i class="bi bi-trash"></i>', [
                            'class' => 'btn btn-sm btn-outline-danger',
                            'title' => 'Excluir',
                            'data-confirm' => 'Excluir este post? Esta ação não pode ser desfeita.',
                        ]) ?>
                        <?= Html::endForm() ?>
                    </div>
                    <?php endif ?>
                </div>
            </div>
        </div>
    </div>
    <?php endforeach ?>
</div>
<?php endif ?>

<div class="mt-5">
    <?= Html::a(
        '<i class="bi bi-house-fill me-2"></i>Voltar ao início',
        Url::to(['/site/index']),
        ['class' => 'btn btn-outline-secondary btn-sm']
    ) ?>
</div>

<?= $wrapClose ?>
