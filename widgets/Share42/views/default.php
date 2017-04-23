<?php
use yii\helpers\ArrayHelper;
use yii\helpers\Html;


$htmlOptions = [
    'class' => 'share42init ' . $this->context->cssClass,
    'data-path' => $this->assetManager->getBundle(\app\widgets\Share42\AssetBundle::className())->baseUrl . "/",
];

if ($this->context->url) {
    $htmlOptions['data-url'] = $this->context->url;
}
if ($this->context->title) {
    $htmlOptions['data-title'] = $this->context->title;
}
if ($this->context->description) {
    $htmlOptions['data-description'] = $this->context->description;
}
//if ($this->context->image) {
//    $htmlOptions['data-image'] = $this->context->image;
//}

echo Html::tag('div', '', $htmlOptions);