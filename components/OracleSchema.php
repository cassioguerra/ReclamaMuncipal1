<?php

declare(strict_types=1);

namespace app\components;

/**
 * Schema Oracle sem quoting de identificadores.
 *
 * Em Oracle, identificadores sem aspas são insensíveis a maiúsculas/minúsculas
 * (tratados como UPPERCASE internamente). Isso permite que o Yii2 gere SQL com
 * nomes de coluna em lowercase sem as aspas duplas que causam ORA-00904.
 */
class OracleSchema extends \yii\db\oci\Schema
{
    /** Remove as aspas duplas dos identificadores */
    protected $columnQuoteCharacter = '';
    protected $tableQuoteCharacter  = '';

    /**
     * Sem aspas, nomes simples não precisam de tratamento especial.
     * Devolvemos o nome tal qual para evitar quoting.
     */
    public function quoteSimpleColumnName($name): string
    {
        return $name === '*' ? $name : $name;
    }

    public function quoteSimpleTableName($name): string
    {
        return $name;
    }
}
