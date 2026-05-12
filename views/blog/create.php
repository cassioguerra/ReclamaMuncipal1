<?php

/**
 * @var yii\web\View    $this
 * @var app\models\Blog $model
 */

use yii\bootstrap5\Html;

$this->title = 'Novo Post';
?>

<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h2 class="fw-bold mb-1" style="color:#1a5c2a;">
            <i class="bi bi-plus-circle-fill me-2" style="color:#237a38;"></i>Novo Post do Blog
        </h2>
        <p class="text-muted small mb-0">Publique um acontecimento ou notícia da cidade.</p>
    </div>
</div>

<?= $this->render('_form', ['model' => $model, 'isEdicao' => false]) ?>
