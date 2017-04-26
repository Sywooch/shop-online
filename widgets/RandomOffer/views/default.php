<?php
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use app\components\Currency;

/* @var $offers \app\models\Product[] */


echo Html::beginTag('div', ['style' => 'text-align: center']);

foreach ($offers as $offer) {
    echo Html::beginTag('div', [
            'class' => 'random-offer-item',
        ]) .
        Html::beginTag('a', [
            'href' => Url::toRoute([
                'site/product',
                'city' => ArrayHelper::getValue($this->context, 'city.url', 'kazakhstan'),
                'seoUrl' => $offer->seo_url,
                'id' => $offer->id,
            ]),
            'alt' => Html::encode($offer->name),
            'title' => 'Посмотреть ' . Html::encode($offer->name),
        ]) .

        Html::beginTag('div', [
            'class' => 'random-offer-item-image',
        ]) .
        Html::img($offer->image, ['alt' => 'Фотография ' . Html::encode($offer->name)]) .
        Html::tag('div', Currency::kzt($offer->price, $offer->currency) . " <small>₸</small>", ['class' => 'random-offer-item-price']) .
//        Html::tag('div', Html::encode($offer->category->name), ['class' => 'random-offer-item-category']) .
        Html::endTag('div') .

        Html::tag('div', Html::encode($offer->name), ['class' => 'random-offer-item-title']) .

        Html::endTag('a') .
        Html::endTag('div');
}

echo Html::endTag('div');