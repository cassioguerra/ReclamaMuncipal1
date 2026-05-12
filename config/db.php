<?php

/**
 * Configuração do banco de dados — MySQL (XAMPP)
 *
 * Variáveis de ambiente sobrescrevem os valores padrão:
 *   DB_HOST  DB_PORT  DB_NAME  DB_USERNAME  DB_PASSWORD
 */

$host   = getenv('DB_HOST')     ?: 'localhost';
$port   = getenv('DB_PORT')     ?: '3306';
$dbname = getenv('DB_NAME')     ?: 'reclamamunicipal';

return [
    'class'    => 'yii\db\Connection',
    'dsn'      => "mysql:host={$host};port={$port};dbname={$dbname};charset=utf8mb4",
    'username' => getenv('DB_USERNAME') ?: 'root',
    'password' => getenv('DB_PASSWORD') ?: '',
    'charset'  => 'utf8mb4',

    'enableSchemaCache'   => true,
    'schemaCacheDuration' => 3600,
    'schemaCache'         => 'cache',
];
