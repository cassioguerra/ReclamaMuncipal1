<?php

declare(strict_types=1);

namespace app\models;

use Yii;
use app\models\OracleActiveRecord;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;

/**
 * Model para a tabela RECLAMACAO_EVIDENCIA.
 * Fotos de evidência enviadas pelo gestor ao atualizar uma reclamação.
 *
 * @property int    $id
 * @property int    $reclamacao_id
 * @property string $caminho
 * @property string $descricao
 * @property string $created_at
 *
 * @property Reclamacao $reclamacao
 */
class ReclamacaoEvidencia extends OracleActiveRecord
{
    public static function tableName(): string
    {
        return 'reclamacao_evidencia';
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
            [['reclamacao_id', 'caminho'], 'required'],
            ['caminho',       'string', 'max' => 500],
            ['descricao',     'string', 'max' => 300],
            ['descricao',     'default', 'value' => null],
            ['reclamacao_id', 'integer'],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'id'            => 'ID',
            'reclamacao_id' => 'Reclamação',
            'caminho'       => 'Arquivo',
            'descricao'     => 'Descrição',
            'created_at'    => 'Enviada em',
        ];
    }

    /** URL pública da evidência */
    public function getEvidenciaUrl(): string
    {
        return Yii::getAlias('@web') . '/uploads/evidencias/' . $this->caminho;
    }

    public function getReclamacao()
    {
        return $this->hasOne(Reclamacao::class, ['id' => 'reclamacao_id']);
    }
}
