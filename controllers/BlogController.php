<?php

declare(strict_types=1);

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\web\UploadedFile;
use app\models\Blog;

class BlogController extends Controller
{
    public function behaviors(): array
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only'  => ['create', 'update', 'delete'],
                'rules' => [
                    [
                        'actions' => ['create', 'update', 'delete'],
                        'allow'   => true,
                        'roles'   => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class'   => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    // ── Helpers ──────────────────────────────────────────────────────────

    private function checkGestor(): void
    {
        $identity = Yii::$app->user->identity;
        if (!$identity || !$identity->isGestor()) {
            throw new ForbiddenHttpException('Apenas gestores podem gerenciar o blog.');
        }
    }

    private function findModel(int $id): Blog
    {
        $model = Blog::findOne(['id' => $id]);
        if ($model === null) {
            throw new NotFoundHttpException('Post não encontrado.');
        }
        return $model;
    }

    /**
     * Salva o arquivo de imagem em /uploads/blog/ e retorna o nome do arquivo.
     * Retorna null se não houver arquivo válido.
     */
    private function salvarImagem(UploadedFile $file, string $prefixo): ?string
    {
        if ($file->error !== UPLOAD_ERR_OK || $file->size === 0) {
            return null;
        }

        $allowed = ['image/jpeg', 'image/png', 'image/webp'];
        $extMap  = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];

        $mime = $file->type;
        if ($file->tempName && function_exists('mime_content_type')) {
            $detected = mime_content_type($file->tempName);
            if ($detected !== false) {
                $mime = $detected;
            }
        }

        if (!in_array($mime, $allowed, true)) {
            return null;
        }

        $uploadDir = Yii::getAlias('@webroot')
            . DIRECTORY_SEPARATOR . 'uploads'
            . DIRECTORY_SEPARATOR . 'blog'
            . DIRECTORY_SEPARATOR;

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $ext      = $extMap[$mime] ?? 'jpg';
        $fileName = $prefixo . '_' . uniqid() . '.' . $ext;

        if ($file->saveAs($uploadDir . $fileName)) {
            return $fileName;
        }

        return null;
    }

    // ── Actions ──────────────────────────────────────────────────────────

    /**
     * Lista todos os posts ativos (público).
     */
    public function actionIndex(): string
    {
        $this->layout = Yii::$app->user->isGuest ? 'main' : 'sidebar';

        $posts = Blog::find()
            ->with('cidadao')
            ->where(['ativo' => 1])
            ->orderBy(['id' => SORT_DESC])
            ->all();

        $isGestor = !Yii::$app->user->isGuest
            && Yii::$app->user->identity
            && Yii::$app->user->identity->isGestor();

        $isPublic   = Yii::$app->user->isGuest;
        $wrapOpen   = $isPublic ? '<div class="container py-5">' : '';
        $wrapClose  = $isPublic ? '</div>' : '';

        $this->view->params['breadcrumbs'][] = 'Blog';

        return $this->render('index', [
            'posts'    => $posts,
            'isGestor' => $isGestor,
            'wrapOpen'  => $wrapOpen,
            'wrapClose' => $wrapClose,
        ]);
    }

    /**
     * Exibe um post individual (público).
     */
    public function actionView(int $id): string
    {
        $this->layout = Yii::$app->user->isGuest ? 'main' : 'sidebar';

        $post = Blog::find()
            ->with('cidadao')
            ->where(['id' => $id, 'ativo' => 1])
            ->one();

        if ($post === null) {
            throw new NotFoundHttpException('Post não encontrado ou indisponível.');
        }

        $isGestor = !Yii::$app->user->isGuest
            && Yii::$app->user->identity
            && Yii::$app->user->identity->isGestor();

        $isPublic  = Yii::$app->user->isGuest;
        $wrapOpen  = $isPublic ? '<div class="container-fluid px-0">' : '';
        $wrapClose = $isPublic ? '</div>' : '';

        $this->view->params['breadcrumbs'][] = ['label' => 'Blog', 'url' => ['/blog/index']];
        $this->view->params['breadcrumbs'][] = $post->titulo;

        return $this->render('view', [
            'post'      => $post,
            'isGestor'  => $isGestor,
            'wrapOpen'  => $wrapOpen,
            'wrapClose' => $wrapClose,
        ]);
    }

    /**
     * Cria novo post (somente gestor).
     */
    public function actionCreate(): Response|string
    {
        $this->layout = 'sidebar';
        $this->checkGestor();

        $model = new Blog();

        if (Yii::$app->request->isPost) {
            $model->load(Yii::$app->request->post());
            $model->cidadao_id = (int) Yii::$app->user->id;

            $model->foto_capa_file   = UploadedFile::getInstance($model, 'foto_capa_file');
            $model->foto_banner_file = UploadedFile::getInstance($model, 'foto_banner_file');

            // Validação manual das fotos obrigatórias na criação
            $erros = [];
            if ($model->foto_capa_file === null) {
                $erros[] = 'A foto de capa é obrigatória.';
            }
            if ($model->foto_banner_file === null) {
                $erros[] = 'A foto de apresentação é obrigatória.';
            }

            if (empty($erros) && $model->validate(['titulo', 'resumo', 'conteudo', 'foto_capa_file', 'foto_banner_file'])) {
                $nomeCapa   = $this->salvarImagem($model->foto_capa_file, 'capa');
                $nomeBanner = $this->salvarImagem($model->foto_banner_file, 'banner');

                if ($nomeCapa === null || $nomeBanner === null) {
                    Yii::$app->session->setFlash('error', 'Erro ao salvar as imagens. Verifique o formato (JPG, PNG ou WebP).');
                    return $this->render('create', ['model' => $model]);
                }

                $model->foto_capa   = $nomeCapa;
                $model->foto_banner = $nomeBanner;

                if ($model->save(false)) {
                    Yii::$app->session->setFlash('success', 'Post publicado com sucesso!');
                    return $this->redirect(['/blog/view', 'id' => $model->id]);
                }

                Yii::$app->session->setFlash('error', 'Erro ao salvar o post. Tente novamente.');
            } else {
                foreach ($erros as $e) {
                    Yii::$app->session->setFlash('error', $e);
                }
            }
        }

        $this->view->params['breadcrumbs'][] = ['label' => 'Blog', 'url' => ['/blog/index']];
        $this->view->params['breadcrumbs'][] = 'Novo Post';

        return $this->render('create', ['model' => $model]);
    }

    /**
     * Edita um post existente (somente gestor).
     */
    public function actionUpdate(int $id): Response|string
    {
        $this->layout = 'sidebar';
        $this->checkGestor();

        $model = $this->findModel($id);

        if (Yii::$app->request->isPost) {
            $model->load(Yii::$app->request->post());

            $model->foto_capa_file   = UploadedFile::getInstance($model, 'foto_capa_file');
            $model->foto_banner_file = UploadedFile::getInstance($model, 'foto_banner_file');

            if ($model->validate(['titulo', 'resumo', 'conteudo', 'foto_capa_file', 'foto_banner_file'])) {
                // Substituir fotos somente se novos arquivos foram enviados
                if ($model->foto_capa_file !== null) {
                    $nomeCapa = $this->salvarImagem($model->foto_capa_file, 'capa');
                    if ($nomeCapa !== null) {
                        $model->foto_capa = $nomeCapa;
                    }
                }

                if ($model->foto_banner_file !== null) {
                    $nomeBanner = $this->salvarImagem($model->foto_banner_file, 'banner');
                    if ($nomeBanner !== null) {
                        $model->foto_banner = $nomeBanner;
                    }
                }

                if ($model->save(false)) {
                    Yii::$app->session->setFlash('success', 'Post atualizado com sucesso!');
                    return $this->redirect(['/blog/view', 'id' => $model->id]);
                }

                Yii::$app->session->setFlash('error', 'Erro ao salvar. Tente novamente.');
            }
        }

        $this->view->params['breadcrumbs'][] = ['label' => 'Blog', 'url' => ['/blog/index']];
        $this->view->params['breadcrumbs'][] = ['label' => $model->titulo, 'url' => ['/blog/view', 'id' => $model->id]];
        $this->view->params['breadcrumbs'][] = 'Editar';

        return $this->render('update', ['model' => $model]);
    }

    /**
     * Exclui um post (somente gestor, somente POST).
     */
    public function actionDelete(int $id): Response
    {
        $this->checkGestor();
        $model = $this->findModel($id);

        // Remove arquivos de imagem
        $uploadDir = Yii::getAlias('@webroot') . '/uploads/blog/';
        foreach ([$model->foto_capa, $model->foto_banner] as $arquivo) {
            if ($arquivo) {
                $caminho = $uploadDir . $arquivo;
                if (is_file($caminho)) {
                    @unlink($caminho);
                }
            }
        }

        $model->delete();

        Yii::$app->session->setFlash('success', 'Post excluído com sucesso.');
        return $this->redirect(['/blog/index']);
    }
}
