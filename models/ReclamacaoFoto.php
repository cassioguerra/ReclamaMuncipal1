<?php

declare(strict_types=1);

namespace app\models;

use app\models\OracleActiveRecord;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;

/**
 * Model para a tabela RECLAMACAO_FOTO.
 *
 * @property int    $id
 * @property int    $reclamacao_id
 * @property string $caminho
 * @property string $nome_original
 * @property string $mime_type
 * @property int    $tamanho_bytes
 * @property string $created_at
 */
class ReclamacaoFoto extends OracleActiveRecord
{
    public static function tableName(): string
    {
        return 'reclamacao_foto';
    }

    public function behaviors(): array
    {
        return [
            [
                'class'             => TimestampBehavior::class,
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => false,
                'value'             => new Expression('NOW()'),
            ],
        ];
    }

    public function rules(): array
    {
        return [
            [['reclamacao_id', 'caminho', 'nome_original'], 'required'],
            ['caminho',       'string', 'max' => 500],
            ['nome_original', 'string', 'max' => 255],
            ['mime_type',     'string', 'max' => 80],
            ['mime_type',     'default', 'value' => 'image/jpeg'],
            ['mime_type',     'in', 'range' => ['image/jpeg', 'image/png', 'image/webp']],
            ['tamanho_bytes', 'integer', 'min' => 0],
            ['reclamacao_id', 'integer'],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'id'            => 'ID',
            'reclamacao_id' => 'Reclamação',
            'caminho'       => 'Caminho do Arquivo',
            'nome_original' => 'Nome Original',
            'mime_type'     => 'Tipo MIME',
            'tamanho_bytes' => 'Tamanho (bytes)',
            'created_at'    => 'Enviado em',
        ];
    }

    public function getReclamacao()
    {
        return $this->hasOne(Reclamacao::class, ['id' => 'reclamacao_id']);
    }

    /** Retorna a URL pública da foto */
    public function getUrl(): string
    {
        return \yii\helpers\Url::base(true) . '/uploads/reclamacoes/' . basename($this->caminho);
    }
}
