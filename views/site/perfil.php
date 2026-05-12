<?php

/** @var yii\web\View $this */
/** @var int $total */
/** @var int $resolvidas */
/** @var int $andamento */
/** @var int $respondidas */
/** @var app\models\Reclamacao[] $recentes */

use app\models\Reclamacao;
use yii\bootstrap5\Html;
use yii\helpers\Url;

$this->title = 'Meu Perfil';
$this->params['breadcrumbs'][] = $this->title;

$usuario = Yii::$app->user->identity;
$inicial = strtoupper(substr($usuario->username, 0, 1));

$stats = [
    ['valor' => (string) $total,       'label' => 'Reclamações',  'icone' => 'bi-megaphone-fill',    'cor' => 'stat-azul'],
    ['valor' => (string) $resolvidas,  'label' => 'Resolvidas',   'icone' => 'bi-check-circle-fill', 'cor' => 'stat-verde'],
    ['valor' => (string) $andamento,   'label' => 'Em Andamento', 'icone' => 'bi-arrow-repeat',      'cor' => 'stat-amarelo'],
    ['valor' => $respondidas . '%',    'label' => 'Respondidas',  'icone' => 'bi-bar-chart-fill',    'cor' => 'stat-roxo'],
];
?>

        <!-- Card do Perfil -->
        <div class="perfil-hero mb-5">
            <div class="perfil-avatar-lg"><?= Html::encode($inicial) ?></div>
            <div class="perfil-info">
                <h1 class="perfil-nome"><?= Html::encode($usuario->username) ?></h1>
                <p class="perfil-cargo">
                    <i class="bi bi-person-check-fill me-1"></i>Cidadão Cadastrado
                </p>
                <div class="perfil-acoes">
                    <?= Html::a(
                        '<i class="bi bi-megaphone-fill me-2"></i>Nova Reclamação',
                        Url::to(['/site/reclamar']),
                        ['class' => 'btn btn-verde-principal']
                    ) ?>
                    <?= Html::a(
                        '<i class="bi bi-search me-2"></i>Consultar Status',
                        Url::to(['/site/consultar']),
                        ['class' => 'btn btn-verde-outline']
                    ) ?>
                </div>
            </div>
        </div>

        <!-- Estatísticas -->
        <div class="row g-3 mb-5">
            <?php foreach ($stats as $s): ?>
            <div class="col-6 col-md-3">
                <div class="perfil-stat-card <?= Html::encode($s['cor']) ?>">
                    <i class="bi <?= Html::encode($s['icone']) ?> perfil-stat-icone"></i>
                    <span class="perfil-stat-valor"><?= Html::encode($s['valor']) ?></span>
                    <span class="perfil-stat-label"><?= Html::encode($s['label']) ?></span>
                </div>
            </div>
            <?php endforeach ?>
        </div>

        <div class="row g-4">

            <!-- Reclamações recentes -->
            <div class="col-lg-8">
                <div class="form-card">
                    <h3 class="form-card-titulo">
                        <i class="bi bi-clock-history text-verde"></i> Minhas Reclamações Recentes
                    </h3>

                    <?php if (empty($recentes)): ?>
                        <div class="text-center py-5">
                            <i class="bi bi-inbox" style="font-size: 3rem; color: #ccc;"></i>
                            <p class="text-muted mt-3">Você ainda não registrou nenhuma reclamação.</p>
                            <?= Html::a(
                                '<i class="bi bi-plus-circle me-2"></i>Registrar Agora',
                                Url::to(['/site/reclamar']),
                                ['class' => 'btn btn-verde-principal mt-2']
                            ) ?>
                        </div>
                    <?php else: ?>
                        <div class="reclamacoes-lista mt-3">
                            <?php foreach ($recentes as $rec): ?>
                            <?php
                                $statusMap = [
                                    Reclamacao::STATUS_PENDENTE  => ['label' => 'Pendente',      'class' => 'status-pendente'],
                                    Reclamacao::STATUS_ANDAMENTO => ['label' => 'Em Andamento',  'class' => 'status-andamento'],
                                    Reclamacao::STATUS_RESOLVIDA => ['label' => 'Resolvida',     'class' => 'status-resolvida'],
                                    Reclamacao::STATUS_ARQUIVADA => ['label' => 'Arquivada',     'class' => 'status-arquivada'],
                                ];
                                $sc   = $statusMap[$rec->status_rec] ?? ['label' => $rec->status_rec, 'class' => 'status-pendente'];
                                $ts   = $rec->created_at ? strtotime((string) $rec->created_at) : 0;
                                $data = $ts > 0 ? date('d/m/Y', $ts) : '';
                                $cat  = $rec->categoria ? Html::encode($rec->categoria->nome) : '—';
                            ?>
                            <div class="reclamacao-item">
                                <div class="reclamacao-meta">
                                    <span class="reclamacao-protocolo"><?= Html::encode($rec->protocolo ?? '#' . $rec->id) ?></span>
                                    <span class="reclamacao-data"><?= Html::encode($data) ?></span>
                                </div>
                                <div class="reclamacao-info">
                                    <h5 class="reclamacao-titulo"><?= Html::encode($rec->titulo) ?></h5>
                                    <span class="reclamacao-categoria"><?= $cat ?></span>
                                </div>
                                <div class="reclamacao-status">
                                    <span class="status-badge <?= Html::encode($sc['class']) ?>">
                                        <?= Html::encode($sc['label']) ?>
                                    </span>
                                </div>
                            </div>
                            <?php endforeach ?>
                        </div>
                        <div class="text-center mt-4">
                            <?= Html::a(
                                'Ver todas as reclamações <i class="bi bi-arrow-right ms-1"></i>',
                                Url::to(['/site/consultar']),
                                ['class' => 'btn btn-verde-outline']
                            ) ?>
                        </div>
                    <?php endif ?>
                </div>
            </div>

            <!-- Dados da conta -->
            <div class="col-lg-4">
                <div class="info-card">
                    <h4 class="info-card-titulo">
                        <i class="bi bi-person-circle text-verde me-2"></i>Dados da Conta
                    </h4>
                    <ul class="info-lista">
                        <li>
                            <strong>Usuário</strong>
                            <span><?= Html::encode($usuario->username) ?></span>
                        </li>
                        <li>
                            <strong>ID</strong>
                            <span>#<?= Html::encode($usuario->id) ?></span>
                        </li>
                        <li>
                            <strong>Status</strong>
                            <span class="text-success fw-bold">
                                <i class="bi bi-check-circle-fill me-1"></i>Ativo
                            </span>
                        </li>
                    </ul>
                </div>

                <div class="info-card mt-3">
                    <h4 class="info-card-titulo">
                        <i class="bi bi-shield-check text-verde me-2"></i>Segurança
                    </h4>
                    <ul class="perfil-acoes-lista">
                        <li>
                            <a href="#" class="perfil-acao-link">
                                <i class="bi bi-key me-2"></i>Alterar Senha
                            </a>
                        </li>
                        <li>
                            <a href="#" class="perfil-acao-link">
                                <i class="bi bi-envelope me-2"></i>Alterar E-mail
                            </a>
                        </li>
                        <li>
                            <?= Html::beginForm(['/site/logout']) ?>
                            <?= Html::submitButton(
                                '<i class="bi bi-box-arrow-left me-2"></i>Sair da Conta',
                                ['class' => 'perfil-acao-link text-danger border-0 bg-transparent p-0 w-100 text-start']
                            ) ?>
                            <?= Html::endForm() ?>
                        </li>
                    </ul>
                </div>
            </div>

        </div>
