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
use app\models\Contato;
use yii\bootstrap5\Html;

class ContatoController extends Controller
{
    public function behaviors(): array
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only'  => ['criar', 'editar', 'deletar'],
                'rules' => [
                    [
                        'actions' => ['criar', 'editar', 'deletar'],
                        'allow'   => true,
                        'roles'   => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class'   => VerbFilter::class,
                'actions' => [
                    'deletar' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Verifica se o usuário logado tem permissão de gestor (permissao = 2).
     * @throws ForbiddenHttpException
     */
    private function checkGestor(): void
    {
        $identity = Yii::$app->user->identity;
        if (!$identity || !$identity->isGestor()) {
            throw new ForbiddenHttpException('Acesso negado. Apenas gestores podem gerenciar contatos.');
        }
    }

    /**
     * Página pública: lista de contatos em cards.
     */
    public function actionIndex(): string
    {
        $contatos = Contato::listaPublica();
        return $this->render('index', ['contatos' => $contatos]);
    }

    /**
     * Criar novo contato (apenas gestor).
     */
    public function actionCriar(): string|Response
    {
        $this->layout = 'sidebar';
        $this->checkGestor();

        $model = new Contato();

        if ($model->load(Yii::$app->request->post())) {
            $model->fotoFile = UploadedFile::getInstance($model, 'fotoFile');

            if ($model->validate()) {
                if ($model->fotoFile) {
                    $fileName = $this->salvarFoto($model->fotoFile);
                    if ($fileName) {
                        $model->foto_caminho = $fileName;
                    }
                }

                if ($model->save(false)) {
                    Yii::$app->session->setFlash('success', 'Contato <strong>' . Html::encode($model->nome) . '</strong> criado com sucesso.');
                    return $this->redirect(['/contato/index']);
                }
            }
        }

        return $this->render('criar', ['model' => $model]);
    }

    /**
     * Editar contato existente (apenas gestor).
     */
    public function actionEditar(int $id): string|Response
    {
        $this->layout = 'sidebar';
        $this->checkGestor();

        $model     = $this->findModel($id);
        $fotoAtual = (string) ($model->foto_caminho ?? '');

        if ($model->load(Yii::$app->request->post())) {
            $model->fotoFile = UploadedFile::getInstance($model, 'fotoFile');

            if ($model->validate()) {
                if ($model->fotoFile) {
                    $fileName = $this->salvarFoto($model->fotoFile, $fotoAtual ?: null);
                    if ($fileName) {
                        $model->foto_caminho = $fileName;
                    }
                } else {
                    // Mantém a foto existente (não sobrescreve)
                    $model->foto_caminho = $fotoAtual ?: null;
                }

                if ($model->save(false)) {
                    Yii::$app->session->setFlash('success', 'Contato <strong>' . Html::encode($model->nome) . '</strong> atualizado com sucesso.');
                    return $this->redirect(['/contato/index']);
                }
            }
        }

        return $this->render('editar', ['model' => $model]);
    }

    /**
     * Excluir contato (POST, apenas gestor).
     */
    public function actionDeletar(int $id): Response
    {
        $this->checkGestor();
        $model = $this->findModel($id);
        $nome  = $model->nome;

        // Remove arquivo de foto se existir
        if ($model->foto_caminho) {
            $uploadDir = $this->getUploadDir();
            @unlink($uploadDir . $model->foto_caminho);
        }

        $model->delete();
        Yii::$app->session->setFlash('success', 'Contato <strong>' . Html::encode($nome) . '</strong> removido com sucesso.');

        return $this->redirect(['/contato/index']);
    }

    // ── Helpers ──────────────────────────────────────────────────────────

    private function findModel(int $id): Contato
    {
        $model = Contato::findOne(['id' => $id]);
        if ($model === null) {
            throw new NotFoundHttpException('Contato não encontrado.');
        }
        return $model;
    }

    private function getUploadDir(): string
    {
        return Yii::getAlias('@webroot') . DIRECTORY_SEPARATOR
             . 'uploads' . DIRECTORY_SEPARATOR
             . 'contatos' . DIRECTORY_SEPARATOR;
    }

    /**
     * Faz o upload da foto e retorna o nome do arquivo salvo.
     * Se houver foto antiga, ela é removida.
     */
    private function salvarFoto(UploadedFile $file, ?string $fotoAntigaFileName = null): ?string
    {
        $allowed = ['image/jpeg', 'image/png', 'image/webp'];
        $extMap  = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];

        $mime = $file->type;
        if (function_exists('mime_content_type') && $file->tempName) {
            $mime = mime_content_type($file->tempName) ?: $mime;
        }

        if (!in_array($mime, $allowed, true)) {
            return null;
        }

        $uploadDir = $this->getUploadDir();
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $ext      = $extMap[$mime] ?? 'jpg';
        $fileName = 'contato_' . uniqid() . '.' . $ext;

        if ($file->saveAs($uploadDir . $fileName)) {
            // Remove foto anterior após salvar a nova com sucesso
            if ($fotoAntigaFileName) {
                @unlink($uploadDir . $fotoAntigaFileName);
            }
            return $fileName;
        }

        return null;
    }
}
