<?php

namespace app\models;

use yii\db\ActiveRecord;

class City extends ActiveRecord
{
    public static function tableName()
    {
        return 'city';
    }

    public function rules()
    {
        return [
            [['name', 'v', 'po', 'url'], 'string', 'max' => 32],
            [['url'], 'unique'],
            [['name', 'v', 'po', 'url'], 'required'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'name' => 'Регион/Город',
        ];
    }
}