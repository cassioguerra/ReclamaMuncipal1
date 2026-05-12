<?php

/** @var yii\web\View $this */
/** @var app\models\Reclamacao $reclamacao */
/** @var app\models\ReclamacaoEvidencia[] $evidencias */

use app\models\Reclamacao;
use yii\bootstrap5\Html;
use yii\helpers\Url;

$this->title = 'Gerenciar — ' . ($reclamacao->protocolo ?? '#' . $reclamacao->id);
$this->params['breadcrumbs'][] = ['label' => 'Gestão', 'url' => ['/gestao/index']];
$this->params['breadcrumbs'][] = 'Gerenciar';

$statusMap = [
    Reclamacao::STATUS_PENDENTE  => ['badge' => 'status-pendente',  'label' => 'Pendente'],
    Reclamacao::STATUS_ANDAMENTO => ['badge' => 'status-andamento', 'label' => 'Em Andamento'],
    Reclamacao::STATUS_RESOLVIDA => ['badge' => 'status-resolvida', 'label' => 'Resolvida'],
    Reclamacao::STATUS_ARQUIVADA => ['badge' => 'status-arquivada', 'label' => 'Arquivada'],
];
$sc = $statusMap[$reclamacao->status_rec] ?? ['badge' => 'status-pendente', 'label' => $reclamacao->status_rec];

$urgColors = [
    Reclamacao::URGENCIA_BAIXA => ['bg' => '#e8f5ec', 'color' => '#2e7d32', 'label' => 'Baixa'],
    Reclamacao::URGENCIA_MEDIA => ['bg' => '#fff8e1', 'color' => '#b45309', 'label' => 'Média'],
    Reclamacao::URGENCIA_ALTA  => ['bg' => '#fdecea', 'color' => '#c62828', 'label' => 'Alta'],
];
$uc = $urgColors[$reclamacao->urgencia] ?? $urgColors[Reclamacao::URGENCIA_BAIXA];

$hasValorGasto = in_array('valor_gasto', $reclamacao->attributes(), true);

$fotos     = $reclamacao->fotos;
$historico = $reclamacao->historico;
?>

<!-- Cabeçalho -->
<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
    <div>
        <?= Html::a(
            '<i class="bi bi-arrow-left me-1"></i>Voltar à lista',
            Url::to(['/gestao/index']),
            ['class' => 'text-muted text-decoration-none small']
        ) ?>
        <h2 class="fw-bold mb-0 mt-1" style="color:#1a5c2a;">
            <i class="bi bi-clipboard2-check-fill me-2" style="color:#237a38;"></i>Gerenciar Reclamação
        </h2>
        <p class="text-muted small mb-0">
            Protocolo: <strong style="font-family:monospace;"><?= Html::encode($reclamacao->protocolo ?? '#' . $reclamacao->id) ?></strong>
        </p>
    </div>
    <span class="status-badge <?= Html::encode($sc['badge']) ?>" style="font-size:.85rem;padding:.4em 1em;">
        <?= Html::encode($sc['label']) ?>
    </span>
</div>

<!-- Linha principal: dados + formulário -->
<div class="row g-4 mb-4">

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
                <p class="text-muted mb-3" style="font-size:.92rem;"><?= nl2br(Html::encode((string) $reclamacao->descricao)) ?></p>

                <div class="row g-2 mb-3">
                    <div class="col-sm-6">
                        <div class="rounded p-2" style="background:#f8fafc;">
                            <div class="text-muted" style="font-size:.7rem;text-transform:uppercase;letter-spacing:.5px;">Categoria</div>
                            <div class="fw-semibold small mt-1">
                                <i class="bi bi-tags-fill me-1" style="color:#237a38;"></i>
                                <?= Html::encode($reclamacao->categoria ? $reclamacao->categoria->nome : '—') ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="rounded p-2" style="background:#f8fafc;">
                            <div class="text-muted" style="font-size:.7rem;text-transform:uppercase;letter-spacing:.5px;">Urgência</div>
                            <div class="fw-semibold small mt-1">
                                <span class="badge rounded-pill px-2"
                                      style="background:<?= $uc['bg'] ?>;color:<?= $uc['color'] ?>;">
                                    <?= Html::encode($uc['label']) ?>
                                </span>
                            </div>
                        </div>
                    </div>
                    <?php if ($reclamacao->bairro || $reclamacao->endereco): ?>
                    <div class="col-12">
                        <div class="rounded p-2" style="background:#f8fafc;">
                            <div class="text-muted" style="font-size:.7rem;text-transform:uppercase;letter-spacing:.5px;">Localização</div>
                            <div class="fw-semibold small mt-1">
                                <i class="bi bi-geo-alt-fill me-1" style="color:#237a38;"></i>
                                <?= Html::encode(implode(' — ', array_filter([(string)$reclamacao->endereco, (string)$reclamacao->bairro]))) ?>
                            </div>
                        </div>
                    </div>
                    <?php endif ?>
                    <div class="col-sm-6">
                        <div class="rounded p-2" style="background:#f8fafc;">
                            <div class="text-muted" style="font-size:.7rem;text-transform:uppercase;letter-spacing:.5px;">Cidadão</div>
                            <div class="fw-semibold small mt-1">
                                <i class="bi bi-person-fill me-1" style="color:#237a38;"></i>
                                <?= Html::encode($reclamacao->cidadao
                                    ? ($reclamacao->cidadao->nome_completo ?: $reclamacao->cidadao->username)
                                    : '—') ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="rounded p-2" style="background:#f8fafc;">
                            <div class="text-muted" style="font-size:.7rem;text-transform:uppercase;letter-spacing:.5px;">Registrado em</div>
                            <div class="fw-semibold small mt-1">
                                <i class="bi bi-calendar3 me-1" style="color:#237a38;"></i>
                                <?= $reclamacao->created_at
                                    ? date('d/m/Y \à\s H:i', strtotime((string) $reclamacao->created_at))
                                    : '—' ?>
                            </div>
                        </div>
                    </div>
                    <?php if ($hasValorGasto && $reclamacao->valor_gasto !== null): ?>
                    <div class="col-12">
                        <div class="rounded p-2" style="background:#e8f5ec;">
                            <div class="text-muted" style="font-size:.7rem;text-transform:uppercase;letter-spacing:.5px;">Valor Gasto Registrado</div>
                            <div class="fw-bold mt-1" style="color:#1a5c2a;font-size:1.1rem;">
                                R$ <?= number_format((float) $reclamacao->valor_gasto, 2, ',', '.') ?>
                            </div>
                        </div>
                    </div>
                    <?php endif ?>
                </div>

                <!-- Fotos do cidadão -->
                <?php if (!empty($fotos)): ?>
                <h6 class="fw-semibold mb-2" style="color:#237a38;font-size:.8rem;text-transform:uppercase;letter-spacing:.5px;">
                    <i class="bi bi-images me-1"></i>Fotos enviadas pelo cidadão
                </h6>
                <div class="d-flex flex-wrap gap-2">
                    <?php foreach ($fotos as $foto): ?>
                    <?php $fotoPath = Yii::getAlias('@web') . '/uploads/reclamacoes/' . $foto->caminho; ?>
                    <a href="<?= Html::encode($fotoPath) ?>" target="_blank" title="Ver em tamanho original">
                        <img src="<?= Html::encode($fotoPath) ?>"
                             alt="Foto"
                             style="width:80px;height:80px;object-fit:cover;border-radius:8px;border:2px solid #e2e8f0;transition:opacity .2s;"
                             onmouseover="this.style.opacity='.75'"
                             onmouseout="this.style.opacity='1'">
                    </a>
                    <?php endforeach ?>
                </div>
                <?php endif ?>

            </div>
        </div>
    </div>

    <!-- Formulário de atualização -->
    <div class="col-lg-5">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom py-3">
                <h5 class="mb-0 fw-bold" style="color:#1a5c2a;">
                    <i class="bi bi-pencil-square me-2"></i>Atualizar
                </h5>
            </div>
            <div class="card-body">
                <form action="<?= Url::to(['/gestao/atualizar', 'id' => $reclamacao->id]) ?>"
                      method="post"
                      enctype="multipart/form-data">
                    <?= Html::hiddenInput(Yii::$app->request->csrfParam, Yii::$app->request->csrfToken) ?>

                    <!-- Status -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            <i class="bi bi-flag-fill me-1" style="color:#237a38;"></i>Status
                        </label>
                        <select name="status_rec" class="form-select">
                            <?php foreach (Reclamacao::statusLabels() as $val => $lbl): ?>
                            <option value="<?= Html::encode($val) ?>"
                                <?= $reclamacao->status_rec === $val ? 'selected' : '' ?>>
                                <?= Html::encode($lbl) ?>
                            </option>
                            <?php endforeach ?>
                        </select>
                    </div>

                    <!-- Valor Gasto -->
                    <?php if ($hasValorGasto): ?>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            <i class="bi bi-cash-stack me-1" style="color:#237a38;"></i>Valor Gasto
                        </label>
                        <div class="input-group">
                            <span class="input-group-text fw-semibold">R$</span>
                            <input type="number"
                                   name="valor_gasto"
                                   step="0.01"
                                   min="0"
                                   max="99999999"
                                   class="form-control"
                                   placeholder="0,00"
                                   value="<?= $reclamacao->valor_gasto !== null
                                       ? Html::encode(number_format((float) $reclamacao->valor_gasto, 2, '.', ''))
                                       : '' ?>">
                        </div>
                        <div class="form-text">Deixe em branco para não alterar.</div>
                    </div>
                    <?php endif ?>

                    <!-- Observação -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            <i class="bi bi-chat-left-text me-1" style="color:#237a38;"></i>Observação
                            <span class="text-muted fw-normal">(opcional)</span>
                        </label>
                        <textarea name="observacao"
                                  class="form-control"
                                  rows="3"
                                  maxlength="1000"
                                  placeholder="Descreva o que foi feito, materiais usados..."></textarea>
                    </div>

                    <!-- Upload de evidências -->
                    <div class="mb-4">
                        <label class="form-label fw-semibold">
                            <i class="bi bi-camera-fill me-1" style="color:#237a38;"></i>Fotos de Evidência
                            <span class="text-muted fw-normal small"> — JPG, PNG ou WEBP</span>
                        </label>
                        <input type="file"
                               name="evidencias[]"
                               class="form-control"
                               multiple
                               accept="image/jpeg,image/png,image/webp">
                        <div class="form-text">Você pode selecionar múltiplas fotos de uma vez.</div>
                    </div>

                    <button type="submit"
                            class="btn btn-verde-principal w-100 fw-semibold">
                        <i class="bi bi-check-circle-fill me-2"></i>Salvar Alterações
                    </button>
                </form>
            </div>
        </div>
    </div>

</div>

<!-- Evidências do gestor -->
<?php if (!empty($evidencias)): ?>
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white border-bottom py-3">
        <h5 class="mb-0 fw-bold" style="color:#1a5c2a;">
            <i class="bi bi-camera-fill me-2" style="color:#237a38;"></i>Evidências Registradas
            <span class="badge ms-2" style="background:#237a38;font-size:.7rem;"><?= count($evidencias) ?></span>
        </h5>
    </div>
    <div class="card-body">
        <div class="row g-3">
            <?php foreach ($evidencias as $ev): ?>
            <?php $evUrl = Yii::getAlias('@web') . '/uploads/evidencias/' . $ev->caminho; ?>
            <div class="col-6 col-sm-4 col-md-3 col-lg-2">
                <a href="<?= Html::encode($evUrl) ?>" target="_blank">
                    <img src="<?= Html::encode($evUrl) ?>"
                         class="img-fluid rounded shadow-sm"
                         alt="Evidência"
                         style="height:110px;width:100%;object-fit:cover;">
                </a>
                <?php if ($ev->descricao): ?>
                <p class="text-muted mt-1 mb-0" style="font-size:.72rem;"><?= Html::encode($ev->descricao) ?></p>
                <?php endif ?>
                <p class="text-muted mb-0" style="font-size:.68rem;">
                    <?= $ev->created_at ? date('d/m/Y', strtotime((string) $ev->created_at)) : '' ?>
                </p>
            </div>
            <?php endforeach ?>
        </div>
    </div>
</div>
<?php endif ?>

<!-- Timeline de histórico -->
<?php if (!empty($historico)): ?>
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-bottom py-3">
        <h5 class="mb-0 fw-bold" style="color:#1a5c2a;">
            <i class="bi bi-clock-history me-2" style="color:#237a38;"></i>Histórico de Atualizações
        </h5>
    </div>
    <div class="card-body pb-2">
        <?php foreach ($historico as $h): ?>
        <?php
            $sc2 = $statusMap[$h->status_novo] ?? ['badge' => 'status-pendente', 'label' => $h->status_novo];
        ?>
        <div class="d-flex gap-3 mb-3 pb-3 border-bottom">
            <div style="width:34px;height:34px;border-radius:50%;background:#e8f5ec;flex-shrink:0;
                        display:flex;align-items:center;justify-content:center;">
                <i class="bi bi-arrow-right-circle-fill" style="color:#237a38;font-size:.95rem;"></i>
            </div>
            <div style="flex:1;">
                <div class="d-flex align-items-center gap-2 flex-wrap mb-1">
                    <span class="status-badge <?= Html::encode($sc2['badge']) ?>" style="font-size:.72rem;">
                        <?= Html::encode($sc2['label']) ?>
                    </span>
                    <?php if ($h->status_anterior && $h->status_anterior !== $h->status_novo): ?>
                    <span class="text-muted small">
                        ← <?= Html::encode(Reclamacao::statusLabels()[$h->status_anterior] ?? $h->status_anterior) ?>
                    </span>
                    <?php endif ?>
                    <?php if ($h->gestor): ?>
                    <span class="text-muted small ms-auto">por <strong><?= Html::encode($h->gestor) ?></strong></span>
                    <?php endif ?>
                </div>
                <?php if ($h->observacao): ?>
                <p class="text-muted mb-1" style="font-size:.82rem;"><?= Html::encode($h->observacao) ?></p>
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
