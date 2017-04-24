<?php
namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\validators\FilterValidator;

class Tag extends ActiveRecord
{
    public static function tableName()
    {
        return 'tag';
    }

    public function rules()
    {
        return [
            ['name', FilterValidator::className(), 'filter' => 'strip_tags'],
            ['name', 'string', 'max' => 255 ],
            ['name', 'required'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'name' => 'Тег',
        ];
    }

    /**
     * Товары по тегу
     * @return $this
     */
    public function getProducts()
    {
        return $this->hasMany(Product::className(), ['id' => 'product_id'])
            ->viaTable('tag_for_product', ['tag_id' => 'id']);
    }

    /**
     * Cвязи с товарами для подсчета популярности
     * - нужна для ускорения, что бы не подгружать таблицу с товарами
     * @return \yii\db\ActiveQuery
     */
    public function getProductLinks()
    {
        return $this->hasMany(TagForProduct::className(), ['tag_id' => 'id']);
    }

    /**
     * Добавление тега
     *
     * @param $tag
     * @return Tag|array|null|ActiveRecord
     */
    public static function add($tag)
    {
        $field = is_numeric($tag) ? 'id' : 'name';
        $model = self::find()->where("{$field} = :tag", [':tag' => $tag])->one();
        if (!$model) {
            $model = new self();
            $model->name = $tag;
            $model->save();
        }
        return $model;
    }

//      Облако тегов не будет использоваться из-за лишней нагрузки на сервер
//    /**
//     * Минимальный размер шрифта
//     */
//    const MIN_FONT_SIZE = 1;
//
//    /**
//     * Максимальный размер шрифта
//     */
//    const MAX_FONT_SIZE = 10;
//
//    public static function getTagWeights($limit = 20)
//    {
//        $models = Tag::find()->with('posts')->orderBy('name')->all();
//
//        $minFrequency = 0;
//        $maxFrequency = 0;
//        foreach ($models as $model) {
//            $weight = count($model->posts);
//            $minFrequency = $minFrequency > $weight ? $weight : $minFrequency;
//            $maxFrequency = $maxFrequency < $weight ? $weight : $maxFrequency;
//        }
//
//
//        $sizeRange = self::MAX_FONT_SIZE - self::MIN_FONT_SIZE;
//
//        $minCount = log($minFrequency + 1);
//        $maxCount = log($maxFrequency + 1);
//
//        if ($maxCount != $minCount){
//            $countRange = $maxCount - $minCount;
//        } else {
//            $countRange = 1;
//        }
//
//        $tags = [];
//        foreach ($models as $model) {
//            $tags[$model->name] = round(
//                self::MIN_FONT_SIZE + (log(count($model->posts) + 1) - $minCount) * ($sizeRange / $countRange)
//            );
//        }
//
//        arsort($tags);
//
//        return $tags;
//    }

}