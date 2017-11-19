<?php

namespace app\models;

use yii\db\ActiveRecord;

/**
 * Class Property
 * @package app\models
 *
 * @property int $product_id
 * @property string $name
 * @property string $value
 *
 */
class Property extends ActiveRecord
{
//    public static function tableName()
//    {
//        return 'property';
//    }

    /**
     * @inheritdoc
     */
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

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'product_id' => 'Товар',
            'name' => 'Характеристика',
            'value' => 'Значение',
        ];
    }

    /**
     * @param Product $product
     * @param $name
     * @param $value
     * @return bool
     */
    public static function add(Product $product, $name, $value)
    {
        $model = new self();
        $model->product_id = $product->id;
        $model->name = $name;
        $model->value = $value;
        return $model->save();
    }

}