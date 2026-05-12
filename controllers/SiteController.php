<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;
use app\models\Cidadao;
use app\models\Reclamacao;
use app\models\Categoria;
use app\models\ReclamacaoFoto;
use app\models\ReclamacaoEvidencia;
use app\models\Blog;
use yii\helpers\Html;

class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['logout', 'reclamar', 'perfil', 'dashboard'],
                'rules' => [
                    [
                        'actions' => ['logout', 'reclamar', 'perfil', 'dashboard'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        $db = Yii::$app->db;

        $total = (int) $db->createCommand('SELECT COUNT(*) FROM reclamacao')->queryScalar();

        $resolvidas = (int) $db->createCommand(
            "SELECT COUNT(*) FROM reclamacao WHERE status_rec = 'resolvida'"
        )->queryScalar();

        $pctResolvidas = $total > 0 ? round(($resolvidas / $total) * 100) : 0;

        $tempoMedioHoras = null;
        if ($resolvidas > 0) {
            $raw = $db->createCommand(
                "SELECT AVG(TIMESTAMPDIFF(HOUR, created_at, updated_at))
                   FROM reclamacao WHERE status_rec = 'resolvida'"
            )->queryScalar();
            if ($raw !== null && $raw !== false) {
                $tempoMedioHoras = (int) round((float) $raw);
            }
        }

        $totalCategorias = (int) $db->createCommand(
            'SELECT COUNT(*) FROM categoria WHERE ativo = 1'
        )->queryScalar();

        // Posts recentes do blog para o carrossel (até 6)
        $blogPosts = [];
        try {
            $blogPosts = Blog::find()
                ->where(['ativo' => 1])
                ->orderBy(['id' => SORT_DESC])
                ->limit(6)
                ->all();
        } catch (\Exception $e) {
            // Tabela BLOG ainda não existe (migration pendente)
        }

        return $this->render('index', [
            'statTotal'          => $total,
            'statPctResolvidas'  => (int) $pctResolvidas,
            'statTempoMedio'     => $tempoMedioHoras,
            'statCategorias'     => $totalCategorias,
            'blogPosts'          => $blogPosts,
        ]);
    }

    /**
     * Login action.
     *
     * @return Response|string
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->redirect(['/site/dashboard']);
        }

        $model->password = '';
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Displays contact page.
     *
     * @return Response|string
     */
    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->contact(Yii::$app->params['adminEmail'])) {
            Yii::$app->session->setFlash('contactFormSubmitted');

            return $this->refresh();
        }
        return $this->render('contact', [
            'model' => $model,
        ]);
    }

    /**
     * Displays about page.
     *
     * @return string
     */
    public function actionAbout()
    {
        return $this->render('about');
    }

    /**
     * Registrar reclamação.
     *
     * @return string
     */
    public function actionReclamar()
    {
        $this->layout = 'sidebar';

        if (Yii::$app->request->isPost) {
            $post = Yii::$app->request->post();

            $categoriaSlug = $post['categoria'] ?? '';
            $categoria = Categoria::find()
                ->where(['slug' => $categoriaSlug])
                ->andWhere(['ativo' => 1])
                ->one();

            $model = new Reclamacao();
            $model->cidadao_id   = Yii::$app->user->id;
            $model->categoria_id = $categoria ? $categoria->id : null;
            $model->titulo       = $post['titulo'] ?? '';
            $model->descricao    = $post['descricao'] ?? '';
            $model->endereco     = $post['endereco'] ?? '';
            $model->bairro       = $post['bairro'] ?? '';
            $model->urgencia     = in_array($post['urgencia'] ?? '', [
                Reclamacao::URGENCIA_BAIXA,
                Reclamacao::URGENCIA_MEDIA,
                Reclamacao::URGENCIA_ALTA,
            ], true) ? $post['urgencia'] : Reclamacao::URGENCIA_BAIXA;
            $model->status_rec   = Reclamacao::STATUS_PENDENTE;

            if ($model->save()) {
                // Recarrega do banco para obter o protocolo gerado em afterSave
                $saved = Reclamacao::find()
                    ->where(['cidadao_id' => Yii::$app->user->id])
                    ->orderBy(['id' => SORT_DESC])
                    ->one();
                if ($saved !== null) {
                    $model = $saved;
                }

                // Processa fotos enviadas
                try {
                    $uploadedFiles = \yii\web\UploadedFile::getInstancesByName('fotos');
                    if (!empty($uploadedFiles) && $model->id) {
                        $uploadDir = Yii::getAlias('@webroot') . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'reclamacoes' . DIRECTORY_SEPARATOR;
                        if (!is_dir($uploadDir)) {
                            mkdir($uploadDir, 0755, true);
                        }
                        $allowed = ['image/jpeg', 'image/png', 'image/webp'];
                        $extMap  = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
                        foreach ($uploadedFiles as $file) {
                            if ($file->error !== UPLOAD_ERR_OK) {
                                continue;
                            }
                            $mime = $file->type;
                            if (!in_array($mime, $allowed, true) && function_exists('mime_content_type')) {
                                $mime = mime_content_type($file->tempName) ?: $mime;
                            }
                            if (!in_array($mime, $allowed, true)) {
                                continue;
                            }
                            $ext      = $extMap[$mime] ?? 'jpg';
                            $fileName = $model->id . '_' . uniqid() . '.' . $ext;
                            if ($file->saveAs($uploadDir . $fileName)) {
                                $foto                = new ReclamacaoFoto();
                                $foto->reclamacao_id = $model->id;
                                $foto->caminho       = $fileName;
                                $foto->nome_original = $file->name;
                                $foto->mime_type     = $mime;
                                $foto->tamanho_bytes = (int) $file->size;
                                if (!$foto->save()) {
                                    Yii::error('Erro ao salvar ReclamacaoFoto: ' . json_encode($foto->errors), 'reclamar');
                                }
                            }
                        }
                    }
                } catch (\Throwable $e) {
                    Yii::error('Erro ao salvar fotos: ' . $e->getMessage(), 'reclamar');
                }

                $protocolo = $model->protocolo ?: ('#' . $model->id);
                Yii::$app->session->setFlash(
                    'success',
                    'Reclamação registrada com sucesso! Protocolo: ' . Html::encode((string) $protocolo)
                );
                return $this->redirect(['/site/consultar']);
            }

            // Erros de validação
            $erros = [];
            foreach ($model->getErrors() as $attr => $msgs) {
                $erros[] = $model->getAttributeLabel($attr) . ': ' . $msgs[0];
            }
            Yii::$app->session->setFlash('error', implode('; ', $erros));
        }

        return $this->render('reclamar');
    }

    /**
     * Consultar status de reclamação.
     *
     * @return string
     */
    public function actionConsultar()
    {
        $this->layout = Yii::$app->user->isGuest ? 'main' : 'sidebar';

        $protocolo = trim((string) Yii::$app->request->get('protocolo', ''));
        $cidadaoId = Yii::$app->user->isGuest ? null : (int) Yii::$app->user->id;

        $query = Reclamacao::find()->with(['categoria']);

        if ($protocolo !== '') {
            $query->andWhere(['protocolo' => strtoupper($protocolo)]);
        }

        $reclamacoes = $query->orderBy(['id' => SORT_DESC])->limit(100)->all();

        return $this->render('consultar', [
            'reclamacoes' => $reclamacoes,
            'protocolo'   => $protocolo,
            'cidadaoId'   => $cidadaoId,
        ]);
    }

    /**
     * Dashboard do cidadão logado.
     */
    public function actionDashboard()
    {
        $this->layout = 'sidebar';
        $cidadao   = Yii::$app->user->identity;
        $cidadaoId = Yii::$app->user->id;

        $total     = Reclamacao::find()->where(['cidadao_id' => $cidadaoId])->count();
        $pendentes = Reclamacao::find()->where(['cidadao_id' => $cidadaoId, 'status_rec' => Reclamacao::STATUS_PENDENTE])->count();
        $andamento = Reclamacao::find()->where(['cidadao_id' => $cidadaoId, 'status_rec' => Reclamacao::STATUS_ANDAMENTO])->count();
        $resolvidas= Reclamacao::find()->where(['cidadao_id' => $cidadaoId, 'status_rec' => Reclamacao::STATUS_RESOLVIDA])->count();

        $recentes = Reclamacao::find()
            ->with(['categoria'])
            ->where(['cidadao_id' => $cidadaoId])
            ->orderBy(['id' => SORT_DESC])
            ->limit(5)
            ->all();

        return $this->render('dashboard', [
            'cidadao'    => $cidadao,
            'total'      => (int) $total,
            'pendentes'  => (int) $pendentes,
            'andamento'  => (int) $andamento,
            'resolvidas' => (int) $resolvidas,
            'recentes'   => $recentes,
        ]);
    }

    /**
     * Criar conta de cidadão.
     */
    public function actionRegistrar()
    {
        $model = new Cidadao();
        $model->scenario = 'cadastro';

        if ($model->load(Yii::$app->request->post())) {
            $model->setPassword($model->senha);
            $model->generateAuthKey();
            $model->ativo = 1;

            if ($model->save()) {
                Yii::$app->session->setFlash('success',
                    'Conta criada com sucesso! Acesse com seu e-mail e senha.'
                );
                return $this->redirect(['/site/login']);
            }
        }

        return $this->render('registrar', [
            'model' => $model,
        ]);
    }

    /**
     * Detalhe público de uma reclamação.
     */
    public function actionDetalhe(int $id): string
    {
        $this->layout = Yii::$app->user->isGuest ? 'main' : 'sidebar';

        $cidadaoId = Yii::$app->user->isGuest ? null : (int) Yii::$app->user->id;

        $query = Reclamacao::find()
            ->with(['categoria', 'fotos', 'historico'])
            ->where(['id' => $id]);

        // Cidadão logado só vê detalhe completo da própria reclamação;
        // visitantes veem qualquer reclamação (leitura pública)
        $reclamacao = $query->one();

        if ($reclamacao === null) {
            throw new \yii\web\NotFoundHttpException('Reclamação não encontrada.');
        }

        $isOwner = $cidadaoId !== null && (int) $reclamacao->cidadao_id === $cidadaoId;

        // Evidências do gestor — requer migration 003_gestao_reclamacao.sql
        $evidencias = [];
        try {
            $evidencias = $reclamacao->evidencias;
        } catch (\Exception $e) {
            Yii::warning('RECLAMACAO_EVIDENCIA não encontrada. Execute migration 003.', 'app');
        }

        return $this->render('detalhe', [
            'reclamacao' => $reclamacao,
            'evidencias' => $evidencias,
            'isOwner'    => $isOwner,
        ]);
    }

    /**
     * Perfil do usuário logado.
     *
     * @return string
     */
    public function actionPerfil()
    {
        $this->layout = 'sidebar';
        $cidadaoId = (int) Yii::$app->user->id;

        $total     = (int) Reclamacao::find()->where(['cidadao_id' => $cidadaoId])->count();
        $resolvidas = (int) Reclamacao::find()->where(['cidadao_id' => $cidadaoId, 'status_rec' => Reclamacao::STATUS_RESOLVIDA])->count();
        $andamento  = (int) Reclamacao::find()->where(['cidadao_id' => $cidadaoId, 'status_rec' => Reclamacao::STATUS_ANDAMENTO])->count();
        $pendentes  = (int) Reclamacao::find()->where(['cidadao_id' => $cidadaoId, 'status_rec' => Reclamacao::STATUS_PENDENTE])->count();
        $respondidas = $total > 0 ? round((($total - $pendentes) / $total) * 100) : 0;

        $recentes = Reclamacao::find()
            ->with(['categoria'])
            ->where(['cidadao_id' => $cidadaoId])
            ->orderBy(['id' => SORT_DESC])
            ->limit(5)
            ->all();

        return $this->render('perfil', [
            'total'       => $total,
            'resolvidas'  => $resolvidas,
            'andamento'   => $andamento,
            'respondidas' => $respondidas,
            'recentes'    => $recentes,
        ]);
    }

    /**
     * Fale Conosco — página pública.
     */
    public function actionFaleConosco()
    {
        $nome     = trim((string) Yii::$app->request->post('nome',     ''));
        $email    = trim((string) Yii::$app->request->post('email',    ''));
        $assunto  = trim((string) Yii::$app->request->post('assunto',  ''));
        $mensagem = trim((string) Yii::$app->request->post('mensagem', ''));

        if (Yii::$app->request->isPost) {
            $erros = [];
            if ($nome === '')     $erros[] = 'Nome é obrigatório.';
            if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) $erros[] = 'E-mail inválido.';
            if ($assunto === '')  $erros[] = 'Assunto é obrigatório.';
            if ($mensagem === '') $erros[] = 'Mensagem é obrigatória.';

            if (empty($erros)) {
                Yii::$app->session->setFlash('success', 'Mensagem enviada com sucesso! Entraremos em contato em breve.');
                return $this->redirect(['/site/fale-conosco']);
            }
            Yii::$app->session->setFlash('error', implode(' ', $erros));
        }

        return $this->render('fale-conosco', [
            'nome'     => $nome,
            'email'    => $email,
            'assunto'  => $assunto,
            'mensagem' => $mensagem,
        ]);
    }
}
