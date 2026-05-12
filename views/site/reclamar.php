<?php

/** @var yii\web\View $this */

use yii\bootstrap5\Html;
use yii\helpers\Url;

$this->title = 'Registrar Reclamação';
$this->params['breadcrumbs'][] = $this->title;

$usuario = Yii::$app->user->identity;
?>

        <!-- Banner do usuário logado -->
        <div class="usuario-banner mb-4">
            <div class="avatar-circle">
                <?= strtoupper(substr($usuario->username, 0, 1)) ?>
            </div>
            <div>
                <p class="mb-0 fw-bold text-verde-escuro">
                    Reclamando como: <span class="text-verde"><?= Html::encode($usuario->username) ?></span>
                </p>
                <p class="mb-0 text-muted small">
                    <i class="bi bi-bell-fill me-1"></i>Você receberá atualizações sobre o andamento desta reclamação
                </p>
            </div>
        </div>

        <div class="row g-5">
            <div class="col-lg-8">
                <div class="form-card">
                    <h3 class="form-card-titulo">
                        <i class="bi bi-clipboard-text text-verde"></i> Dados da Reclamação
                    </h3>

                    <form id="form-reclamacao"
                          method="post"
                          action="<?= Url::to(['/site/reclamar']) ?>"
                          enctype="multipart/form-data" class="mt-3">
                        <?= Html::hiddenInput(Yii::$app->request->csrfParam, Yii::$app->request->csrfToken) ?>

                        <!-- Categoria -->
                        <div class="mb-4">
                            <label class="form-label fw-semibold">
                                <i class="bi bi-grid me-1"></i>Categoria do Problema
                            </label>
                            <div class="categoria-grid">
                                <?php
                                $cats = [
                                    ['val' => 'infraestrutura', 'bi' => 'bi-cone-striped',     'label' => 'Infraestrutura'],
                                    ['val' => 'saneamento',     'bi' => 'bi-droplet-fill',      'label' => 'Saneamento'],
                                    ['val' => 'saude',          'bi' => 'bi-heart-pulse-fill',  'label' => 'Saúde'],
                                    ['val' => 'educacao',       'bi' => 'bi-mortarboard-fill',  'label' => 'Educação'],
                                    ['val' => 'transporte',     'bi' => 'bi-bus-front-fill',    'label' => 'Transporte'],
                                    ['val' => 'seguranca',      'bi' => 'bi-shield-fill-check', 'label' => 'Segurança'],
                                    ['val' => 'meio-ambiente',  'bi' => 'bi-tree-fill',         'label' => 'Meio Ambiente'],
                                    ['val' => 'outros',         'bi' => 'bi-list-check',        'label' => 'Outros'],
                                ];
                                foreach ($cats as $i => $c):
                                ?>
                                <label class="cat-opcao">
                                    <input type="radio" name="categoria" value="<?= Html::encode($c['val']) ?>" <?= $i === 0 ? 'required' : '' ?>>
                                    <span class="cat-opcao-inner cat-<?= Html::encode($c['val']) ?>">
                                        <i class="bi <?= Html::encode($c['bi']) ?> cat-opcao-icone-bi"></i>
                                        <span class="cat-opcao-label"><?= Html::encode($c['label']) ?></span>
                                    </span>
                                </label>
                                <?php endforeach ?>
                            </div>
                        </div>

                        <!-- Título -->
                        <div class="mb-4">
                            <label class="form-label fw-semibold" for="titulo">
                                <i class="bi bi-pencil me-1"></i>Título da Reclamação
                            </label>
                            <input type="text" id="titulo" name="titulo" class="form-control form-control-lg"
                                   placeholder="Resumo breve do problema (ex.: Buraco na Rua das Flores)"
                                   maxlength="200" required>
                        </div>

                        <!-- Descrição -->
                        <div class="mb-4">
                            <label class="form-label fw-semibold" for="descricao">
                                <i class="bi bi-chat-text me-1"></i>Descrição Detalhada
                            </label>
                            <textarea id="descricao" name="descricao" class="form-control" rows="5"
                                      placeholder="Descreva o problema com detalhes: o que aconteceu, desde quando, quantas pessoas são afetadas..."
                                      maxlength="2000"></textarea>
                        </div>

                        <!-- Endereço -->
                        <div class="row mb-4">
                            <div class="col-md-8">
                                <label class="form-label fw-semibold" for="endereco">
                                    <i class="bi bi-geo-alt me-1"></i>Endereço / Localização
                                </label>
                                <input type="text" id="endereco" name="endereco" class="form-control form-control-lg"
                                       placeholder="Rua, número, ponto de referência" maxlength="300">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold" for="bairro">
                                    <i class="bi bi-map me-1"></i>Bairro
                                </label>
                                <input type="text" id="bairro" name="bairro" class="form-control form-control-lg"
                                       placeholder="Bairro" maxlength="100">
                            </div>
                        </div>

                        <!-- Urgência -->
                        <div class="mb-4">
                            <label class="form-label fw-semibold">
                                <i class="bi bi-exclamation-triangle me-1"></i>Nível de Urgência
                            </label>
                            <div class="urgencia-opcoes">
                                <label class="urgencia-opcao urgencia-baixa">
                                    <input type="radio" name="urgencia" value="baixa" checked>
                                    <span class="urgencia-inner">
                                        <i class="bi bi-arrow-down-circle-fill"></i> Baixa
                                    </span>
                                </label>
                                <label class="urgencia-opcao urgencia-media">
                                    <input type="radio" name="urgencia" value="media">
                                    <span class="urgencia-inner">
                                        <i class="bi bi-dash-circle-fill"></i> Média
                                    </span>
                                </label>
                                <label class="urgencia-opcao urgencia-alta">
                                    <input type="radio" name="urgencia" value="alta">
                                    <span class="urgencia-inner">
                                        <i class="bi bi-exclamation-circle-fill"></i> Alta
                                    </span>
                                </label>
                            </div>
                        </div>

                        <!-- Upload de Fotos -->
                        <div class="mb-4">
                            <label class="form-label fw-semibold">
                                <i class="bi bi-images me-1"></i>Fotos do Problema
                                <span class="text-muted fw-normal"> — opcional, até 5 imagens</span>
                            </label>
                            <div class="upload-area" id="upload-area" role="button" tabindex="0"
                                 aria-label="Clique ou arraste fotos aqui">
                                <i class="bi bi-cloud-arrow-up upload-icone"></i>
                                <p class="upload-texto">
                                    Arraste as fotos aqui ou
                                    <span class="upload-link">clique para selecionar</span>
                                </p>
                                <p class="upload-dica">JPG, PNG ou WEBP &bull; Máx. 5 MB cada &bull; Até 5 fotos</p>
                                <input type="file" id="fotos" name="fotos[]"
                                       accept="image/jpeg,image/png,image/webp"
                                       multiple class="upload-input" aria-hidden="true">
                            </div>
                            <div id="preview-fotos" class="preview-fotos"></div>
                        </div>

                        <!-- Botões -->
                        <div class="d-flex gap-3 mt-4">
                            <?= Html::a(
                                '<i class="bi bi-arrow-left me-2"></i>Cancelar',
                                Url::to(['/site/index']),
                                ['class' => 'btn btn-verde-outline']
                            ) ?>
                            <button type="submit" class="btn btn-verde-principal flex-grow-1">
                                <i class="bi bi-send-fill me-2"></i>Enviar Reclamação
                            </button>
                        </div>

                    </form>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <div class="info-card">
                    <h4 class="info-card-titulo">
                        <i class="bi bi-info-circle-fill text-verde me-2"></i>Como funciona?
                    </h4>
                    <ol class="info-passos">
                        <li>
                            <strong>Preencha o formulário</strong><br>
                            <span class="text-muted small">Informe o local e descreva o problema com detalhes</span>
                        </li>
                        <li>
                            <strong>Adicione fotos</strong><br>
                            <span class="text-muted small">Imagens ajudam a identificar o problema mais rapidamente</span>
                        </li>
                        <li>
                            <strong>Envie a reclamação</strong><br>
                            <span class="text-muted small">Sua solicitação é protocolada automaticamente</span>
                        </li>
                        <li>
                            <strong>Acompanhe o status</strong><br>
                            <span class="text-muted small">Receba atualizações sobre o andamento</span>
                        </li>
                    </ol>
                </div>
                <div class="info-card mt-3">
                    <h4 class="info-card-titulo">
                        <i class="bi bi-clock-fill text-verde me-2"></i>Prazo de Resposta
                    </h4>
                    <p class="text-muted small mb-0">
                        Reclamações são respondidas em até
                        <strong class="text-verde">72 horas</strong> úteis após o protocolo.
                    </p>
                </div>
                <div class="info-card mt-3">
                    <h4 class="info-card-titulo">
                        <i class="bi bi-person-check-fill text-verde me-2"></i>Usuário Identificado
                    </h4>
                    <p class="text-muted small mb-2">
                        Esta reclamação será vinculada à sua conta, facilitando o acompanhamento.
                    </p>
                    <?= Html::a(
                        '<i class="bi bi-person me-1"></i>Ver Meu Perfil',
                        Url::to(['/site/perfil']),
                        ['class' => 'btn btn-verde-outline w-100 btn-sm']
                    ) ?>
                </div>
            </div>
        </div>

<?php $this->registerJs("(function () {
    // ── Upload preview ───────────────────────────────────────────────────
    var area  = document.getElementById('upload-area');
    var input = document.getElementById('fotos');
    var prev  = document.getElementById('preview-fotos');

    area.addEventListener('click', function () { input.click(); });
    area.addEventListener('keydown', function (e) { if (e.key === 'Enter' || e.key === ' ') input.click(); });

    function criarPreviews(files) {
        prev.innerHTML = '';
        Array.from(files).slice(0, 5).forEach(function (file) {
            if (!file.type.startsWith('image/')) return;
            var reader = new FileReader();
            reader.onload = function (e) {
                var item = document.createElement('div');
                item.className = 'preview-item';
                var img = document.createElement('img');
                img.src = e.target.result;
                img.className = 'preview-img';
                img.alt = file.name;
                var btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'preview-remove';
                btn.title = 'Remover foto';
                btn.innerHTML = '<i class=\"bi bi-x-circle-fill\"></i>';
                btn.addEventListener('click', function () { item.remove(); });
                item.appendChild(img);
                item.appendChild(btn);
                prev.appendChild(item);
            };
            reader.readAsDataURL(file);
        });
    }

    input.addEventListener('change', function () { criarPreviews(this.files); });
    area.addEventListener('dragover',  function (e) { e.preventDefault(); area.classList.add('drag-over'); });
    area.addEventListener('dragleave', function ()  { area.classList.remove('drag-over'); });
    area.addEventListener('drop', function (e) {
        e.preventDefault();
        area.classList.remove('drag-over');
        criarPreviews(e.dataTransfer.files);
    });

    // ── Prevenção de dupla submissão ────────────────────────────────────
    var form      = document.getElementById('form-reclamacao');
    var submitBtn = form.querySelector('button[type=\"submit\"]');
    form.addEventListener('submit', function () {
        if (submitBtn.disabled) { return; }
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class=\"spinner-border spinner-border-sm me-2\" role=\"status\" aria-hidden=\"true\"></span>Enviando...';
    });
}());
"); ?>
