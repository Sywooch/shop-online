<?php

namespace app\models;

use yii\db\ActiveRecord;

/**
 * Class Picture
 * @package app\models
 */
class Picture extends ActiveRecord
{
//    public static function tableName()
//    {
//        return 'picture';
//    }

    /**
     * @inheritdoc
     */
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

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'src' => 'URL фото',
            'product_id' => 'Товар',
        ];
    }

    /**
     * @param Product $product
     * @param $src
     * @return bool
     */
    public static function add(Product $product, $src)
    {
        $model = new self();
        $model->product_id = $product->id;
        $model->src = $src;
        return $model->save();
    }

}