<?php

/** @var yii\web\View $this */
/** @var app\models\Contato $model */
/** @var bool $editando */

use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;
use yii\helpers\Url;

$editando = $editando ?? false;
$fotoUrl  = $model->getFotoUrl();
?>

<div class="form-card">
    <?php $form = ActiveForm::begin([
        'action' => $editando ? ['/contato/editar', 'id' => $model->id] : ['/contato/criar'],
        'options' => [
            'enctype' => 'multipart/form-data',
            'class'   => 'row g-4',
        ],
        'fieldConfig' => [
            'options'      => ['class' => 'mb-0'],
            'labelOptions' => ['class' => 'form-label fw-semibold'],
            'inputOptions' => ['class' => 'form-control'],
            'errorOptions' => ['class' => 'invalid-feedback d-block'],
        ],
    ]); ?>

    <!-- Nome -->
    <div class="col-md-8">
        <?= $form->field($model, 'nome', [
            'inputOptions' => ['class' => 'form-control form-control-lg', 'placeholder' => 'Nome da secretaria, departamento ou pessoa'],
        ])->label('<i class="bi bi-person-fill me-1 text-verde"></i>Nome <span class="text-danger">*</span>') ?>
    </div>

    <!-- Ordem -->
    <div class="col-md-4">
        <?= $form->field($model, 'ordem', [
            'inputOptions' => ['class' => 'form-control form-control-lg', 'min' => 0, 'type' => 'number'],
        ])->label('<i class="bi bi-sort-numeric-up me-1 text-verde"></i>Ordem de exibição') ?>
    </div>

    <!-- Cargo -->
    <div class="col-md-6">
        <?= $form->field($model, 'cargo', [
            'inputOptions' => ['class' => 'form-control', 'placeholder' => 'Ex.: Secretário, Coordenador, Atendimento'],
        ])->label('<i class="bi bi-briefcase me-1 text-verde"></i>Cargo / Função') ?>
    </div>

    <!-- Telefone -->
    <div class="col-md-6">
        <?= $form->field($model, 'telefone', [
            'inputOptions' => ['class' => 'form-control', 'placeholder' => '(00) 3300-0000'],
        ])->label('<i class="bi bi-telephone me-1 text-verde"></i>Telefone') ?>
    </div>

    <!-- E-mail -->
    <div class="col-12">
        <?= $form->field($model, 'email', [
            'inputOptions' => ['class' => 'form-control', 'placeholder' => 'email@municipio.gov.br', 'type' => 'email'],
        ])->label('<i class="bi bi-envelope me-1 text-verde"></i>E-mail') ?>
    </div>

    <!-- Descrição -->
    <div class="col-12">
        <?= $form->field($model, 'descricao', [
            'inputOptions' => ['class' => 'form-control', 'rows' => 3, 'placeholder' => 'Breve descrição das responsabilidades deste contato (max. 500 caracteres)'],
        ])->textarea()->label('<i class="bi bi-chat-text me-1 text-verde"></i>Descrição') ?>
    </div>

    <!-- Upload de Foto -->
    <div class="col-12">
        <label class="form-label fw-semibold">
            <i class="bi bi-image me-1 text-verde"></i>Foto
            <span class="text-muted fw-normal small"> — JPG, PNG ou WEBP · Máx. 5 MB</span>
        </label>

        <?php if ($editando && $fotoUrl): ?>
        <div class="mb-3 d-flex align-items-center gap-3">
            <img src="<?= Html::encode($fotoUrl) ?>"
                 alt="Foto atual"
                 style="width:72px;height:72px;object-fit:cover;border-radius:12px;border:2px solid #e2e8f0;">
            <span class="text-muted small">Foto atual. Upload abaixo substitui esta.</span>
        </div>
        <?php endif ?>

        <?= $form->field($model, 'fotoFile', [
            'options'      => ['class' => 'mb-0'],
            'inputOptions' => ['class' => 'form-control', 'accept' => 'image/jpeg,image/png,image/webp'],
        ])->fileInput()->label(false) ?>
    </div>

    <!-- Ativo -->
    <div class="col-12">
        <?= $form->field($model, 'ativo', [
            'options'      => ['class' => 'mb-0'],
            'template'     => "{input}\n{label}\n{error}",
            'inputOptions' => ['class' => 'form-check-input me-2'],
            'labelOptions' => ['class' => 'form-check-label fw-semibold'],
        ])->checkbox()->label('<i class="bi bi-eye-fill me-1 text-verde"></i>Ativo (visível na página pública)') ?>
    </div>

    <!-- Botões -->
    <div class="col-12 d-flex gap-3 mt-2">
        <?= Html::a(
            '<i class="bi bi-x-circle me-2"></i>Cancelar',
            Url::to(['/contato/index']),
            ['class' => 'btn btn-outline-secondary px-4']
        ) ?>
        <?= Html::submitButton(
            '<i class="bi bi-check-circle-fill me-2"></i>' . ($editando ? 'Salvar Alterações' : 'Criar Contato'),
            ['class' => 'btn btn-verde-principal px-5']
        ) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>
