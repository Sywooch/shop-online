<?php
namespace app\models;

use yii\db\ActiveRecord;

/**
 * Class TagForProduct
 * @package app\models
 */
class TagForProduct extends ActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tag_for_product';
    }
}