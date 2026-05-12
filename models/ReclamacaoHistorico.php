<?php

declare(strict_types=1);

namespace app\models;

use app\models\OracleActiveRecord;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;

/**
 * Model para a tabela RECLAMACAO_HISTORICO.
 * Registra cada mudança de status de uma reclamação (auditoria).
 *
 * @property int    $id
 * @property int    $reclamacao_id
 * @property string $status_anterior
 * @property string $status_novo
 * @property string $observacao
 * @property string $gestor
 * @property string $created_at
 */
class ReclamacaoHistorico extends OracleActiveRecord
{
    public static function tableName(): string
    {
        return 'reclamacao_historico';
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
            [['reclamacao_id', 'status_novo'], 'required'],
            ['status_anterior', 'string', 'max' => 15],
            ['status_novo',     'string', 'max' => 15],
            ['observacao',      'string', 'max' => 1000],
            ['gestor',          'string', 'max' => 100],
            ['reclamacao_id',   'integer'],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'id'              => 'ID',
            'reclamacao_id'   => 'Reclamação',
            'status_anterior' => 'Status Anterior',
            'status_novo'     => 'Novo Status',
            'observacao'      => 'Observação',
            'gestor'          => 'Gestor',
            'created_at'      => 'Data',
        ];
    }

    public function getReclamacao()
    {
        return $this->hasOne(Reclamacao::class, ['id' => 'reclamacao_id']);
    }
}
