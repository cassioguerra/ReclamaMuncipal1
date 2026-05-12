<?php

/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $provider */
/** @var string|null $statusFiltro */
/** @var array $totais */

use app\models\Reclamacao;
use yii\bootstrap5\Html;
use yii\helpers\Url;
use yii\widgets\LinkPager;

$this->title = 'Gestão de Reclamações';
?>

<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h2 class="fw-bold mb-1" style="color:#1a5c2a;">
            <i class="bi bi-clipboard2-check-fill me-2" style="color:#237a38;"></i>Gestão de Reclamações
        </h2>
        <p class="text-muted small mb-0">
            Visualize, atualize o status e registre informações sobre as reclamações.
        </p>
    </div>
</div>

<!-- Cards de resumo / filtros rápidos -->
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <?= Html::a(
            '<div class="card-body py-3 px-3">'
            . '<div class="fw-bold fs-4 lh-1" style="color:#237a38;">' . $totais['total'] . '</div>'
            . '<div class="text-muted small mt-1">Total</div></div>',
            Url::to(['/gestao/index']),
            ['class' => 'card border-0 shadow-sm text-decoration-none d-block' . (!$statusFiltro ? ' border-start border-3 border-success' : '')]
        ) ?>
    </div>
    <div class="col-6 col-md-3">
        <?= Html::a(
            '<div class="card-body py-3 px-3">'
            . '<div class="fw-bold fs-4 lh-1" style="color:#e6a800;">' . $totais['pendentes'] . '</div>'
            . '<div class="text-muted small mt-1">Pendentes</div></div>',
            Url::to(['/gestao/index', 'status' => Reclamacao::STATUS_PENDENTE]),
            ['class' => 'card border-0 shadow-sm text-decoration-none d-block'
                . ($statusFiltro === Reclamacao::STATUS_PENDENTE ? ' border-start border-3' : '')
            , 'style' => $statusFiltro === Reclamacao::STATUS_PENDENTE ? 'border-color:#e6a800 !important' : '']
        ) ?>
    </div>
    <div class="col-6 col-md-3">
        <?= Html::a(
            '<div class="card-body py-3 px-3">'
            . '<div class="fw-bold fs-4 lh-1" style="color:#1565c0;">' . $totais['andamento'] . '</div>'
            . '<div class="text-muted small mt-1">Em Andamento</div></div>',
            Url::to(['/gestao/index', 'status' => Reclamacao::STATUS_ANDAMENTO]),
            ['class' => 'card border-0 shadow-sm text-decoration-none d-block'
                . ($statusFiltro === Reclamacao::STATUS_ANDAMENTO ? ' border-start border-3 border-primary' : '')]
        ) ?>
    </div>
    <div class="col-6 col-md-3">
        <?= Html::a(
            '<div class="card-body py-3 px-3">'
            . '<div class="fw-bold fs-4 lh-1" style="color:#2e7d32;">' . $totais['resolvidas'] . '</div>'
            . '<div class="text-muted small mt-1">Resolvidas</div></div>',
            Url::to(['/gestao/index', 'status' => Reclamacao::STATUS_RESOLVIDA]),
            ['class' => 'card border-0 shadow-sm text-decoration-none d-block'
                . ($statusFiltro === Reclamacao::STATUS_RESOLVIDA ? ' border-start border-3 border-success' : '')]
        ) ?>
    </div>
</div>

<!-- Tabela -->
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-bottom py-3 d-flex align-items-center justify-content-between">
        <h6 class="mb-0 fw-bold" style="color:#1a5c2a;">
            <i class="bi bi-list-ul me-2"></i>Reclamações
            <?php if ($statusFiltro): ?>
            <span class="badge ms-2" style="background:#237a38;font-size:.7rem;">
                <?= Html::encode(Reclamacao::statusLabels()[$statusFiltro] ?? $statusFiltro) ?>
            </span>
            <?php endif ?>
        </h6>
        <?php if ($statusFiltro): ?>
        <?= Html::a(
            '<i class="bi bi-x-circle me-1"></i>Limpar filtro',
            Url::to(['/gestao/index']),
            ['class' => 'btn btn-sm btn-outline-secondary']
        ) ?>
        <?php endif ?>
    </div>

    <div class="card-body p-0">
        <?php $models = $provider->getModels(); ?>

        <?php if (empty($models)): ?>
        <div class="text-center py-5">
            <i class="bi bi-inbox text-muted" style="font-size:2.5rem;opacity:.4"></i>
            <p class="text-muted mt-3 mb-0">Nenhuma reclamação encontrada.</p>
        </div>

        <?php else: ?>
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4" style="width:130px;">Protocolo</th>
                        <th>Título</th>
                        <th style="width:160px;">Cidadão</th>
                        <th style="width:130px;">Categoria</th>
                        <th style="width:90px;">Urgência</th>
                        <th style="width:120px;">Status</th>
                        <th style="width:90px;">Data</th>
                        <th style="width:60px;"></th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($models as $r): ?>
                <?php
                    $urgColors = [
                        Reclamacao::URGENCIA_BAIXA => ['bg' => '#e8f5ec', 'color' => '#2e7d32'],
                        Reclamacao::URGENCIA_MEDIA => ['bg' => '#fff8e1', 'color' => '#b45309'],
                        Reclamacao::URGENCIA_ALTA  => ['bg' => '#fdecea', 'color' => '#c62828'],
                    ];
                    $uc = $urgColors[$r->urgencia] ?? $urgColors[Reclamacao::URGENCIA_BAIXA];

                    $statusMap = [
                        Reclamacao::STATUS_PENDENTE  => ['badge' => 'status-pendente',  'label' => 'Pendente'],
                        Reclamacao::STATUS_ANDAMENTO => ['badge' => 'status-andamento', 'label' => 'Em Andamento'],
                        Reclamacao::STATUS_RESOLVIDA => ['badge' => 'status-resolvida', 'label' => 'Resolvida'],
                        Reclamacao::STATUS_ARQUIVADA => ['badge' => 'status-arquivada', 'label' => 'Arquivada'],
                    ];
                    $sc = $statusMap[$r->status_rec] ?? ['badge' => 'status-pendente', 'label' => $r->status_rec];
                ?>
                <tr>
                    <td class="ps-4">
                        <span class="text-muted fw-semibold" style="font-size:.72rem;font-family:monospace;">
                            <?= Html::encode($r->protocolo ?? '#' . $r->id) ?>
                        </span>
                    </td>
                    <td>
                        <div class="fw-semibold text-truncate" style="max-width:200px;"
                             title="<?= Html::encode($r->titulo) ?>">
                            <?= Html::encode($r->titulo) ?>
                        </div>
                    </td>
                    <td class="text-muted small">
                        <?= Html::encode($r->cidadao
                            ? ($r->cidadao->nome_completo ?: $r->cidadao->username)
                            : '—') ?>
                    </td>
                    <td class="text-muted small">
                        <?= Html::encode($r->categoria ? $r->categoria->nome : '—') ?>
                    </td>
                    <td>
                        <span class="badge rounded-pill px-2"
                              style="background:<?= $uc['bg'] ?>;color:<?= $uc['color'] ?>;font-size:.7rem;">
                            <?= Html::encode($r->getUrgenciaLabel()) ?>
                        </span>
                    </td>
                    <td>
                        <span class="status-badge <?= Html::encode($sc['badge']) ?>">
                            <?= Html::encode($sc['label']) ?>
                        </span>
                    </td>
                    <td class="text-muted small">
                        <?= $r->created_at
                            ? date('d/m/Y', strtotime((string) $r->created_at))
                            : '—' ?>
                    </td>
                    <td>
                        <?= Html::a(
                            '<i class="bi bi-eye-fill"></i>',
                            Url::to(['/gestao/visualizar', 'id' => $r->id]),
                            ['class' => 'btn btn-sm btn-outline-success', 'title' => 'Gerenciar']
                        ) ?>
                    </td>
                </tr>
                <?php endforeach ?>
                </tbody>
            </table>
        </div>

        <?php if ($provider->pagination->pageCount > 1): ?>
        <div class="bg-white border-top d-flex justify-content-center py-3">
            <?= LinkPager::widget([
                'pagination'          => $provider->pagination,
                'options'             => ['class' => 'pagination pagination-sm mb-0'],
                'linkOptions'         => ['class' => 'page-link'],
                'pageCssClass'        => 'page-item',
                'activePageCssClass'  => 'page-item active',
                'disabledPageCssClass'=> 'page-item disabled',
                'prevPageLabel'       => '&lsaquo;',
                'nextPageLabel'       => '&rsaquo;',
            ]) ?>
        </div>
        <?php endif ?>

        <?php endif ?>
    </div>
</div>
