<?php

/** @var yii\web\View $this */
/** @var app\models\Cidadao $model */

use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;
use yii\helpers\Url;

$this->title = 'Criar Conta';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="pagina-auth">
    <div class="auth-card auth-card-lg">

        <div class="auth-header">
            <div class="auth-icone"><i class="bi bi-person-plus-fill"></i></div>
            <h1 class="auth-titulo">Criar Conta</h1>
            <p class="auth-subtitulo">Cadastre-se para registrar e acompanhar suas reclamações</p>
        </div>

        <div class="auth-body">
            <?php $form = ActiveForm::begin([
                'id'          => 'registrar-form',
                'options'     => ['class' => 'auth-form'],
                'fieldConfig' => [
                    'options'      => ['class' => 'mb-3'],
                    'labelOptions' => ['class' => 'form-label fw-semibold'],
                    'inputOptions' => ['class' => 'form-control form-control-lg'],
                    'errorOptions' => ['class' => 'invalid-feedback d-block'],
                ],
            ]); ?>

            <?= $form->field($model, 'nome_completo', [
                'inputOptions' => ['class' => 'form-control form-control-lg', 'placeholder' => 'Seu nome completo'],
            ])->label('<i class="bi bi-person me-1 text-verde"></i>Nome Completo') ?>

            <?= $form->field($model, 'email', [
                'inputOptions' => ['class' => 'form-control form-control-lg', 'placeholder' => 'seu@email.com.br'],
            ])->input('email')->label('<i class="bi bi-envelope me-1 text-verde"></i>E-mail') ?>

            <?= $form->field($model, 'telefone', [
                'inputOptions' => ['class' => 'form-control form-control-lg', 'placeholder' => '(00) 90000-0000', 'maxlength' => '16'],
            ])->input('tel')->label('<i class="bi bi-telephone me-1 text-verde"></i>Telefone <span class="text-muted fw-normal small">(opcional)</span>') ?>

            <?= $form->field($model, 'username', [
                'inputOptions' => ['class' => 'form-control form-control-lg', 'placeholder' => 'nome.sobrenome (sem espaços)'],
            ])->label('<i class="bi bi-at me-1 text-verde"></i>Nome de Usuário') ?>

            <div class="row g-3 mb-1">
                <div class="col-sm-6">
                    <?= $form->field($model, 'senha', [
                        'options'      => ['class' => 'mb-0'],
                        'inputOptions' => ['class' => 'form-control form-control-lg', 'placeholder' => 'Mínimo 8 caracteres'],
                    ])->passwordInput()->label('<i class="bi bi-lock me-1 text-verde"></i>Senha') ?>
                </div>
                <div class="col-sm-6">
                    <?= $form->field($model, 'senha_confirmar', [
                        'options'      => ['class' => 'mb-0'],
                        'inputOptions' => ['class' => 'form-control form-control-lg', 'placeholder' => 'Repita a senha'],
                    ])->passwordInput()->label('<i class="bi bi-lock-fill me-1 text-verde"></i>Confirmar Senha') ?>
                </div>
            </div>

            <div class="mb-4 mt-3">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="lgpd" required>
                    <label class="form-check-label text-muted small" for="lgpd">
                        Concordo com a
                        <a href="#" class="link-verde">Política de Privacidade</a>
                        e autorizo o tratamento dos meus dados conforme a LGPD.
                    </label>
                </div>
            </div>

            <?= Html::submitButton(
                '<i class="bi bi-person-check-fill me-2"></i>Criar Minha Conta',
                ['class' => 'btn btn-auth-principal w-100']
            ) ?>

            <?php ActiveForm::end(); ?>
        </div>

        <div class="auth-footer">
            <span class="text-muted">Já tem conta?</span>
            <?= Html::a('<strong>Entrar</strong>', Url::to(['/site/login']), ['class' => 'auth-link-criar']) ?>
        </div>

    </div>
</div>
