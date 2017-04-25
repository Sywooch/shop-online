<?php

namespace app\models;

use yii\db\ActiveRecord;

class Property extends ActiveRecord
{
//    public static function tableName()
//    {
//        return 'property';
//    }

    public function rules()
    {
        return [
            [['name', 'value'], 'string', 'max' => 64],
            [
                'product_id',
                'exist',
                'targetAttribute' => 'id',
                'targetClass' => Product::className(),
                'skipOnError' => true,
            ],
            [['product_id', 'name', /*'value'*/], 'required'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'product_id' => 'Товар',
            'name' => 'Характеристика',
            'value' => 'Значение',
        ];
    }

    public static function add(Product $product, $name, $value)
    {
        $model = new self();
        $model->product_id = $product->id;
        $model->name = $name;
        $model->value = $value;
        return $model->save();
    }

}