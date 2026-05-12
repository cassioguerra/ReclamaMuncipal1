<?php

/**
 * @var yii\web\View    $this
 * @var app\models\Blog $model
 * @var bool            $isEdicao  true quando for edição
 */

use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;
use yii\helpers\Url;

$isEdicao = $isEdicao ?? false;
?>

<?= Html::beginForm('', 'post', ['enctype' => 'multipart/form-data', 'id' => 'form-blog']) ?>

<?php $form = ActiveForm::begin([
    'id'      => 'form-blog-af',
    'options' => ['enctype' => 'multipart/form-data'],
]) ?>

<div class="row g-4">

    <!-- Coluna principal -->
    <div class="col-lg-8">

        <!-- Título -->
        <?= $form->field($model, 'titulo')->textInput([
            'maxlength' => true,
            'class'     => 'form-control',
            'placeholder' => 'Ex: Obras na Avenida Central concluídas',
        ])->label('Título do Post <span class="text-danger">*</span>', ['encode' => false]) ?>

        <!-- Resumo -->
        <?= $form->field($model, 'resumo')->textarea([
            'rows'      => 3,
            'maxlength' => true,
            'class'     => 'form-control',
            'placeholder' => 'Breve descrição que aparece no carrossel da página inicial (máx. 500 caracteres).',
        ])->label('Resumo <span class="text-danger">*</span>', ['encode' => false]) ?>

        <!-- Conteúdo -->
        <?= $form->field($model, 'conteudo')->textarea([
            'rows'  => 14,
            'class' => 'form-control',
            'placeholder' => 'Escreva o conteúdo completo do post aqui...',
        ])->label('Conteúdo <span class="text-danger">*</span>', ['encode' => false]) ?>

    </div>

    <!-- Coluna lateral -->
    <div class="col-lg-4">

        <!-- Foto de Capa -->
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-white fw-semibold py-2" style="border-bottom:2px solid #e8f5ec;">
                <i class="bi bi-image me-2 text-success"></i>Foto de Capa
                <span class="text-danger ms-1">*</span>
            </div>
            <div class="card-body">
                <?php if ($isEdicao && $model->foto_capa): ?>
                <div class="mb-2">
                    <img src="<?= Html::encode($model->urlCapa()) ?>" class="img-fluid rounded" style="max-height:150px;object-fit:cover;width:100%;" alt="Capa atual">
                    <p class="text-muted small mt-1 mb-0">Imagem atual — envie nova para substituir.</p>
                </div>
                <?php endif ?>
                <?= $form->field($model, 'foto_capa_file')->fileInput([
                    'accept' => 'image/jpeg,image/png,image/webp',
                ])->label($isEdicao ? 'Nova foto de capa (opcional)' : 'Selecionar arquivo') ?>
                <p class="text-muted small mb-0"><i class="bi bi-info-circle me-1"></i>JPG, PNG ou WebP · máx. 5 MB</p>
            </div>
        </div>

        <!-- Foto de Apresentação (banner) -->
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-white fw-semibold py-2" style="border-bottom:2px solid #e8f5ec;">
                <i class="bi bi-card-image me-2 text-success"></i>Foto de Apresentação
                <span class="text-danger ms-1">*</span>
            </div>
            <div class="card-body">
                <?php if ($isEdicao && $model->foto_banner): ?>
                <div class="mb-2">
                    <img src="<?= Html::encode($model->urlBanner()) ?>" class="img-fluid rounded" style="max-height:150px;object-fit:cover;width:100%;" alt="Banner atual">
                    <p class="text-muted small mt-1 mb-0">Imagem atual — envie nova para substituir.</p>
                </div>
                <?php endif ?>
                <?= $form->field($model, 'foto_banner_file')->fileInput([
                    'accept' => 'image/jpeg,image/png,image/webp',
                ])->label($isEdicao ? 'Nova foto de apresentação (opcional)' : 'Selecionar arquivo') ?>
                <p class="text-muted small mb-0"><i class="bi bi-info-circle me-1"></i>Exibida no topo da página do post. JPG, PNG ou WebP · máx. 5 MB</p>
            </div>
        </div>

        <!-- Publicação -->
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-white fw-semibold py-2" style="border-bottom:2px solid #e8f5ec;">
                <i class="bi bi-toggle-on me-2 text-success"></i>Publicação
            </div>
            <div class="card-body">
                <div class="form-check form-switch">
                    <?= Html::activeCheckbox($model, 'ativo', [
                        'class'       => 'form-check-input',
                        'id'          => 'blog-ativo',
                        'value'       => 1,
                        'uncheck'     => 0,
                    ]) ?>
                    <label class="form-check-label" for="blog-ativo">Publicar imediatamente</label>
                </div>
            </div>
        </div>

        <!-- Botões -->
        <div class="d-grid gap-2">
            <?= Html::submitButton(
                '<i class="bi bi-check-lg me-2"></i>' . ($isEdicao ? 'Salvar Alterações' : 'Publicar Post'),
                ['class' => 'btn btn-success']
            ) ?>
            <?= Html::a(
                '<i class="bi bi-x-lg me-2"></i>Cancelar',
                Url::to(['/blog/index']),
                ['class' => 'btn btn-outline-secondary']
            ) ?>
        </div>

    </div>

</div>

<?php ActiveForm::end() ?>
