<?php
namespace app\models;

use yii\db\ActiveRecord;

class TagForProduct extends ActiveRecord
{
    public static function tableName()
    {
        return 'tag_for_product';
    }
}