<?php

declare(strict_types=1);

namespace app\models;

use app\models\OracleActiveRecord;

/**
 * Model para a tabela CATEGORIA.
 *
 * @property int    $id
 * @property string $slug
 * @property string $nome
 * @property string $icone_bi
 * @property int    $ativo
 */
class Categoria extends OracleActiveRecord
{
    public static function tableName(): string
    {
        return 'categoria';
    }

    public function rules(): array
    {
        return [
            [['slug', 'nome', 'icone_bi'], 'required'],
            ['slug',     'string', 'max' => 50],
            ['nome',     'string', 'max' => 100],
            ['icone_bi', 'string', 'max' => 80],
            ['ativo',    'integer'],
            ['ativo',    'default', 'value' => 1],
            ['slug',     'unique'],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'id'       => 'ID',
            'slug'     => 'Slug',
            'nome'     => 'Nome',
            'icone_bi' => 'Ícone Bootstrap Icons',
            'ativo'    => 'Ativo',
        ];
    }

    // ── Relações ───────────────────────────────────────────────────

    public function getReclamacoes()
    {
        return $this->hasMany(Reclamacao::class, ['categoria_id' => 'id']);
    }

    // ── Auxiliares ─────────────────────────────────────────────────

    /** Retorna todas as categorias ativas para uso em dropdowns / grids */
    public static function listaAtiva(): array
    {
        return static::find()
            ->where(['ativo' => 1])
            ->orderBy(['nome' => SORT_ASC])
            ->all();
    }
}
