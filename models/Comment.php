<?php

namespace app\models;

use yii\db\ActiveRecord;

class Comment extends ActiveRecord
{

    public function rules()
    {
        return [
            [['buyer', 'text', 'photos'], 'filter', 'filter' => 'strip_tags'],
            [['buyer'], 'string', 'max' => 255],
            [['text', 'photos'], 'string', 'max' => 65535],
            [['date'], 'date', 'format' => 'php:Y-m-d H:i:s'],
            [
                'product_id',
                'exist',
                'targetAttribute' => 'id',
                'targetClass' => Product::className(),
                'skipOnError' => true,
            ],
            [['product_id', 'text', 'date'], 'required'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'product_id' => 'Товар',
            'buyer' => 'Покупатель',
            'text' => 'Комментарий',
            'date' => 'Дата',
        ];
    }

    public static function add(Product $product, $attributes = [])
    {
        $model = new self();
        $model->attributes = $attributes;
        $model->product_id = $product->id;
        return $model->save();
    }

}