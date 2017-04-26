<?php

namespace app\models\admin;

use yii\db\ActiveRecord;
use yii\db\Expression;

class Source extends ActiveRecord
{
    public static function tableName()
    {
        return 'source';
    }

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

    public function attributeLabels()
    {
        return [
            'url' => 'URL для парсинга',
            'pattern' => 'Шаблон ссылок',
            'blocked' => 'Блокировка',
            'used' => 'Время последнего обращения',
        ];
    }

    public static function getNextSource($touch = false)
    {
        $model = self::find()->where(['blocked' => '0'])->orderBy(['used' => SORT_ASC])->one();
        if ($touch && $model) {
            $model->updateAttributes(['used' => new Expression('now()')]);
        }
        return $model;
    }
}