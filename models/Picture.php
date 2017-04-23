<?php

namespace app\models;

use yii\db\ActiveRecord;

class Picture extends ActiveRecord
{
//    public static function tableName()
//    {
//        return 'picture';
//    }

    public function rules()
    {
        return [
            [['src'], 'string', 'max' => 255],
            [
                'product_id',
                'exist',
                'targetAttribute' => 'id',
                'targetClass' => Product::className(),
                'skipOnError' => true,
            ],
            [['product_id', 'src'], 'required'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'src' => 'URL фото',
            'product_id' => 'Товар',
        ];
    }

    public static function add(Product $product, $src)
    {
        $model = new self();
        $model->product_id = $product->id;
        $model->src = $src;
        return $model->save();
    }

}