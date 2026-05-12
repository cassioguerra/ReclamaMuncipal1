<?php

declare(strict_types=1);

namespace app\models;

use Yii;
use app\models\OracleActiveRecord;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;

/**
 * Model para a tabela CONTATO.
 *
 * @property int    $id
 * @property string $nome
 * @property string $cargo
 * @property string $email
 * @property string $telefone
 * @property string $descricao
 * @property string $foto_caminho
 * @property int    $ativo
 * @property int    $ordem
 * @property string $created_at
 * @property string $updated_at
 */
class Contato extends OracleActiveRecord
{
    /** Arquivo temporário para upload de foto (não persistido) */
    /** @var \yii\web\UploadedFile|null */
    public $fotoFile = null;

    public static function tableName(): string
    {
        return 'contato';
    }

    public function behaviors(): array
    {
        return [
            [
                'class'              => TimestampBehavior::class,
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => 'updated_at',
                'value'              => new Expression('NOW()'),
            ],
        ];
    }

    public function rules(): array
    {
        return [
            [['nome'], 'required'],
            ['nome',         'string', 'max' => 200],
            ['cargo',        'string', 'max' => 150],
            ['email',        'email'],
            ['email',        'string', 'max' => 150],
            ['email',        'default', 'value' => null],
            ['telefone',     'string', 'max' => 30],
            ['descricao',    'string', 'max' => 500],
            ['foto_caminho', 'string', 'max' => 500],
            ['ativo',        'integer'],
            ['ativo',        'default', 'value' => 1],
            ['ordem',        'integer'],
            ['ordem',        'default', 'value' => 0],
            ['fotoFile',     'file',
                'skipOnEmpty' => true,
                'extensions'  => 'jpg,jpeg,png,webp',
                'mimeTypes'   => 'image/jpeg,image/png,image/webp',
                'maxSize'     => 5 * 1024 * 1024,
                'tooBig'      => 'A foto não pode ultrapassar 5 MB.',
                'wrongExtension' => 'Apenas arquivos JPG, PNG ou WEBP são aceitos.',
            ],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'id'           => 'ID',
            'nome'         => 'Nome',
            'cargo'        => 'Cargo / Função',
            'email'        => 'E-mail',
            'telefone'     => 'Telefone',
            'descricao'    => 'Descrição',
            'foto_caminho' => 'Foto atual',
            'fotoFile'     => 'Foto',
            'ativo'        => 'Ativo',
            'ordem'        => 'Ordem de exibição',
            'created_at'   => 'Cadastrado em',
            'updated_at'   => 'Atualizado em',
        ];
    }

    /** URL pública da foto, ou null se não houver */
    public function getFotoUrl(): ?string
    {
        if (!$this->foto_caminho) {
            return null;
        }
        return Yii::getAlias('@web') . '/uploads/contatos/' . $this->foto_caminho;
    }

    /** Todos os contatos ativos ordenados para a página pública */
    public static function listaPublica(): array
    {
        return static::find()
            ->where(['ativo' => 1])
            ->orderBy(['ordem' => SORT_ASC, 'nome' => SORT_ASC])
            ->all();
    }
}
