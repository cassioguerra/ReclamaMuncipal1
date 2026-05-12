<?php

/** @var yii\web\View $this */
/** @var app\models\Contato $model */

use yii\helpers\Url;

$this->title = 'Novo Contato';
$this->params['breadcrumbs'][] = ['label' => 'Contatos', 'url' => ['/contato/index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="row justify-content-center">
    <div class="col-lg-8">

        <div class="d-flex align-items-center gap-3 mb-4">
            <div style="width:48px;height:48px;border-radius:12px;background:#e8f5ec;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <i class="bi bi-person-plus-fill" style="font-size:1.3rem;color:#237a38;"></i>
            </div>
            <div>
                <h2 class="fw-bold mb-0" style="color:#1a5c2a;">Novo Contato</h2>
                <p class="text-muted small mb-0">Preencha os dados do novo contato institucional.</p>
            </div>
        </div>

        <?= $this->render('_form', ['model' => $model, 'editando' => false]) ?>

    </div>
</div>
