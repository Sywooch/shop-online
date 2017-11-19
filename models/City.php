<?php

namespace app\models;

use yii\db\ActiveRecord;

/**
 * Class City
 * @package app\models
 */
class City extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'city';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'v', 'po', 'url'], 'filter', 'filter' => 'strip_tags'],
            [['name', 'v', 'po', 'url'], 'string', 'max' => 32],
            [['url'], 'unique'],
            [['name', 'v', 'po', 'url'], 'required'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'name' => 'Регион/Город',
        ];
    }
}