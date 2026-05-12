<?php

/** @var yii\web\View $this */
/** @var app\models\Cidadao $cidadao */
/** @var int $total */
/** @var int $pendentes */
/** @var int $andamento */
/** @var int $resolvidas */
/** @var app\models\Reclamacao[] $recentes */

use app\models\Reclamacao;
use yii\helpers\Url;
use yii\bootstrap5\Html;

$this->title = 'Dashboard';
?>

<!-- Cards de estatísticas -->
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div style="width:48px;height:48px;border-radius:12px;background:#e8f5ec;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <i class="bi bi-megaphone-fill" style="font-size:1.3rem;color:#237a38;"></i>
                </div>
                <div>
                    <div class="fw-bold fs-4 lh-1" style="color:#237a38;"><?= $total ?></div>
                    <div class="text-muted small mt-1">Total de Reclamações</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div style="width:48px;height:48px;border-radius:12px;background:#fff8e1;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <i class="bi bi-hourglass-split" style="font-size:1.3rem;color:#e6a800;"></i>
                </div>
                <div>
                    <div class="fw-bold fs-4 lh-1" style="color:#e6a800;"><?= $pendentes ?></div>
                    <div class="text-muted small mt-1">Pendentes</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div style="width:48px;height:48px;border-radius:12px;background:#e3f2fd;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <i class="bi bi-arrow-repeat" style="font-size:1.3rem;color:#1565c0;"></i>
                </div>
                <div>
                    <div class="fw-bold fs-4 lh-1" style="color:#1565c0;"><?= $andamento ?></div>
                    <div class="text-muted small mt-1">Em Andamento</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div style="width:48px;height:48px;border-radius:12px;background:#e8f5e9;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <i class="bi bi-check-circle-fill" style="font-size:1.3rem;color:#2e7d32;"></i>
                </div>
                <div>
                    <div class="fw-bold fs-4 lh-1" style="color:#2e7d32;"><?= $resolvidas ?></div>
                    <div class="text-muted small mt-1">Resolvidas</div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Ações rápidas -->
<div class="row g-3 mb-4">
    <div class="col-md-6">
        <div class="card border-0 shadow-sm" style="border-left:4px solid #237a38 !important;">
            <div class="card-body">
                <h5 class="fw-bold mb-1" style="color:#1a5c2a;">
                    <i class="bi bi-plus-circle-fill me-2" style="color:#237a38;"></i>Nova Reclamação
                </h5>
                <p class="text-muted small mb-3">
                    Registre um problema no seu município e acompanhe o andamento.
                </p>
                <a href="<?= Url::to(['/site/reclamar']) ?>"
                   class="btn btn-sm fw-semibold"
                   style="background:#237a38;color:#fff;border-radius:8px;padding:8px 20px;">
                    <i class="bi bi-megaphone me-1"></i>Registrar agora
                </a>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card border-0 shadow-sm" style="border-left:4px solid #0ea5e9 !important;">
            <div class="card-body">
                <h5 class="fw-bold mb-1" style="color:#0c4a6e;">
                    <i class="bi bi-search me-2" style="color:#0ea5e9;"></i>Consultar Status
                </h5>
                <p class="text-muted small mb-3">
                    Acompanhe todas as suas reclamações e veja o andamento de cada uma.
                </p>
                <a href="<?= Url::to(['/site/consultar']) ?>"
                   class="btn btn-sm fw-semibold"
                   style="background:#0ea5e9;color:#fff;border-radius:8px;padding:8px 20px;">
                    <i class="bi bi-list-check me-1"></i>Ver reclamações
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Boas-vindas + reclamações recentes -->
<div class="row g-4">
    <!-- Card de boas-vindas -->
    <div class="col-lg-5">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body p-4">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div style="width:54px;height:54px;background:linear-gradient(135deg,#1a5c2a,#2d9e4a);border-radius:14px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <span style="font-size:1.5rem;font-weight:800;color:#fff;">
                            <?= strtoupper(substr((string) $cidadao->username, 0, 1)) ?>
                        </span>
                    </div>
                    <div>
                        <h5 class="fw-bold mb-0" style="color:#1a5c2a;">
                            Olá, <?= Html::encode($cidadao->nome_completo ?: $cidadao->username) ?>!
                        </h5>
                        <span class="text-muted small">Bem-vindo ao Portal do Cidadão</span>
                    </div>
                </div>
                <hr>
                <div class="d-flex flex-column gap-3">
                    <div class="d-flex gap-2 align-items-start">
                        <div style="width:32px;height:32px;background:#e8f5ec;border-radius:8px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                            <i class="bi bi-1-circle-fill" style="color:#237a38;"></i>
                        </div>
                        <div>
                            <div class="fw-semibold small">Registre sua reclamação</div>
                            <div class="text-muted" style="font-size:.78rem;">Descreva o problema e escolha a categoria.</div>
                        </div>
                    </div>
                    <div class="d-flex gap-2 align-items-start">
                        <div style="width:32px;height:32px;background:#e8f5ec;border-radius:8px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                            <i class="bi bi-2-circle-fill" style="color:#237a38;"></i>
                        </div>
                        <div>
                            <div class="fw-semibold small">Receba seu protocolo</div>
                            <div class="text-muted" style="font-size:.78rem;">Um número único para acompanhar sua solicitação.</div>
                        </div>
                    </div>
                    <div class="d-flex gap-2 align-items-start">
                        <div style="width:32px;height:32px;background:#e8f5ec;border-radius:8px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                            <i class="bi bi-3-circle-fill" style="color:#237a38;"></i>
                        </div>
                        <div>
                            <div class="fw-semibold small">Acompanhe o andamento</div>
                            <div class="text-muted" style="font-size:.78rem;">Resposta em até 72 horas úteis.</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Reclamações recentes -->
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body p-4">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h5 class="fw-bold mb-0" style="color:#1a5c2a;">
                        <i class="bi bi-clock-history me-2" style="color:#237a38;"></i>Recentes
                    </h5>
                    <a href="<?= Url::to(['/site/consultar']) ?>"
                       class="btn btn-sm btn-outline-success">
                        Ver todas
                    </a>
                </div>

                <?php if (empty($recentes)): ?>
                <div class="text-center py-4">
                    <i class="bi bi-inbox text-muted" style="font-size:2.5rem;opacity:.4"></i>
                    <p class="text-muted small mt-2 mb-3">Você ainda não fez nenhuma reclamação.</p>
                    <a href="<?= Url::to(['/site/reclamar']) ?>"
                       class="btn btn-sm btn-verde-principal">
                        <i class="bi bi-megaphone me-1"></i>Registrar primeira reclamação
                    </a>
                </div>

                <?php else: ?>
                <div class="reclamacoes-lista reclamacoes-lista-sm">
                    <?php foreach ($recentes as $rec): ?>
                    <?php
                        $statusColors = [
                            Reclamacao::STATUS_PENDENTE  => ['badge' => 'status-pendente',  'label' => 'Pendente'],
                            Reclamacao::STATUS_ANDAMENTO => ['badge' => 'status-andamento', 'label' => 'Em Andamento'],
                            Reclamacao::STATUS_RESOLVIDA => ['badge' => 'status-resolvida', 'label' => 'Resolvida'],
                            Reclamacao::STATUS_ARQUIVADA => ['badge' => 'status-arquivada', 'label' => 'Arquivada'],
                        ];
                        $sc = $statusColors[$rec->status_rec] ?? ['badge' => 'status-pendente', 'label' => $rec->status_rec];
                    ?>
                    <div class="reclamacao-item reclamacao-item-sm">
                        <div class="reclamacao-info" style="flex:1;min-width:0;">
                            <div class="reclamacao-titulo text-truncate">
                                <?= Html::encode($rec->titulo) ?>
                            </div>
                            <div class="text-muted" style="font-size:.72rem;">
                                <?= Html::encode($rec->protocolo ?? '#' . $rec->id) ?>
                                <?php if ($rec->categoria): ?>
                                · <?= Html::encode($rec->categoria->nome) ?>
                                <?php endif ?>
                            </div>
                        </div>
                        <span class="status-badge <?= Html::encode($sc['badge']) ?> ms-2 flex-shrink-0">
                            <?= Html::encode($sc['label']) ?>
                        </span>
                    </div>
                    <?php endforeach ?>
                </div>
                <?php endif ?>

            </div>
        </div>
    </div>
</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div style="width:48px;height:48px;border-radius:12px;background:#fff8e1;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <i class="bi bi-hourglass-split" style="font-size:1.3rem;color:#e6a800;"></i>
                </div>
                <div>
                    <div class="fw-bold fs-4 lh-1" style="color:#e6a800;">0</div>
                    <div class="text-muted small mt-1">Pendentes</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div style="width:48px;height:48px;border-radius:12px;background:#e3f2fd;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <i class="bi bi-arrow-repeat" style="font-size:1.3rem;color:#1565c0;"></i>
                </div>
                <div>
                    <div class="fw-bold fs-4 lh-1" style="color:#1565c0;">0</div>
                    <div class="text-muted small mt-1">Em Andamento</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div style="width:48px;height:48px;border-radius:12px;background:#e8f5e9;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <i class="bi bi-check-circle-fill" style="font-size:1.3rem;color:#2e7d32;"></i>
                </div>
                <div>
                    <div class="fw-bold fs-4 lh-1" style="color:#2e7d32;">0</div>
                    <div class="text-muted small mt-1">Resolvidas</div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Ações rápidas -->
<div class="row g-3 mb-4">
    <div class="col-md-6">
        <div class="card border-0 shadow-sm" style="border-left:4px solid #237a38 !important;">
            <div class="card-body">
                <h5 class="fw-bold mb-1" style="color:#1a5c2a;">
                    <i class="bi bi-plus-circle-fill me-2" style="color:#237a38;"></i>Nova Reclamação
                </h5>
                <p class="text-muted small mb-3">
                    Registre um problema no seu município e acompanhe o andamento.
                </p>
                <a href="<?= Url::to(['/site/reclamar']) ?>"
                   class="btn btn-sm fw-semibold"
                   style="background:#237a38;color:#fff;border-radius:8px;padding:8px 20px;">
                    <i class="bi bi-megaphone me-1"></i>Registrar agora
                </a>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card border-0 shadow-sm" style="border-left:4px solid #0ea5e9 !important;">
            <div class="card-body">
                <h5 class="fw-bold mb-1" style="color:#0c4a6e;">
                    <i class="bi bi-search me-2" style="color:#0ea5e9;"></i>Consultar Status
                </h5>
                <p class="text-muted small mb-3">
                    Acompanhe todas as suas reclamações e veja o andamento de cada uma.
                </p>
                <a href="<?= Url::to(['/site/consultar']) ?>"
                   class="btn btn-sm fw-semibold"
                   style="background:#0ea5e9;color:#fff;border-radius:8px;padding:8px 20px;">
                    <i class="bi bi-list-check me-1"></i>Ver reclamações
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Boas-vindas / orientação -->
<div class="card border-0 shadow-sm">
    <div class="card-body p-4">
        <div class="d-flex align-items-center gap-3 mb-3">
            <div style="width:54px;height:54px;background:linear-gradient(135deg,#1a5c2a,#2d9e4a);border-radius:14px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <span style="font-size:1.5rem;font-weight:800;color:#fff;">
                    <?= strtoupper(substr((string) $cidadao->username, 0, 1)) ?>
                </span>
            </div>
            <div>
                <h5 class="fw-bold mb-0" style="color:#1a5c2a;">
                    Olá, <?= Html::encode($cidadao->nome_completo ?: $cidadao->username) ?>!
                </h5>
                <span class="text-muted small">Bem-vindo ao Portal do Cidadão</span>
            </div>
        </div>
        <hr>
        <div class="row g-3">
            <div class="col-md-4 d-flex gap-2 align-items-start">
                <div style="width:32px;height:32px;background:#e8f5ec;border-radius:8px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <i class="bi bi-1-circle-fill" style="color:#237a38;"></i>
                </div>
                <div>
                    <div class="fw-semibold small">Registre sua reclamação</div>
                    <div class="text-muted" style="font-size:.78rem;">Descreva o problema e escolha a categoria.</div>
                </div>
            </div>
            <div class="col-md-4 d-flex gap-2 align-items-start">
                <div style="width:32px;height:32px;background:#e8f5ec;border-radius:8px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <i class="bi bi-2-circle-fill" style="color:#237a38;"></i>
                </div>
                <div>
                    <div class="fw-semibold small">Receba seu protocolo</div>
                    <div class="text-muted" style="font-size:.78rem;">Um número único para acompanhar sua solicitação.</div>
                </div>
            </div>
            <div class="col-md-4 d-flex gap-2 align-items-start">
                <div style="width:32px;height:32px;background:#e8f5ec;border-radius:8px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <i class="bi bi-3-circle-fill" style="color:#237a38;"></i>
                </div>
                <div>
                    <div class="fw-semibold small">Acompanhe o andamento</div>
                    <div class="text-muted" style="font-size:.78rem;">Resposta em até 72 horas úteis.</div>
                </div>
            </div>
        </div>
    </div>
</div>
