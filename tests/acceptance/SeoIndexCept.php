<?php
use app\models\City;


$I = new AcceptanceTester($scenario);
$I->wantTo('проверить СЕО главной страницы');

$cities = City::find()->all();
foreach ($cities as $city) {
    $title = Yii::$app->params['siteName'] . " с доставкой по {$city->po} на " . Yii::$app->params['name'];

    $I->amOnPage($city->url);

    $I->seeInTitle($title);

    $I->seeInSource('<meta name="og:description" content="' . $title . '">');

    $I->seeLink($city->name, $city->url);
}
