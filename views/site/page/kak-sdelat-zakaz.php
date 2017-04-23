<?php
use yii\helpers\Html;

/* @var $this yii\web\View */

$title = "";
$description = "";
$keywords = "";
$h1 = "Как купить товар: поиск, выбор, оформление заказа";

$this->registerMetaTag(['name' => 'og:type', 'content' => 'article']);
$this->registerMetaTag(['name' => 'og:title', 'content' => $title]);
$this->registerMetaTag(['name' => 'og:site_name', 'content' => Yii::$app->params['siteName']]);
$this->registerMetaTag(['name' => 'og:description', 'content' => $description]);
$this->registerMetaTag(['name' => 'og:keywords', 'content' => $keywords]);

$this->registerMetaTag(['name' => 'description', 'content' => $description]);
$this->registerMetaTag(['name' => 'keywords', 'content' => $keywords]);

$this->title = $title;

$this->params['breadcrumbs'] = [$h1];


echo Html::tag('h1', $h1);
// <h1>Как купить товар: поиск, выбор, оформление заказа</h1>
?>

<h2>Как найти товар?</h2>

<h2>Как выбрать лучшее?</h2>

<h2>Как сделать заказ?</h2>