<?php

namespace app\widgets\RandomOffer;

use app\models\Offer;
use app\models\Product;
use yii\base\Widget;

class RandomOffer extends Widget
{
    /*
     * URL города для ссылок
     */
    public $city;

    /*
     * Количество товаров
     */
    public $count = 4;

    /*
     * Теги для определения похожих товаров
     */
    public $tags;

    /*
     * Исключение
     */
    public $productId;


    public function run()
    {
        AssetBundle::register($this->view);

        $offers = [];

        $queryProducts = Product::find()
            ->where(['moderated' => true])
            ->joinWith(['tags'])
            ->andFilterWhere(['and', ['not', ['product.id' => $this->productId]], ['in', 'tag.name', $this->tags]]);
        $productCount = $queryProducts->count('product.id');

        $iterate = 2 * $this->count;
        while (count($offers) < $this->count && $iterate > 0) {
            $offer = $queryProducts
                ->orderBy('product.id')
                ->offset(rand(0, $productCount - 1))
                ->limit(1)
                ->one();
            if (!$offer) {
                break;
            }
            if (!in_array($offer, $offers)) {
                $offers[] = $offer;
            }
            $iterate--;
        }

        return $this->render('default', ['offers' => $offers]);
    }
}
