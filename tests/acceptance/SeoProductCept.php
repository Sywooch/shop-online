<?php
use yii\console\Exception;
use yii\helpers\Url;
use app\components\Currency;
use app\models\City;
use app\models\Product;
use app\models\Property;
use app\models\Picture;
use app\models\Tag;


$I = new AcceptanceTester($scenario);
$I->wantTo('проверить СЕО страницы товара');


$dataProduct = [
    'name' => 'Название Тестового товара',
    'price' => '1234.56',
    'currency' => 'RUB',
    'description' => 'Описание Тестового товара',
    'seo_url' => 'nazvanie-testovogo-tovara',
    'url' => 'http://shop.local/store/nazvanie-testovogo-tovara/qwer.html',
    'image' => 'https://www.google.com/images/branding/googlelogo/1x/googlelogo_color_150x54dp.png',
    'moderated' => 1,
    'created' => date("Y-m-d H:i:s"),
];
$dataProductPictures = [
    ['src' => 'https://www.google.com/images/branding/googlelogo/1x/googlelogo_color_150x54dp.png'],
    ['src' => 'https://www.google.com/images/branding/googlelogo/2x/googlelogo_color_150x54dp.png'],
];
$dataProductProperties = [
    ['name' => 'Характеристика 1', 'value' => 'Значение 1'],
    ['name' => 'Характеристика 2', 'value' => 'Значение 2'],
    ['name' => 'Характеристика 3', 'value' => 'Значение 3'],
];
$dataProductTags = [
    ['name' => 'Тестовый тег 1'],
    ['name' => 'Тестовый тег 2'],
];

$product = new Product();
$product->attributes = $dataProduct;
if (!$product->save()) {
    throw new Exception(print_r($product->errors, true));
}

foreach ($dataProductPictures as $dataProductPicture) {
    $productPicture = new Picture();
    $productPicture->attributes = $dataProductPicture;
    $productPicture->product_id = $product->id;
    if (!$productPicture->save()) {
        throw new Exception();
    }
}

foreach ($dataProductProperties as $dataProductProperty) {
    $productProperty = new Property();
    $productProperty->attributes = $dataProductProperty;
    $productProperty->product_id = $product->id;
    if (!$productProperty->save()) {
        throw new Exception();
    }
}

foreach ($dataProductTags as $dataProductTag) {
    $productTag = new Tag();
    $productTag->attributes = $dataProductTag;
    if (!$productTag->save()) {
        throw new Exception();
    }
    $productTag->link("products", $product);
}


/** @var $city City */
$city = City::findOne(['url' => 'kazakhstan']);
if (!$city) {
    throw new Exception();
}


$I->amOnPage("/index-test.php?r=site%2Fproduct&city={$city->url}&seo_url={$product->seo_url}&id={$product->id}");


$price = Currency::kzt($product->price, $product->currency);
$title = "{$product->name} по цене {$price} тенге продаётся на " .
    Yii::$app->params['name'] . " с бесплатной доставкой по {$city->po}";
$I->seeInTitle($title);

$description = "Купить {$product->name} за {$price} тенге с доставкой по {$city->po}";
$I->seeInSource('<meta name="og:description" content="' . $description . '">');


$I->see($product->name, 'h1[itemprop="name"]');


// микроразметка
$I->seeInSource('itemscope itemtype="http://schema.org/Product"');



$product->delete();
foreach ($dataProductTags as $dataProductTag) {
    Tag::deleteAll($dataProductTag);
}
