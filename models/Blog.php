<?php

declare(strict_types=1);

namespace app\models;

use Yii;
use app\models\OracleActiveRecord;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;
use yii\web\UploadedFile;

/**
 * Model para a tabela BLOG.
 *
 * @property int    $id
 * @property string $titulo
 * @property string $resumo
 * @property string $conteudo
 * @property string $foto_capa
 * @property string $foto_banner
 * @property int    $cidadao_id
 * @property int    $ativo
 * @property string $created_at
 * @property string $updated_at
 *
 * @property Cidadao $cidadao
 */
class Blog extends OracleActiveRecord
{
    /** Arquivo temporário — foto de capa (thumbnail do carrossel) */
    public $foto_capa_file   = null;

    /** Arquivo temporário — foto de banner (hero da página do post) */
    public $foto_banner_file = null;

    public static function tableName(): string
    {
        return 'blog';
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
            [['titulo', 'resumo', 'conteudo'], 'required'],
            ['titulo',  'string', 'max' => 200],
            ['resumo',  'string', 'max' => 500],
            ['conteudo','string'],
            ['ativo',   'integer'],
            ['ativo',   'default', 'value' => 1],
            ['cidadao_id', 'integer'],
            [['foto_capa', 'foto_banner'], 'string', 'max' => 500],
            [
                'foto_capa_file', 'file',
                'extensions'  => ['jpg', 'jpeg', 'png', 'webp'],
                'mimeTypes'   => ['image/jpeg', 'image/png', 'image/webp'],
                'maxSize'     => 5 * 1024 * 1024,
            ],
            [
                'foto_banner_file', 'file',
                'extensions'  => ['jpg', 'jpeg', 'png', 'webp'],
                'mimeTypes'   => ['image/jpeg', 'image/png', 'image/webp'],
                'maxSize'     => 5 * 1024 * 1024,
            ],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'id'               => 'ID',
            'titulo'           => 'Título',
            'resumo'           => 'Resumo',
            'conteudo'         => 'Conteúdo',
            'foto_capa'        => 'Foto de Capa',
            'foto_capa_file'   => 'Foto de Capa',
            'foto_banner'      => 'Foto de Apresentação',
            'foto_banner_file' => 'Foto de Apresentação',
            'cidadao_id'       => 'Autor',
            'ativo'            => 'Publicado',
            'created_at'       => 'Criado em',
            'updated_at'       => 'Atualizado em',
        ];
    }

    /**
     * Converte campos CLOB (Oracle retorna resource/stream) para string.
     */
    public function afterFind(): void
    {
        parent::afterFind();
        if (is_resource($this->conteudo)) {
            $this->conteudo = stream_get_contents($this->conteudo);
        }
    }

    public function getCidadao(): \yii\db\ActiveQuery
    {
        return $this->hasOne(Cidadao::class, ['id' => 'cidadao_id']);
    }

    /**
     * Retorna a URL pública da foto de capa.
     */
    public function urlCapa(): string
    {
        return Yii::getAlias('@web') . '/uploads/blog/' . $this->foto_capa;
    }

    /**
     * Retorna a URL pública da foto de banner.
     */
    public function urlBanner(): string
    {
        return Yii::getAlias('@web') . '/uploads/blog/' . $this->foto_banner;
    }
}
