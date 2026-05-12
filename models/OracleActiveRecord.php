<?php

declare(strict_types=1);

namespace app\models;

use yii\db\ActiveRecord;

/**
 * Base ActiveRecord mantida para compatibilidade de herança.
 * Migrado de Oracle para MySQL — os workarounds de casing Oracle foram removidos.
 */
abstract class OracleActiveRecord extends ActiveRecord
{
}
