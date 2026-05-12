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
use yii\data\ActiveDataProvider;
use app\models\Reclamacao;
use app\models\ReclamacaoHistorico;
use app\models\ReclamacaoEvidencia;
use yii\bootstrap5\Html;

class GestaoController extends Controller
{
    public function behaviors(): array
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class'   => VerbFilter::class,
                'actions' => [
                    'atualizar' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Verifica se o usuário logado é gestor (permissao = 2).
     * @throws ForbiddenHttpException
     */
    private function checkGestor(): void
    {
        $identity = Yii::$app->user->identity;
        if (!$identity || !$identity->isGestor()) {
            throw new ForbiddenHttpException('Apenas gestores podem acessar esta área.');
        }
    }

    /**
     * Lista todas as reclamações com filtro por status.
     */
    public function actionIndex(): string
    {
        $this->layout = 'sidebar';
        $this->checkGestor();

        $statusFiltro = Yii::$app->request->get('status');
        $statusValidos = [
            Reclamacao::STATUS_PENDENTE,
            Reclamacao::STATUS_ANDAMENTO,
            Reclamacao::STATUS_RESOLVIDA,
            Reclamacao::STATUS_ARQUIVADA,
        ];
        if (!in_array($statusFiltro, $statusValidos, true)) {
            $statusFiltro = null;
        }

        $query = Reclamacao::find()
            ->with(['cidadao', 'categoria'])
            ->orderBy(['id' => SORT_DESC]);

        if ($statusFiltro !== null) {
            $query->andWhere(['status_rec' => $statusFiltro]);
        }

        $provider = new ActiveDataProvider([
            'query'      => $query,
            'key'        => 'id',
            'pagination' => ['pageSize' => 20],
        ]);

        $totais = [
            'total'     => (int) Reclamacao::find()->count(),
            'pendentes' => (int) Reclamacao::find()->where(['status_rec' => Reclamacao::STATUS_PENDENTE])->count(),
            'andamento' => (int) Reclamacao::find()->where(['status_rec' => Reclamacao::STATUS_ANDAMENTO])->count(),
            'resolvidas'=> (int) Reclamacao::find()->where(['status_rec' => Reclamacao::STATUS_RESOLVIDA])->count(),
        ];

        return $this->render('index', [
            'provider'     => $provider,
            'statusFiltro' => $statusFiltro,
            'totais'       => $totais,
        ]);
    }

    /**
     * Exibe detalhes de uma reclamação para o gestor gerenciar.
     */
    public function actionVisualizar(int $id): string
    {
        $this->layout = 'sidebar';
        $this->checkGestor();

        $reclamacao = $this->findModel($id);
        $reclamacao->cidadao;
        $reclamacao->categoria;
        $reclamacao->fotos;
        $reclamacao->historico;

        // Evidências só carregadas se tabela existir
        $evidencias = [];
        try {
            $evidencias = $reclamacao->evidencias;
        } catch (\Exception $e) {
            // tabela RECLAMACAO_EVIDENCIA ainda não existe (migration pendente)
        }

        return $this->render('visualizar', [
            'reclamacao' => $reclamacao,
            'evidencias' => $evidencias,
        ]);
    }

    /**
     * Atualiza status, valor gasto, observação e adiciona evidências (POST).
     */
    public function actionAtualizar(int $id): Response
    {
        $this->checkGestor();
        $reclamacao = $this->findModel($id);

        $statusAnterior = (string) $reclamacao->status_rec;
        $novoStatus     = Yii::$app->request->post('status_rec');
        $observacao     = trim((string) Yii::$app->request->post('observacao', ''));
        $valorGastoPost = Yii::$app->request->post('valor_gasto');

        $statusValidos = [
            Reclamacao::STATUS_PENDENTE,
            Reclamacao::STATUS_ANDAMENTO,
            Reclamacao::STATUS_RESOLVIDA,
            Reclamacao::STATUS_ARQUIVADA,
        ];

        if ($novoStatus && in_array($novoStatus, $statusValidos, true)) {
            $reclamacao->status_rec = $novoStatus;
        }

        // Valor gasto — apenas se coluna existir (migration 003)
        if (in_array('valor_gasto', $reclamacao->attributes(), true)
            && $valorGastoPost !== null && $valorGastoPost !== '') {
            $valorNormalizado = str_replace(',', '.', (string) $valorGastoPost);
            $reclamacao->valor_gasto = is_numeric($valorNormalizado)
                ? (float) $valorNormalizado
                : null;
        }

        if ($reclamacao->save(false)) {
            // Registrar histórico se status mudou
            if ($statusAnterior !== (string) $reclamacao->status_rec) {
                $hist = new ReclamacaoHistorico();
                $hist->reclamacao_id   = (int) $reclamacao->id;
                $hist->status_anterior = $statusAnterior;
                $hist->status_novo     = (string) $reclamacao->status_rec;
                $hist->observacao      = $observacao ?: null;
                $hist->gestor          = (string) Yii::$app->user->identity->username;
                $hist->save(false);
            } elseif ($observacao !== '') {
                // Registro de observação sem mudança de status
                $hist = new ReclamacaoHistorico();
                $hist->reclamacao_id   = (int) $reclamacao->id;
                $hist->status_anterior = $statusAnterior;
                $hist->status_novo     = $statusAnterior;
                $hist->observacao      = $observacao;
                $hist->gestor          = (string) Yii::$app->user->identity->username;
                $hist->save(false);
            }

            // Upload de evidências
            $files = UploadedFile::getInstancesByName('evidencias');
            foreach ($files as $file) {
                $this->salvarEvidencia($file, (int) $reclamacao->id, $observacao ?: null);
            }

            Yii::$app->session->setFlash('success', 'Reclamação atualizada com sucesso.');
        } else {
            Yii::$app->session->setFlash('error', 'Erro ao salvar. Tente novamente.');
        }

        return $this->redirect(['/gestao/visualizar', 'id' => $id]);
    }

    // ── Helpers ──────────────────────────────────────────────────────────

    private function findModel(int $id): Reclamacao
    {
        $model = Reclamacao::findOne(['id' => $id]);
        if ($model === null) {
            throw new NotFoundHttpException('Reclamação não encontrada.');
        }
        return $model;
    }

    /**
     * Salva um arquivo de evidência em disco e cria o registro no banco.
     */
    private function salvarEvidencia(UploadedFile $file, int $reclamacaoId, ?string $descricao): void
    {
        if ($file->error !== UPLOAD_ERR_OK || $file->size === 0) {
            return;
        }

        $allowed = ['image/jpeg', 'image/png', 'image/webp'];
        $extMap  = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];

        $mime = $file->type;
        if ($file->tempName && function_exists('mime_content_type')) {
            $mime = mime_content_type($file->tempName) ?: $mime;
        }

        if (!in_array($mime, $allowed, true)) {
            return;
        }

        $uploadDir = Yii::getAlias('@webroot')
            . DIRECTORY_SEPARATOR . 'uploads'
            . DIRECTORY_SEPARATOR . 'evidencias'
            . DIRECTORY_SEPARATOR;

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $ext      = $extMap[$mime] ?? 'jpg';
        $fileName = 'ev_' . $reclamacaoId . '_' . uniqid() . '.' . $ext;

        if ($file->saveAs($uploadDir . $fileName)) {
            try {
                $ev = new ReclamacaoEvidencia();
                $ev->reclamacao_id = $reclamacaoId;
                $ev->caminho       = $fileName;
                $ev->descricao     = $descricao;
                $ev->save(false);
            } catch (\Exception $e) {
                Yii::error('Erro ao salvar evidência: ' . $e->getMessage(), 'gestao');
                @unlink($uploadDir . $fileName);
            }
        }
    }
}
