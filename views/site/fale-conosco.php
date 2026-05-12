<?php

/** @var yii\web\View $this */
/** @var string $nome */
/** @var string $email */
/** @var string $assunto */
/** @var string $mensagem */

use app\widgets\Alert;
use yii\bootstrap5\Html;
use yii\helpers\Url;

$this->title = 'Fale Conosco';
$this->params['breadcrumbs'][] = $this->title;
?>

<!-- HERO da página -->
<section style="background:linear-gradient(135deg,#1a5c2a 0%,#237a38 60%,#2d9249 100%);padding:3.5rem 0 2.5rem;">
    <div class="container text-center text-white">
        <div class="mb-3">
            <span style="display:inline-flex;align-items:center;justify-content:center;
                         width:64px;height:64px;border-radius:50%;
                         background:rgba(255,255,255,.15);font-size:1.8rem;">
                <i class="bi bi-envelope-open-fill"></i>
            </span>
        </div>
        <h1 class="fw-bold mb-2" style="font-size:2rem;">Fale Conosco</h1>
        <p class="mb-0" style="opacity:.85;max-width:520px;margin:0 auto;font-size:1rem;">
            Dúvidas, sugestões ou elogios? Nossa equipe responde em até 48 horas nos dias úteis.
        </p>
    </div>
</section>

<div class="container py-5">
    <?= Alert::widget() ?>

    <div class="row g-5 justify-content-center">

        <!-- Formulário -->
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-body p-4 p-lg-5">
                    <h4 class="fw-bold mb-1" style="color:#1a5c2a;">
                        <i class="bi bi-chat-dots-fill me-2" style="color:#237a38;"></i>Envie sua mensagem
                    </h4>
                    <p class="text-muted small mb-4">Todos os campos marcados com * são obrigatórios.</p>

                    <form action="<?= Url::to(['/site/fale-conosco']) ?>" method="post">
                        <?= Html::hiddenInput(Yii::$app->request->csrfParam, Yii::$app->request->csrfToken) ?>

                        <div class="row g-3">
                            <div class="col-sm-6">
                                <label class="form-label fw-semibold" for="fc-nome">
                                    Nome completo *
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white"><i class="bi bi-person text-muted"></i></span>
                                    <input type="text" id="fc-nome" name="nome"
                                           class="form-control"
                                           value="<?= Html::encode($nome) ?>"
                                           placeholder="Seu nome"
                                           maxlength="150" required>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <label class="form-label fw-semibold" for="fc-email">
                                    E-mail *
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white"><i class="bi bi-envelope text-muted"></i></span>
                                    <input type="email" id="fc-email" name="email"
                                           class="form-control"
                                           value="<?= Html::encode($email) ?>"
                                           placeholder="seu@email.com"
                                           maxlength="150" required>
                                </div>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold" for="fc-assunto">
                                    Assunto *
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white"><i class="bi bi-tag text-muted"></i></span>
                                    <select id="fc-assunto" name="assunto" class="form-select" required>
                                        <option value="">Selecione o assunto...</option>
                                        <?php
                                        $assuntos = [
                                            'duvida'       => 'Dúvida sobre o sistema',
                                            'reclamacao'   => 'Reclamação sobre atendimento',
                                            'sugestao'     => 'Sugestão de melhoria',
                                            'elogio'       => 'Elogio',
                                            'protocolo'    => 'Problema com protocolo',
                                            'tecnico'      => 'Problema técnico no site',
                                            'outro'        => 'Outro assunto',
                                        ];
                                        foreach ($assuntos as $val => $label):
                                        ?>
                                        <option value="<?= Html::encode($val) ?>"
                                            <?= $assunto === $val ? 'selected' : '' ?>>
                                            <?= Html::encode($label) ?>
                                        </option>
                                        <?php endforeach ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold" for="fc-mensagem">
                                    Mensagem *
                                </label>
                                <textarea id="fc-mensagem" name="mensagem"
                                          class="form-control"
                                          rows="6"
                                          placeholder="Descreva sua dúvida, sugestão ou problema com detalhes..."
                                          maxlength="2000"
                                          required><?= Html::encode($mensagem) ?></textarea>
                                <div class="text-end mt-1">
                                    <span id="fc-contador" class="text-muted" style="font-size:.75rem;">0 / 2000</span>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex flex-column flex-sm-row gap-3 mt-4">
                            <button type="submit"
                                    class="btn btn-verde-principal px-5"
                                    style="background:#237a38;border-color:#237a38;color:#fff;font-weight:600;">
                                <i class="bi bi-send-fill me-2"></i>Enviar Mensagem
                            </button>
                            <a href="<?= Url::to(['/site/index']) ?>" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left me-2"></i>Voltar
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Informações de contato -->
        <div class="col-lg-4">

            <!-- Canais de atendimento -->
            <div class="card border-0 shadow-sm rounded-3 mb-4">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3" style="color:#1a5c2a;">
                        <i class="bi bi-headset me-2" style="color:#237a38;"></i>Canais de Atendimento
                    </h5>
                    <ul class="list-unstyled mb-0">
                        <li class="d-flex align-items-start gap-3 mb-3">
                            <div style="width:38px;height:38px;border-radius:10px;background:#e8f5ec;
                                        display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                <i class="bi bi-envelope-fill" style="color:#237a38;"></i>
                            </div>
                            <div>
                                <div class="fw-semibold small">E-mail</div>
                                <div class="text-muted small">atendimento@municipio.gov.br</div>
                            </div>
                        </li>
                        <li class="d-flex align-items-start gap-3 mb-3">
                            <div style="width:38px;height:38px;border-radius:10px;background:#e8f5ec;
                                        display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                <i class="bi bi-telephone-fill" style="color:#237a38;"></i>
                            </div>
                            <div>
                                <div class="fw-semibold small">Telefone</div>
                                <div class="text-muted small">(00) 3300-0000</div>
                            </div>
                        </li>
                        <li class="d-flex align-items-start gap-3">
                            <div style="width:38px;height:38px;border-radius:10px;background:#e8f5ec;
                                        display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                <i class="bi bi-whatsapp" style="color:#237a38;"></i>
                            </div>
                            <div>
                                <div class="fw-semibold small">WhatsApp</div>
                                <div class="text-muted small">(00) 99999-0000</div>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Horário de atendimento -->
            <div class="card border-0 shadow-sm rounded-3 mb-4">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3" style="color:#1a5c2a;">
                        <i class="bi bi-clock-fill me-2" style="color:#237a38;"></i>Horário de Atendimento
                    </h5>
                    <ul class="list-unstyled mb-0 small">
                        <li class="d-flex justify-content-between py-2 border-bottom">
                            <span class="text-muted">Segunda a Sexta</span>
                            <span class="fw-semibold">08h – 17h</span>
                        </li>
                        <li class="d-flex justify-content-between py-2 border-bottom">
                            <span class="text-muted">Sábado</span>
                            <span class="fw-semibold">08h – 12h</span>
                        </li>
                        <li class="d-flex justify-content-between py-2">
                            <span class="text-muted">Domingo / Feriado</span>
                            <span class="text-danger fw-semibold">Fechado</span>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Tempo de resposta -->
            <div class="rounded-3 p-4" style="background:#e8f5ec;border-left:4px solid #237a38;">
                <div class="d-flex align-items-center gap-2 mb-2">
                    <i class="bi bi-lightning-charge-fill" style="color:#237a38;font-size:1.1rem;"></i>
                    <span class="fw-bold" style="color:#1a5c2a;">Resposta Rápida</span>
                </div>
                <p class="text-muted small mb-0">
                    Mensagens enviadas por este formulário recebem resposta em até
                    <strong style="color:#1a5c2a;">48 horas úteis</strong>.
                    Para urgências, utilize o telefone ou WhatsApp.
                </p>
            </div>

        </div>
    </div>
</div>

<?php $this->registerJs(<<<JS
(function () {
    var ta = document.getElementById('fc-mensagem');
    var ct = document.getElementById('fc-contador');
    if (!ta || !ct) return;
    function update() { ct.textContent = ta.value.length + ' / 2000'; }
    ta.addEventListener('input', update);
    update();
})();
JS) ?>
