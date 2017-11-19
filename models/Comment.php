<?php

namespace app\models;

use yii\db\ActiveRecord;

/**
 * Class Comment
 * @package app\models
 *
 * @property int $product_id
 * @property string $date
 * @property string $buyer
 * @property string $text
 * @property string $photos
 */
class Comment extends ActiveRecord
{

    /**
     * @inheritdoc
     */
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

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'product_id' => 'Товар',
            'buyer' => 'Покупатель',
            'text' => 'Комментарий',
            'date' => 'Дата',
        ];
    }

    /**
     * @param Product $product
     * @param array $attributes
     * @return bool
     */
    public static function add(Product $product, $attributes = [])
    {
        $model = new self();
        $model->attributes = $attributes;
        $model->product_id = $product->id;
        return $model->save();
    }

}