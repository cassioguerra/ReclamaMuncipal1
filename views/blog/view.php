<?php

/**
 * @var yii\web\View    $this
 * @var app\models\Blog $post
 * @var bool            $isGestor
 * @var string          $wrapOpen
 * @var string          $wrapClose
 */

use yii\bootstrap5\Html;
use yii\helpers\Url;

$this->title = $post->titulo;
?>

<?= $wrapOpen ?>

<!-- Banner / Hero do post -->
<div class="blog-view-banner" style="background-image:url('<?= Html::encode($post->urlBanner()) ?>');">
    <div class="blog-view-banner-overlay">
        <div class="container">
            <h1 class="blog-view-titulo"><?= Html::encode($post->titulo) ?></h1>
            <p class="blog-view-meta">
                <?php
                    $meses = ['janeiro','fevereiro','março','abril','maio','junho','julho','agosto','setembro','outubro','novembro','dezembro'];
                    $ts    = strtotime((string) $post->created_at);
                    echo '<i class="bi bi-calendar3 me-2"></i>' . date('d', $ts) . ' de ' . $meses[date('n', $ts) - 1] . ' de ' . date('Y', $ts);
                ?>
                <?php if ($post->cidadao): ?>
                &nbsp;·&nbsp;<i class="bi bi-person-fill me-2"></i><?= Html::encode($post->cidadao->nome_completo) ?>
                <?php endif ?>
            </p>
        </div>
    </div>
</div>

<!-- Conteúdo -->
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">

            <!-- Resumo em destaque -->
            <div class="blog-view-resumo">
                <i class="bi bi-quote me-2 text-success"></i><?= Html::encode($post->resumo) ?>
            </div>

            <!-- Corpo do post -->
            <div class="blog-view-conteudo">
                <?= nl2br(Html::encode((string) $post->conteudo)) ?>
            </div>

            <!-- Ações do gestor -->
            <?php if ($isGestor): ?>
            <div class="d-flex gap-2 mt-4 pt-3 border-top">
                <?= Html::a(
                    '<i class="bi bi-pencil me-1"></i>Editar Post',
                    Url::to(['/blog/update', 'id' => $post->id]),
                    ['class' => 'btn btn-outline-secondary btn-sm']
                ) ?>
                <?= Html::beginForm(Url::to(['/blog/delete', 'id' => $post->id]), 'post') ?>
                <?= Html::submitButton('<i class="bi bi-trash me-1"></i>Excluir Post', [
                    'class'        => 'btn btn-outline-danger btn-sm',
                    'data-confirm' => 'Excluir este post permanentemente?',
                ]) ?>
                <?= Html::endForm() ?>
            </div>
            <?php endif ?>

            <!-- Navegação -->
            <div class="d-flex gap-3 mt-4">
                <?= Html::a(
                    '<i class="bi bi-grid-3x3-gap me-1"></i>Ver todos os posts',
                    Url::to(['/blog/index']),
                    ['class' => 'btn btn-outline-success btn-sm']
                ) ?>
                <?= Html::a(
                    '<i class="bi bi-house-fill me-1"></i>Início',
                    Url::to(['/site/index']),
                    ['class' => 'btn btn-outline-secondary btn-sm']
                ) ?>
            </div>

        </div>
    </div>
</div>

<?= $wrapClose ?>
