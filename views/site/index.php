<?php

/**
 * @var yii\web\View $this
 * @var int          $statTotal
 * @var int          $statPctResolvidas
 * @var int|null     $statTempoMedio
 * @var int          $statCategorias
 * @var app\models\Blog[] $blogPosts
 */

use yii\bootstrap5\Html;
use yii\helpers\Url;

$this->title = 'Início';
?>

<!-- HERO SECTION -->
<section class="hero-section">
    <div class="hero-overlay">
        <div class="container">
            <div class="row align-items-center min-vh-hero">
                <div class="col-lg-7">
                    <span class="hero-badge">
                        <i class="bi bi-shield-fill-check me-2"></i>Portal Oficial do Cidadão
                    </span>
                    <h1 class="hero-titulo">
                        Sua voz transforma<br>
                        <span class="hero-destaque">o seu município</span>
                    </h1>
                    <p class="hero-subtitulo">
                        Registre reclamações, acompanhe o andamento e contribua para uma cidade
                        mais organizada, limpa e justa para todos os cidadãos.
                    </p>
                    <div class="hero-acoes">
                        <a href="<?= Url::to(['/site/reclamar']) ?>" class="btn btn-hero-principal">
                            <i class="bi bi-megaphone-fill me-2"></i>Registrar Reclamação
                        </a>
                        <a href="<?= Url::to(['/site/consultar']) ?>" class="btn btn-hero-secundario">
                            <i class="bi bi-search me-2"></i>Consultar Status
                        </a>
                    </div>
                </div>
                <div class="col-lg-5 d-none d-lg-flex justify-content-center">
                    <div class="hero-icone-container">
                        <div class="hero-icone-circulo">
                            <span class="hero-icone-emoji">🌟</span>
                        </div>
                        <div class="hero-icone-badge badge-top">
                            <i class="bi bi-check-circle-fill text-success"></i> 100% Online
                        </div>
                        <div class="hero-icone-badge badge-bottom">
                            <i class="bi bi-clock-fill text-warning"></i> Resposta em 72h
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ESTATÍSTICAS -->
<section class="stats-section">
    <div class="container">
        <div class="row g-0">
            <div class="col-6 col-md-3">
                <div class="stat-card">
                    <span class="stat-numero"><?= number_format($statTotal, 0, ',', '.') ?></span>
                    <span class="stat-label">Reclamações Registradas</span>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="stat-card stat-card-alt">
                    <span class="stat-numero"><?= $statPctResolvidas ?>%</span>
                    <span class="stat-label">Resolvidas</span>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="stat-card">
                    <span class="stat-numero"><?= $statTempoMedio !== null ? $statTempoMedio . 'h' : '—' ?></span>
                    <span class="stat-label">Tempo Médio de Resposta</span>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="stat-card stat-card-alt">
                    <span class="stat-numero"><?= $statCategorias ?></span>
                    <span class="stat-label">Categorias Disponíveis</span>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- COMO FUNCIONA -->
<section class="como-funciona-section">
    <div class="container">
        <div class="section-header text-center">
            <span class="section-tag">Simples e Rápido</span>
            <h2 class="section-titulo">Como funciona?</h2>
            <p class="section-subtitulo">Em apenas 3 passos, sua reclamação chega à prefeitura.</p>
        </div>
        <div class="row g-4 mt-2">
            <div class="col-md-4">
                <div class="passo-card">
                    <div class="passo-numero">01</div>
                    <div class="passo-icone-bi"><i class="bi bi-pencil-square"></i></div>
                    <h4 class="passo-titulo">Descreva o Problema</h4>
                    <p class="passo-texto">
                        Informe o local, tipo de ocorrência e detalhes do problema que você identificou no município.
                    </p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="passo-card passo-card-destaque">
                    <div class="passo-numero">02</div>
                    <div class="passo-icone-bi"><i class="bi bi-send-fill"></i></div>
                    <h4 class="passo-titulo">Envie sua Reclamação</h4>
                    <p class="passo-texto">
                        Sua solicitação é encaminhada automaticamente ao setor responsável da prefeitura.
                    </p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="passo-card">
                    <div class="passo-numero">03</div>
                    <div class="passo-icone-bi"><i class="bi bi-check-circle-fill"></i></div>
                    <h4 class="passo-titulo">Acompanhe a Resolução</h4>
                    <p class="passo-texto">
                        Receba atualizações sobre o andamento e saiba quando o problema for resolvido.
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CATEGORIAS DE RECLAMAÇÃO -->
<section class="categorias-section">
    <div class="container">
        <div class="section-header text-center">
            <span class="section-tag">Áreas de Atuação</span>
            <h2 class="section-titulo">O que você pode reclamar?</h2>
        </div>
        <div class="row g-3 mt-2">
            <?php
            $categorias = [
                ['val' => 'infraestrutura', 'bi' => 'bi-cone-striped',      'titulo' => 'Infraestrutura',  'desc' => 'Buracos, calçadas, iluminação pública'],
                ['val' => 'saneamento',     'bi' => 'bi-droplet-fill',       'titulo' => 'Saneamento',      'desc' => 'Esgoto, água, limpeza urbana'],
                ['val' => 'saude',          'bi' => 'bi-heart-pulse-fill',   'titulo' => 'Saúde',           'desc' => 'Postos, UBS, atendimento médico'],
                ['val' => 'educacao',       'bi' => 'bi-mortarboard-fill',   'titulo' => 'Educação',        'desc' => 'Escolas, creches, estrutura escolar'],
                ['val' => 'transporte',     'bi' => 'bi-bus-front-fill',     'titulo' => 'Transporte',      'desc' => 'Ônibus, terminais, mobilidade'],
                ['val' => 'seguranca',      'bi' => 'bi-shield-fill-check',  'titulo' => 'Segurança',       'desc' => 'Iluminação, câmeras, fiscalização'],
                ['val' => 'meio-ambiente',  'bi' => 'bi-tree-fill',          'titulo' => 'Meio Ambiente',   'desc' => 'Poda, resíduos, área verde'],
                ['val' => 'outros',         'bi' => 'bi-list-check',         'titulo' => 'Outros Serviços', 'desc' => 'Demais serviços municipais'],
            ];
            foreach ($categorias as $cat):
            ?>
            <div class="col-6 col-md-3">
                <a href="<?= Url::to(['/site/reclamar']) ?>" class="categoria-card cat-<?= Html::encode($cat['val']) ?>">
                    <div class="categoria-icone-wrap">
                        <i class="bi <?= Html::encode($cat['bi']) ?>"></i>
                    </div>
                    <span class="categoria-titulo"><?= Html::encode($cat['titulo']) ?></span>
                    <span class="categoria-desc"><?= Html::encode($cat['desc']) ?></span>
                </a>
            </div>
            <?php endforeach ?>
        </div>
    </div>
</section>

<!-- BLOG — CARROSSEL -->
<?php if (!empty($blogPosts)): ?>
<section class="blog-section">
    <div class="container">
        <div class="section-header text-center">
            <span class="section-tag">Novidades</span>
            <h2 class="section-titulo">Acontecimentos da Cidade</h2>
            <p class="section-subtitulo">Fique por dentro das últimas notícias e eventos do município.</p>
        </div>

        <!-- Carrossel Bootstrap 5 -->
        <div id="blogCarousel" class="carousel slide mt-4" data-bs-ride="carousel">

            <!-- Indicadores -->
            <div class="carousel-indicators">
                <?php foreach ($blogPosts as $i => $bp): ?>
                <button
                    type="button"
                    data-bs-target="#blogCarousel"
                    data-bs-slide-to="<?= $i ?>"
                    class="<?= $i === 0 ? 'active' : '' ?>"
                    aria-label="Post <?= $i + 1 ?>"
                ></button>
                <?php endforeach ?>
            </div>

            <!-- Slides -->
            <div class="carousel-inner">
                <?php foreach ($blogPosts as $i => $bp): ?>
                <div class="carousel-item <?= $i === 0 ? 'active' : '' ?>">
                    <div class="blog-carousel-slide">
                        <div class="blog-carousel-img-wrap">
                            <img
                                src="<?= Html::encode($bp->urlCapa()) ?>"
                                alt="<?= Html::encode($bp->titulo) ?>"
                                class="blog-carousel-img"
                                loading="lazy"
                            >
                        </div>
                        <div class="blog-carousel-info">
                            <span class="blog-carousel-date">
                                <i class="bi bi-calendar3 me-1"></i><?= date('d/m/Y', strtotime((string) $bp->created_at)) ?>
                            </span>
                            <h4 class="blog-carousel-titulo"><?= Html::encode($bp->titulo) ?></h4>
                            <p class="blog-carousel-resumo"><?= Html::encode(mb_strimwidth((string) $bp->resumo, 0, 140, '…')) ?></p>
                            <?= Html::a(
                                'Ler mais <i class="bi bi-arrow-right ms-1"></i>',
                                Url::to(['/blog/view', 'id' => $bp->id]),
                                ['class' => 'btn btn-sm btn-success mt-2']
                            ) ?>
                        </div>
                    </div>
                </div>
                <?php endforeach ?>
            </div>

            <!-- Setas de navegação -->
            <button class="carousel-control-prev blog-carousel-btn" type="button" data-bs-target="#blogCarousel" data-bs-slide="prev">
                <span class="carousel-control-prev-icon"></span>
            </button>
            <button class="carousel-control-next blog-carousel-btn" type="button" data-bs-target="#blogCarousel" data-bs-slide="next">
                <span class="carousel-control-next-icon"></span>
            </button>
        </div>

        <!-- Botão Ver Blog -->
        <div class="text-center mt-4">
            <?= Html::a(
                '<i class="bi bi-grid-3x3-gap me-2"></i>Ver todos os posts',
                Url::to(['/blog/index']),
                ['class' => 'btn btn-outline-success px-4']
            ) ?>
        </div>
    </div>
</section>
<?php endif ?>

<!-- CTA FINAL -->
<section class="cta-section">
    <div class="container text-center">
        <h2 class="cta-titulo">Pronto para fazer a diferença?</h2>
        <p class="cta-subtitulo">
            Seu município precisa de você. Registre sua reclamação agora e ajude
            a construir uma cidade melhor para todos.
        </p>
        <div class="d-flex flex-column flex-sm-row gap-3 justify-content-center">
            <a href="<?= Url::to(['/site/reclamar']) ?>" class="btn btn-cta-principal">
                <i class="bi bi-megaphone-fill me-2"></i>Registrar Agora
            </a>
            <?php if (Yii::$app->user->isGuest): ?>
            <a href="<?= Url::to(['/site/registrar']) ?>" class="btn btn-cta-secundario">
                <i class="bi bi-person-plus-fill me-2"></i>Criar Conta Grátis
            </a>
            <?php endif ?>
        </div>
    </div>
</section>

