<?php

namespace app\models\admin;

class ProductAddForm extends \yii\base\Model
{
    public $url;

    public function rules()
    {
        return [
            [['url'], 'url'],
            [['url'], 'required'],
        ];
    }
}
