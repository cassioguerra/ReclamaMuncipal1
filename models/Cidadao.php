<?php

declare(strict_types=1);

namespace app\models;

use Yii;
use app\models\OracleActiveRecord;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;
use yii\web\IdentityInterface;

/**
 * Model ActiveRecord para a tabela CIDADAO.
 *
 * @property int    $id
 * @property string $username
 * @property string $email
 * @property string $nome_completo
 * @property string $telefone
 * @property string $senha_hash
 * @property string $auth_key
 * @property string $access_token
 * @property int    $ativo
 * @property string $created_at
 * @property string $updated_at
 */
class Cidadao extends OracleActiveRecord implements IdentityInterface
{
    /** Senha em texto plano — apenas durante cadastro/alteração (não persistida) */
    public ?string $senha = null;
    public ?string $senha_confirmar = null;

    public static function tableName(): string
    {
        return 'cidadao';
    }

    public function behaviors(): array
    {
        return [
            [
                'class'             => TimestampBehavior::class,
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => 'updated_at',
                'value'             => new Expression('NOW()'),
            ],
        ];
    }

    public function rules(): array
    {
        return [
            [['username', 'email', 'nome_completo'], 'required'],
            ['email',        'email'],
            ['email',        'string', 'max' => 150],
            ['username',     'string', 'min' => 3, 'max' => 50],
            ['username',     'match', 'pattern' => '/^[a-zA-Z0-9._-]+$/'],
            ['nome_completo','string', 'max' => 200],
            ['telefone',     'string', 'max' => 20],
            ['permissao',    'integer'],
            ['permissao',    'default', 'value' => 1],
            ['permissao',    'in', 'range' => [1, 2]],
            ['ativo',        'integer'],
            ['ativo',        'default', 'value' => 1],
            ['email',        'unique', 'message' => 'Este e-mail já está em uso.'],
            ['username',     'unique', 'message' => 'Este nome de usuário já está em uso.'],
            // Cenário de cadastro — senha obrigatória
            ['senha',          'required', 'on' => 'cadastro'],
            ['senha',          'string',   'min' => 8, 'on' => 'cadastro'],
            ['senha_confirmar','required', 'on' => 'cadastro'],
            ['senha_confirmar','compare',  'compareAttribute' => 'senha',
                               'message' => 'As senhas não coincidem.', 'on' => 'cadastro'],        ];
    }

    public function scenarios(): array
    {
        $scenarios = parent::scenarios();
        $scenarios['cadastro'] = ['username', 'email', 'nome_completo', 'telefone',
                                  'senha', 'senha_confirmar'];
        return $scenarios;
    }

    public function attributeLabels(): array
    {
        return [
            'id'            => 'ID',
            'username'      => 'Usuário',
            'email'         => 'E-mail',
            'nome_completo' => 'Nome Completo',
            'telefone'      => 'Telefone',
            'permissao'     => 'Permissão',
            'ativo'         => 'Ativo',
            'created_at'    => 'Cadastrado em',
            'updated_at'    => 'Atualizado em',
        ];
    }

    /** Verifica se o cidadão tem permissão de gestor (defensivo: retorna false se a coluna não existir no banco) */
    public function isGestor(): bool
    {
        if (!in_array('permissao', $this->attributes(), true)) {
            return false;
        }
        return (int) $this->getAttribute('permissao') === 2;
    }

    // -- IdentityInterface ------------------------------------------

    public static function findIdentity($id): ?self
    {
        return static::findOne(['id' => $id, 'ativo' => 1]);
    }

    public static function findIdentityByAccessToken($token, $type = null): ?self
    {
        return static::findOne(['access_token' => $token, 'ativo' => 1]);
    }

    /**
     * Localiza cidadão por username ou e-mail (case-insensitive).
     */
    public static function findByUsername(string $username): ?self
    {
        return static::find()
            ->where(['ativo' => 1])
            ->andWhere(['or',
                ['username' => $username],
                ['email'    => $username],
            ])
            ->one();
    }

    public function getId(): int
    {
        return (int) $this->id;
    }

    public function getAuthKey(): string
    {
        return (string) $this->auth_key;
    }

    public function validateAuthKey($authKey): bool
    {
        return $this->auth_key === $authKey;
    }

    // -- Senha ------------------------------------------------------

    public function validatePassword(string $password): bool
    {
        return Yii::$app->security->validatePassword($password, $this->senha_hash);
    }

    public function setPassword(string $password): void
    {
        $this->senha_hash = Yii::$app->security->generatePasswordHash($password);
    }

    public function generateAuthKey(): void
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    // -- Relações ---------------------------------------------------

    public function getReclamacoes()
    {
        return $this->hasMany(Reclamacao::class, ['cidadao_id' => 'id'])
                    ->orderBy(['created_at' => SORT_DESC]);
    }
}
