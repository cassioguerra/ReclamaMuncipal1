<?php

/** @var yii\web\View $this */
/** @var yii\bootstrap5\ActiveForm $form */
/** @var app\models\LoginForm $model */

use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;
use yii\helpers\Url;

$this->title = 'Entrar';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="pagina-auth">
    <div class="auth-card">

        <!-- Cabeçalho do card -->
        <div class="auth-header">
            <div class="auth-icone"><i class="bi bi-building-fill"></i></div>
            <h2 class="auth-titulo">Bem-vindo de volta</h2>
            <p class="auth-subtitulo">Acesse sua conta para registrar e acompanhar reclamações</p>
        </div>

        <!-- Formulário -->
        <div class="auth-body">
            <?php $form = ActiveForm::begin([
                'id'          => 'login-form',
                'options'     => ['class' => 'auth-form'],
                'fieldConfig' => [
                    'options'      => ['class' => 'mb-4'],
                    'labelOptions' => ['class' => 'form-label fw-semibold'],
                    'inputOptions' => ['class' => 'form-control form-control-lg'],
                    'errorOptions' => ['class' => 'invalid-feedback d-block'],
                ],
            ]); ?>

            <?= $form->field($model, 'username', [
                'inputOptions' => ['class' => 'form-control form-control-lg', 'placeholder' => 'E-mail ou nome de usuário', 'autofocus' => true],
            ])->label('<i class="bi bi-person me-1"></i>Usuário / E-mail') ?>

            <?= $form->field($model, 'password', [
                'inputOptions' => ['class' => 'form-control form-control-lg', 'placeholder' => '••••••••'],
            ])->passwordInput()->label('<i class="bi bi-lock me-1"></i>Senha') ?>

            <div class="d-flex justify-content-between align-items-center mb-4">
                <?= $form->field($model, 'rememberMe', [
                    'options'      => ['class' => 'mb-0'],
                    'template'     => '<div class="form-check">{input}{label}{error}</div>',
                    'labelOptions' => ['class' => 'form-check-label text-muted'],
                ])->checkbox(['class' => 'form-check-input'])->label('Lembrar-me') ?>
                <a href="#" class="auth-link-esqueci">Esqueci minha senha</a>
            </div>

            <?= Html::submitButton(
                '<i class="bi bi-box-arrow-in-right me-2"></i>Entrar na conta',
                ['class' => 'btn btn-auth-principal w-100', 'name' => 'login-button']
            ) ?>

            <?php ActiveForm::end(); ?>
        </div>

        <!-- Rodapé do card -->
        <div class="auth-footer">
            <span class="text-muted">Não tem uma conta?</span>
            <a href="<?= Url::to(['/site/registrar']) ?>" class="auth-link-criar">
                <strong>Criar conta grátis</strong>
            </a>
        </div>

    </div>
</div>
