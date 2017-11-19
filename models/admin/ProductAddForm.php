<?php

namespace app\models\admin;

/**
 * Class ProductAddForm
 * @package app\models\admin
 *
 * @property string $url
 */
class ProductAddForm extends \yii\base\Model
{
    /**
     * @var string
     */
    public $url;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['url'], 'url'],
            [['url'], 'required'],
        ];
    }
}
