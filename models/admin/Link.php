<?php
// todo DEPRECATED!
namespace app\models\admin;

use yii\db\ActiveRecord;
use yii\db\Expression;

class Link extends ActiveRecord
{
//    public static function tableName()
//    {
//        return 'link';
//    }

    public function rules()
    {
        return [
            [['url'], 'string', 'max' => 255],
            [['url'], 'unique'],
            [
                'source_id',
                'exist',
                'targetAttribute' => 'id',
                'targetClass' => Source::className(),
                'skipOnError' => true,
            ],
//            ['created', 'date', 'format' => 'php:Y-m-d H:i:s'],
            ['url', 'required'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'url' => 'URL для парсинга',
            'source_id' => 'Источник',
        ];
    }

    public static function getNextLink($delete = false)
    {
        $model = self::find()->orderBy(new Expression('rand()'))->one();
        if (!$model) {
            return null;
        }

        if ($delete) {
            $model->delete();
        }

        return $model;
    }
}