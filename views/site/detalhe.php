<?php

/** @var yii\web\View $this */
/** @var app\models\Reclamacao $reclamacao */
/** @var app\models\ReclamacaoEvidencia[] $evidencias */
/** @var bool $isOwner */

use app\models\Reclamacao;
use yii\bootstrap5\Html;
use yii\helpers\Url;

$protocolo = $reclamacao->protocolo ?? ('#' . $reclamacao->id);
$this->title = 'Reclamação ' . $protocolo;
$this->params['breadcrumbs'][] = ['label' => 'Consultar Reclamações', 'url' => ['/site/consultar']];
$this->params['breadcrumbs'][] = $protocolo;

$isPublic  = Yii::$app->user->isGuest;
$wrapOpen  = $isPublic ? '<div class="container py-5">' : '';
$wrapClose = $isPublic ? '</div>' : '';

$statusMap = [
    Reclamacao::STATUS_PENDENTE  => ['badge' => 'status-pendente',  'label' => 'Pendente',      'icon' => 'bi-hourglass-split',    'desc' => 'Sua reclamação foi recebida e está aguardando análise.'],
    Reclamacao::STATUS_ANDAMENTO => ['badge' => 'status-andamento', 'label' => 'Em Andamento',  'icon' => 'bi-arrow-repeat',        'desc' => 'A prefeitura já está trabalhando na resolução do problema.'],
    Reclamacao::STATUS_RESOLVIDA => ['badge' => 'status-resolvida', 'label' => 'Resolvida',     'icon' => 'bi-check-circle-fill',   'desc' => 'O problema foi resolvido. Obrigado por contribuir com a cidade!'],
    Reclamacao::STATUS_ARQUIVADA => ['badge' => 'status-arquivada', 'label' => 'Arquivada',     'icon' => 'bi-archive-fill',        'desc' => 'Esta reclamação foi arquivada.'],
];
$sc = $statusMap[$reclamacao->status_rec] ?? $statusMap[Reclamacao::STATUS_PENDENTE];

$urgColors = [
    Reclamacao::URGENCIA_BAIXA => ['bg' => '#e8f5ec', 'color' => '#2e7d32', 'label' => 'Baixa'],
    Reclamacao::URGENCIA_MEDIA => ['bg' => '#fff8e1', 'color' => '#b45309', 'label' => 'Média'],
    Reclamacao::URGENCIA_ALTA  => ['bg' => '#fdecea', 'color' => '#c62828', 'label' => 'Alta'],
];
$uc = $urgColors[$reclamacao->urgencia] ?? $urgColors[Reclamacao::URGENCIA_BAIXA];

$fotos     = $reclamacao->fotos;
$historico = $reclamacao->historico;
$hasValorGasto = in_array('valor_gasto', $reclamacao->attributes(), true)
    && $reclamacao->valor_gasto !== null;
?>

<?= $wrapOpen ?>
<!-- Cabeçalho -->
<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
    <div>
        <?= Html::a(
            '<i class="bi bi-arrow-left me-1"></i>Consultar Reclamações',
            Url::to(['/site/consultar']),
            ['class' => 'text-muted text-decoration-none small']
        ) ?>
        <h2 class="fw-bold mb-0 mt-1" style="color:#1a5c2a;">
            <i class="bi bi-file-text-fill me-2" style="color:#237a38;"></i>Detalhes da Reclamação
        </h2>
        <p class="text-muted small mb-0">
            Protocolo: <strong style="font-family:monospace;"><?= Html::encode($protocolo) ?></strong>
        </p>
    </div>
    <span class="status-badge <?= Html::encode($sc['badge']) ?>" style="font-size:.9rem;padding:.5em 1.2em;">
        <i class="bi <?= Html::encode($sc['icon']) ?> me-1"></i><?= Html::encode($sc['label']) ?>
    </span>
</div>

<!-- Banner de status -->
<?php
$bannerStyle = [
    Reclamacao::STATUS_PENDENTE  => 'background:#fff8e1;border-left:4px solid #e6a800;',
    Reclamacao::STATUS_ANDAMENTO => 'background:#e3f2fd;border-left:4px solid #1565c0;',
    Reclamacao::STATUS_RESOLVIDA => 'background:#e8f5e9;border-left:4px solid #2e7d32;',
    Reclamacao::STATUS_ARQUIVADA => 'background:#f5f5f5;border-left:4px solid #9e9e9e;',
];
$bStyle = $bannerStyle[$reclamacao->status_rec] ?? $bannerStyle[Reclamacao::STATUS_PENDENTE];
?>
<div class="rounded p-3 mb-4" style="<?= $bStyle ?>">
    <i class="bi <?= Html::encode($sc['icon']) ?> me-2" style="font-size:1.1rem;"></i>
    <strong><?= Html::encode($sc['label']) ?> — </strong><?= Html::encode($sc['desc']) ?>
</div>

<div class="row g-4">

    <!-- Dados da reclamação -->
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white border-bottom py-3">
                <h5 class="mb-0 fw-bold" style="color:#1a5c2a;">
                    <i class="bi bi-file-text me-2"></i>Dados da Reclamação
                </h5>
            </div>
            <div class="card-body">
                <h5 class="fw-bold mb-1"><?= Html::encode($reclamacao->titulo) ?></h5>
                <p class="text-muted mb-4" style="font-size:.93rem;">
                    <?= nl2br(Html::encode((string) $reclamacao->descricao)) ?>
                </p>

                <div class="row g-2 mb-3">
                    <div class="col-sm-6">
                        <div class="rounded p-2" style="background:#f8fafc;">
                            <div class="text-muted" style="font-size:.68rem;text-transform:uppercase;letter-spacing:.5px;">Categoria</div>
                            <div class="fw-semibold small mt-1">
                                <i class="bi bi-tags-fill me-1" style="color:#237a38;"></i>
                                <?= Html::encode($reclamacao->categoria ? $reclamacao->categoria->nome : '—') ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="rounded p-2" style="background:#f8fafc;">
                            <div class="text-muted" style="font-size:.68rem;text-transform:uppercase;letter-spacing:.5px;">Urgência</div>
                            <div class="fw-semibold small mt-1">
                                <span class="badge rounded-pill"
                                      style="background:<?= $uc['bg'] ?>;color:<?= $uc['color'] ?>;">
                                    <?= Html::encode($uc['label']) ?>
                                </span>
                            </div>
                        </div>
                    </div>
                    <?php if ($reclamacao->bairro || $reclamacao->endereco): ?>
                    <div class="col-12">
                        <div class="rounded p-2" style="background:#f8fafc;">
                            <div class="text-muted" style="font-size:.68rem;text-transform:uppercase;letter-spacing:.5px;">Localização</div>
                            <div class="fw-semibold small mt-1">
                                <i class="bi bi-geo-alt-fill me-1" style="color:#237a38;"></i>
                                <?= Html::encode(implode(' — ', array_filter([(string)$reclamacao->endereco, (string)$reclamacao->bairro]))) ?>
                            </div>
                        </div>
                    </div>
                    <?php endif ?>
                    <div class="col-sm-6">
                        <div class="rounded p-2" style="background:#f8fafc;">
                            <div class="text-muted" style="font-size:.68rem;text-transform:uppercase;letter-spacing:.5px;">Registrado em</div>
                            <div class="fw-semibold small mt-1">
                                <i class="bi bi-calendar3 me-1" style="color:#237a38;"></i>
                                <?= $reclamacao->created_at
                                    ? date('d/m/Y \à\s H:i', strtotime((string) $reclamacao->created_at))
                                    : '—' ?>
                            </div>
                        </div>
                    </div>
                    <?php if ($hasValorGasto): ?>
                    <div class="col-sm-6">
                        <div class="rounded p-2" style="background:#e8f5ec;">
                            <div class="text-muted" style="font-size:.68rem;text-transform:uppercase;letter-spacing:.5px;">Valor Investido</div>
                            <div class="fw-bold mt-1" style="color:#1a5c2a;font-size:1.05rem;">
                                <i class="bi bi-cash-stack me-1"></i>
                                R$ <?= number_format((float) $reclamacao->valor_gasto, 2, ',', '.') ?>
                            </div>
                        </div>
                    </div>
                    <?php endif ?>
                </div>

                <!-- Fotos do cidadão -->
                <?php if (!empty($fotos)): ?>
                <div class="mt-3">
                    <h6 class="fw-semibold mb-2" style="color:#237a38;font-size:.78rem;text-transform:uppercase;letter-spacing:.5px;">
                        <i class="bi bi-images me-1"></i>Fotos que você enviou
                    </h6>
                    <div class="d-flex flex-wrap gap-2">
                        <?php foreach ($fotos as $foto): ?>
                        <?php $fotoPath = Yii::getAlias('@web') . '/uploads/reclamacoes/' . $foto->caminho; ?>
                        <a href="<?= Html::encode($fotoPath) ?>" target="_blank" title="Ver foto em tamanho original">
                            <img src="<?= Html::encode($fotoPath) ?>"
                                 alt="Foto"
                                 style="width:80px;height:80px;object-fit:cover;border-radius:8px;border:2px solid #e2e8f0;">
                        </a>
                        <?php endforeach ?>
                    </div>
                </div>
                <?php endif ?>
            </div>
        </div>
    </div>

    <!-- Progresso de status -->
    <div class="col-lg-5">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white border-bottom py-3">
                <h5 class="mb-0 fw-bold" style="color:#1a5c2a;">
                    <i class="bi bi-diagram-3 me-2"></i>Acompanhamento
                </h5>
            </div>
            <div class="card-body">

                <!-- Etapas visuais -->
                <?php
                $etapas = [
                    Reclamacao::STATUS_PENDENTE  => ['label' => 'Recebida',    'icon' => 'bi-inbox-fill'],
                    Reclamacao::STATUS_ANDAMENTO => ['label' => 'Em Andamento','icon' => 'bi-tools'],
                    Reclamacao::STATUS_RESOLVIDA => ['label' => 'Resolvida',   'icon' => 'bi-check-circle-fill'],
                ];
                $ordemStatus = array_keys($etapas);
                $idxAtual   = array_search($reclamacao->status_rec, $ordemStatus);
                if ($idxAtual === false) $idxAtual = 0;
                ?>
                <div class="d-flex justify-content-between mb-4 position-relative">
                    <div style="position:absolute;top:19px;left:10%;right:10%;height:3px;background:#e2e8f0;z-index:0;"></div>
                    <?php foreach ($etapas as $statusKey => $etapa): ?>
                    <?php
                        $idxEtapa = array_search($statusKey, $ordemStatus);
                        $ativo    = $idxEtapa <= $idxAtual;
                        $atual    = $statusKey === $reclamacao->status_rec;
                    ?>
                    <div class="d-flex flex-column align-items-center" style="z-index:1;flex:1;">
                        <div style="width:40px;height:40px;border-radius:50%;
                             background:<?= $ativo ? '#237a38' : '#e2e8f0' ?>;
                             display:flex;align-items:center;justify-content:center;
                             box-shadow:<?= $atual ? '0 0 0 4px rgba(35,122,56,.2)' : 'none' ?>;">
                            <i class="bi <?= Html::encode($etapa['icon']) ?>"
                               style="color:<?= $ativo ? '#fff' : '#9e9e9e' ?>;font-size:1rem;"></i>
                        </div>
                        <span class="mt-2 text-center fw-semibold"
                              style="font-size:.7rem;color:<?= $ativo ? '#1a5c2a' : '#9e9e9e' ?>;">
                            <?= Html::encode($etapa['label']) ?>
                        </span>
                    </div>
                    <?php endforeach ?>
                </div>

                <p class="text-muted small text-center mb-0">
                    <?= Html::encode($sc['desc']) ?>
                </p>

                <?php if ($reclamacao->status_rec === Reclamacao::STATUS_RESOLVIDA): ?>
                <div class="text-center mt-4">
                    <div style="font-size:3rem;">✅</div>
                    <p class="fw-bold mt-2 mb-1" style="color:#1a5c2a;">Problema resolvido!</p>
                    <p class="text-muted small mb-0">Obrigado por ajudar a melhorar a cidade.</p>
                </div>
                <?php endif ?>
            </div>
        </div>
    </div>
</div>

<!-- Fotos de evidência do gestor -->
<?php if (!empty($evidencias)): ?>
<div class="card border-0 shadow-sm mt-4">
    <div class="card-header bg-white border-bottom py-3 d-flex align-items-center gap-2">
        <div style="width:34px;height:34px;border-radius:8px;background:#e8f5ec;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
            <i class="bi bi-camera-fill" style="color:#237a38;"></i>
        </div>
        <div>
            <h5 class="mb-0 fw-bold" style="color:#1a5c2a;">Fotos de Evidência da Prefeitura</h5>
            <p class="text-muted small mb-0">Registradas pelos gestores ao trabalhar na sua solicitação.</p>
        </div>
    </div>
    <div class="card-body">
        <div class="row g-3">
            <?php foreach ($evidencias as $ev): ?>
            <?php $evUrl = Yii::getAlias('@web') . '/uploads/evidencias/' . $ev->caminho; ?>
            <div class="col-6 col-sm-4 col-md-3">
                <a href="<?= Html::encode($evUrl) ?>" target="_blank">
                    <img src="<?= Html::encode($evUrl) ?>"
                         class="img-fluid rounded shadow-sm"
                         alt="Evidência"
                         style="height:130px;width:100%;object-fit:cover;">
                </a>
                <p class="text-muted mt-1 mb-0" style="font-size:.7rem;">
                    <?= $ev->created_at ? date('d/m/Y', strtotime((string) $ev->created_at)) : '' ?>
                </p>
            </div>
            <?php endforeach ?>
        </div>
    </div>
</div>
<?php endif ?>

<!-- Histórico de atualizações -->
<?php if (!empty($historico)): ?>
<div class="card border-0 shadow-sm mt-4">
    <div class="card-header bg-white border-bottom py-3">
        <h5 class="mb-0 fw-bold" style="color:#1a5c2a;">
            <i class="bi bi-clock-history me-2" style="color:#237a38;"></i>Histórico de Atualizações
        </h5>
    </div>
    <div class="card-body pb-2">
        <?php foreach ($historico as $h): ?>
        <?php
            $sh  = $statusMap[$h->status_novo] ?? ['badge' => 'status-pendente', 'label' => $h->status_novo, 'icon' => 'bi-circle'];
        ?>
        <div class="d-flex gap-3 mb-3 pb-3 border-bottom">
            <div style="width:36px;height:36px;border-radius:50%;background:#e8f5ec;flex-shrink:0;
                        display:flex;align-items:center;justify-content:center;">
                <i class="bi <?= Html::encode($sh['icon']) ?>" style="color:#237a38;font-size:.9rem;"></i>
            </div>
            <div style="flex:1;">
                <div class="d-flex align-items-center gap-2 flex-wrap mb-1">
                    <span class="status-badge <?= Html::encode($sh['badge']) ?>" style="font-size:.72rem;">
                        <?= Html::encode($sh['label']) ?>
                    </span>
                    <?php if ($h->status_anterior && $h->status_anterior !== $h->status_novo): ?>
                    <span class="text-muted small">
                        ← <?= Html::encode(Reclamacao::statusLabels()[$h->status_anterior] ?? $h->status_anterior) ?>
                    </span>
                    <?php endif ?>
                </div>
                <?php if ($h->observacao): ?>
                <p class="mb-1" style="font-size:.85rem;"><?= Html::encode($h->observacao) ?></p>
                <?php endif ?>
                <span class="text-muted" style="font-size:.72rem;">
                    <?= $h->created_at ? date('d/m/Y \à\s H:i', strtotime((string) $h->created_at)) : '' ?>
                </span>
            </div>
        </div>
        <?php endforeach ?>
    </div>
</div>
<?php endif ?>

<!-- Sem histórico ainda -->
<?php if (empty($historico) && $reclamacao->status_rec === Reclamacao::STATUS_PENDENTE): ?>
<div class="card border-0 shadow-sm mt-4">
    <div class="card-body text-center py-4">
        <i class="bi bi-clock text-muted" style="font-size:2rem;opacity:.4;"></i>
        <p class="text-muted small mt-2 mb-0">
            Ainda não há atualizações. A prefeitura ainda não iniciou o atendimento desta reclamação.
        </p>
    </div>
</div>
<?php endif ?>

<?= $wrapClose ?>
