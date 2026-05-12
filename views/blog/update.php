<?php

/**
 * @var yii\web\View    $this
 * @var app\models\Blog $model
 */

use yii\bootstrap5\Html;
use yii\helpers\Url;

$this->title = 'Editar: ' . $model->titulo;
?>

<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h2 class="fw-bold mb-1" style="color:#1a5c2a;">
            <i class="bi bi-pencil-fill me-2" style="color:#237a38;"></i>Editar Post
        </h2>
        <p class="text-muted small mb-0"><?= Html::encode($model->titulo) ?></p>
    </div>
    <?= Html::a(
        '<i class="bi bi-eye me-1"></i>Ver publicado',
        Url::to(['/blog/view', 'id' => $model->id]),
        ['class' => 'btn btn-sm btn-outline-success', 'target' => '_blank']
    ) ?>
</div>

<?= $this->render('_form', ['model' => $model, 'isEdicao' => true]) ?>
