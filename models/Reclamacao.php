<?php

declare(strict_types=1);

namespace app\models;

use Yii;
use app\models\OracleActiveRecord;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;

/**
 * Model para a tabela RECLAMACAO.
 *
 * @property int    $id
 * @property string $protocolo
 * @property int    $cidadao_id
 * @property int    $categoria_id
 * @property string $titulo
 * @property string $descricao
 * @property string $endereco
 * @property string $bairro
 * @property string $urgencia
 * @property string $status_rec
 * @property string $created_at
 * @property string $updated_at
 * @property float|null $valor_gasto
 *
 * @property Cidadao              $cidadao
 * @property Categoria            $categoria
 * @property ReclamacaoFoto[]     $fotos
 * @property ReclamacaoHistorico[] $historico
 */
class Reclamacao extends OracleActiveRecord
{
    public const URGENCIA_BAIXA  = 'baixa';
    public const URGENCIA_MEDIA  = 'media';
    public const URGENCIA_ALTA   = 'alta';

    public const STATUS_PENDENTE  = 'pendente';
    public const STATUS_ANDAMENTO = 'andamento';
    public const STATUS_RESOLVIDA = 'resolvida';
    public const STATUS_ARQUIVADA = 'arquivada';

    public static function tableName(): string
    {
        return 'reclamacao';
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
            [['cidadao_id', 'categoria_id', 'titulo', 'descricao'], 'required'],
            ['titulo',    'string', 'max' => 200],
            ['descricao', 'string', 'max' => 2000],
            ['endereco',  'string', 'max' => 300],
            ['bairro',    'string', 'max' => 100],
            [['cidadao_id', 'categoria_id'], 'integer'],
            ['urgencia',  'in', 'range' => [self::URGENCIA_BAIXA, self::URGENCIA_MEDIA, self::URGENCIA_ALTA]],
            ['urgencia',  'default', 'value' => self::URGENCIA_BAIXA],
            ['status_rec','in', 'range' => [self::STATUS_PENDENTE, self::STATUS_ANDAMENTO, self::STATUS_RESOLVIDA, self::STATUS_ARQUIVADA]],
            ['status_rec','default', 'value' => self::STATUS_PENDENTE],
            ['valor_gasto', 'number', 'min' => 0, 'max' => 99999999.99],
            ['valor_gasto', 'default', 'value' => null],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'id'           => 'ID',
            'protocolo'    => 'Protocolo',
            'cidadao_id'   => 'Cidadão',
            'categoria_id' => 'Categoria',
            'titulo'       => 'Título',
            'descricao'    => 'Descrição',
            'endereco'     => 'Endereço',
            'bairro'       => 'Bairro',
            'urgencia'     => 'Urgência',
            'status_rec'   => 'Status',
            'created_at'   => 'Registrado em',
            'updated_at'   => 'Atualizado em',
            'valor_gasto'  => 'Valor Gasto (R$)',
        ];
    }

    // -- Auxiliares de label ----------------------------------------

    public static function urgenciaLabels(): array
    {
        return [
            self::URGENCIA_BAIXA => 'Baixa',
            self::URGENCIA_MEDIA => 'Média',
            self::URGENCIA_ALTA  => 'Alta',
        ];
    }

    public static function statusLabels(): array
    {
        return [
            self::STATUS_PENDENTE  => 'Pendente',
            self::STATUS_ANDAMENTO => 'Em Andamento',
            self::STATUS_RESOLVIDA => 'Resolvida',
            self::STATUS_ARQUIVADA => 'Arquivada',
        ];
    }

    public function getUrgenciaLabel(): string
    {
        return self::urgenciaLabels()[$this->urgencia] ?? $this->urgencia;
    }

    public function getStatusLabel(): string
    {
        return self::statusLabels()[$this->status_rec] ?? $this->status_rec;
    }

    // -- Relações ---------------------------------------------------

    public function getCidadao()
    {
        return $this->hasOne(Cidadao::class, ['id' => 'cidadao_id']);
    }

    public function getCategoria()
    {
        return $this->hasOne(Categoria::class, ['id' => 'categoria_id']);
    }

    public function getFotos()
    {
        return $this->hasMany(ReclamacaoFoto::class, ['reclamacao_id' => 'id']);
    }

    public function getHistorico()
    {
        return $this->hasMany(ReclamacaoHistorico::class, ['reclamacao_id' => 'id'])
                    ->orderBy(['created_at' => SORT_DESC]);
    }

    public function getEvidencias()
    {
        return $this->hasMany(ReclamacaoEvidencia::class, ['reclamacao_id' => 'id'])
                    ->orderBy(['created_at' => SORT_ASC]);
    }

    // -- Geração de protocolo (substituiu trigger Oracle) -----------

    public function beforeSave($insert): bool
    {
        if ($insert && empty($this->protocolo)) {
            // Valor temporário único — será substituído em afterSave com o ID real
            $this->protocolo = 'TMP-' . uniqid('', true);
        }
        return parent::beforeSave($insert);
    }

    public function afterSave($insert, $changedAttributes): void
    {
        parent::afterSave($insert, $changedAttributes);
        if ($insert && str_starts_with((string) $this->protocolo, 'TMP-')) {
            $protocolo = date('Y') . str_pad((string) $this->id, 6, '0', STR_PAD_LEFT);
            static::updateAll(['protocolo' => $protocolo], ['id' => $this->id]);
            $this->protocolo = $protocolo;
        }
    }
}
