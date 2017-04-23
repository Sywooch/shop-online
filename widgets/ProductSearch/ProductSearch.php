<?php
/**
 * todo Запоминать в куках состояние отображения фильтров
 */
namespace app\widgets\ProductSearch;

use app\models\CatalogueFilter;
use app\models\Tag;
use yii\base\Widget;

class ProductSearch extends Widget
{
    /** @var CatalogueFilter */
    public $model;

    public function run()
    {
        AssetBundle::register($this->view);

        $tags = Tag::find()
            ->select('id, name, count(product_id) as product_count')
            ->joinWith(['productLinks'])
            ->groupBy(['name'])
            ->orderBy(['product_count' => SORT_DESC])
            ->limit(5)
            ->asArray()
            ->all();

        return $this->render('default', ['tags' => $tags]);
    }
}