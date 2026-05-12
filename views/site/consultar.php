<?php

/** @var yii\web\View $this */
/** @var app\models\Reclamacao[] $reclamacoes */
/** @var string $protocolo */
/** @var int|null $cidadaoId */

use app\models\Reclamacao;
use yii\bootstrap5\Html;
use yii\helpers\Json;
use yii\helpers\Url;

$this->title = 'Consultar Reclamações';
$this->params['breadcrumbs'][] = $this->title;

$statusConfig = [
    Reclamacao::STATUS_PENDENTE  => ['label' => 'Pendente',      'class' => 'status-pendente'],
    Reclamacao::STATUS_ANDAMENTO => ['label' => 'Em Andamento',  'class' => 'status-andamento'],
    Reclamacao::STATUS_RESOLVIDA => ['label' => 'Resolvida',     'class' => 'status-resolvida'],
    Reclamacao::STATUS_ARQUIVADA => ['label' => 'Arquivada',     'class' => 'status-arquivada'],
];

$isPublic = Yii::$app->user->isGuest;
$wrapOpen  = $isPublic ? '<div class="container py-5">' : '';
$wrapClose = $isPublic ? '</div>' : '';
?>

<?= $wrapOpen ?>
        <!-- Busca por protocolo -->
        <div class="row justify-content-center mb-5">
            <div class="col-lg-7">
                <div class="form-card">
                    <h3 class="form-card-titulo">Buscar por Protocolo</h3>
                    <div class="input-group input-group-lg mt-3">
                        <span class="input-group-text bg-white">
                            <i class="bi bi-hash text-muted"></i>
                        </span>
                        <input type="text" id="prot-input" class="form-control"
                               value="<?= Html::encode($protocolo) ?>"
                               placeholder="Digite o número do protocolo (ex.: 2026001234)"
                               maxlength="20">
                        <button class="btn btn-verde-principal" type="button" id="btn-buscar">
                            <i class="bi bi-search me-1"></i>Consultar
                        </button>
                    </div>
                    <?php if ($protocolo !== '' && empty($reclamacoes)): ?>
                    <p class="text-danger small mt-2 mb-0">
                        <i class="bi bi-exclamation-circle me-1"></i>
                        Nenhuma reclamação encontrada com o protocolo <strong><?= Html::encode($protocolo) ?></strong>.
                    </p>
                    <?php else: ?>
                    <p class="text-muted small mt-2 mb-0">
                        O protocolo é gerado automaticamente ao registrar a reclamação.
                    </p>
                    <?php endif ?>
                </div>
            </div>
        </div>

        <!-- Minhas Reclamações -->
        <div class="row">
            <div class="col-12">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h4 class="mb-0 fw-bold text-verde">
                        <?= $protocolo !== '' ? 'Resultado da Pesquisa' : 'Reclamações' ?>
                        <span class="badge bg-verde-suave text-verde ms-2" style="font-size:.75rem">
                            <?= count($reclamacoes) ?>
                        </span>
                    </h4>
                    <?php if ($protocolo !== ''): ?>
                    <a href="<?= Url::to(['/site/consultar']) ?>" class="btn btn-sm btn-verde-outline">
                        <i class="bi bi-x-circle me-1"></i>Limpar filtro
                    </a>
                    <?php endif ?>
                </div>

                <?php if (empty($reclamacoes)): ?>
                <div class="text-center py-5">
                    <i class="bi bi-inbox text-muted" style="font-size: 3.5rem; opacity:.4"></i>
                    <p class="text-muted mt-3 mb-1 fw-semibold">Nenhuma reclamação encontrada.</p>
                    <?php if ($protocolo !== ''): ?>
                    <p class="text-muted small mb-4">Protocolo <strong><?= Html::encode($protocolo) ?></strong> não localizado.</p>
                    <?php else: ?>
                    <p class="text-muted small mb-4">Ainda não há reclamações registradas no sistema.</p>
                    <?php endif ?>
                    <?php if (!Yii::$app->user->isGuest): ?>
                    <?= Html::a(
                        '<i class="bi bi-megaphone-fill me-2"></i>Registrar Agora',
                        Url::to(['/site/reclamar']),
                        ['class' => 'btn btn-verde-principal']
                    ) ?>
                    <?php endif ?>
                </div>

                <?php else: ?>
                <div class="reclamacoes-lista">
                    <?php foreach ($reclamacoes as $rec): ?>
                    <?php
                        $statusCfg  = $statusConfig[$rec->status_rec] ?? ['label' => $rec->status_rec, 'class' => 'status-pendente'];
                        $catNome    = $rec->categoria ? Html::encode($rec->categoria->nome) : '<em class="text-muted">Sem categoria</em>';
                        $dataRaw    = (string) ($rec->created_at ?? '');
                        $ts         = $dataRaw !== '' ? strtotime($dataRaw) : 0;
                        $dataFmt    = $ts > 0 ? date('d/m/Y', $ts) : substr($dataRaw, 0, 10);
                    ?>
                    <div class="reclamacao-item">
                        <div class="reclamacao-meta">
                            <span class="reclamacao-protocolo">
                                <?= Html::encode($rec->protocolo ?? '#' . $rec->id) ?>
                            </span>
                            <span class="reclamacao-data"><?= Html::encode($dataFmt) ?></span>
                        </div>
                        <div class="reclamacao-info">
                            <h5 class="reclamacao-titulo"><?= Html::encode($rec->titulo) ?></h5>
                            <span class="reclamacao-categoria"><?= $catNome ?></span>
                            <?php if (!empty($rec->bairro) || !empty($rec->endereco)): ?>
                            <span class="text-muted small ms-2">
                                <i class="bi bi-geo-alt me-1"></i><?= Html::encode(trim(($rec->bairro ?? '') . ($rec->endereco ? ' — ' . $rec->endereco : ''))) ?>
                            </span>
                            <?php endif ?>
                        </div>
                        <div class="reclamacao-status d-flex flex-column align-items-end gap-2">
                            <span class="status-badge <?= Html::encode($statusCfg['class']) ?>">
                                <?= Html::encode($statusCfg['label']) ?>
                            </span>
                            <?php
                                $urgLabels = Reclamacao::urgenciaLabels();
                                $urgLabel  = $urgLabels[$rec->urgencia] ?? $rec->urgencia;
                                $urgClass  = $rec->urgencia === 'alta' ? 'text-danger' : ($rec->urgencia === 'media' ? 'text-warning' : 'text-success');
                            ?>
                            <small class="<?= $urgClass ?>">
                                <i class="bi bi-exclamation-circle me-1"></i><?= Html::encode($urgLabel) ?>
                            </small>
                            <?= Html::a(
                                '<i class="bi bi-eye me-1"></i>Ver detalhes',
                                Url::to(['/site/detalhe', 'id' => $rec->id]),
                                ['class' => 'btn btn-sm btn-outline-success mt-1']
                            ) ?>
                        </div>
                    </div>
                    <?php endforeach ?>
                </div>
                <?php endif ?>
            </div>
        </div>

        <?php if (!empty($reclamacoes)): ?>
        <!-- CTA nova reclamação -->
        <div class="text-center mt-5 py-4 cta-mini">
            <?php if (!Yii::$app->user->isGuest): ?>
            <p class="mb-3 text-muted">Tem outro problema para reportar?</p>
            <?= Html::a(
                '<i class="bi bi-megaphone-fill me-2"></i>Nova Reclamação',
                Url::to(['/site/reclamar']),
                ['class' => 'btn btn-verde-principal']
            ) ?>
            <?php else: ?>
            <p class="mb-3 text-muted">Quer registrar uma reclamação?</p>
            <?= Html::a(
                '<i class="bi bi-person-plus-fill me-2"></i>Criar Conta e Registrar',
                Url::to(['/site/registrar']),
                ['class' => 'btn btn-verde-principal']
            ) ?>
            <?php endif ?>
        </div>
        <?php endif ?>

<?php
$baseUrl = Json::encode(Url::to(['/site/consultar']));
$this->registerJs(<<<JS
(function () {
    var baseUrl = {$baseUrl};
    var input   = document.getElementById('prot-input');
    var btn     = document.getElementById('btn-buscar');

    function buscar() {
        var p = input.value.trim();
        if (!p) { window.location.href = baseUrl; return; }
        var sep = baseUrl.indexOf('?') >= 0 ? '&' : '?';
        window.location.href = baseUrl + sep + 'protocolo=' + encodeURIComponent(p);
    }

    btn.addEventListener('click', buscar);
    input.addEventListener('keydown', function (e) { if (e.key === 'Enter') buscar(); });
})();
JS);
?>

<?= $wrapClose ?>
