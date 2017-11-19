<?php

namespace app\models\admin;

use yii\db\ActiveRecord;
use yii\db\Expression;

/**
 * Class Source
 * @package app\models\admin
 *
 * @property string $url
 * @property string $pattern
 * @property bool $blocked
 * @property string $used
 */
class Source extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'source';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['url', 'pattern'], 'string', 'max' => 255],
            [['url'], 'unique'],
            [['blocked'], 'in', 'range' => [0, 1]],
            [['used'], 'date', 'format' => 'php:Y-m-d H:i:s'],
            [['url', 'pattern'], 'required'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'url' => 'URL для парсинга',
            'pattern' => 'Шаблон ссылок',
            'blocked' => 'Блокировка',
            'used' => 'Время последнего обращения',
        ];
    }

    /**
     * @param bool $touch
     * @return array|null|ActiveRecord
     */
    public static function getNextSource($touch = false)
    {
        $model = self::find()->where(['blocked' => '0'])->orderBy(['used' => SORT_ASC])->one();

        if ($touch && $model) {
            $model->updateAttributes(['used' => new Expression('now()')]);
        }

        return $model;
    }
}