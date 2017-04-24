<?php
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use app\assets\AppAsset;


/* @var $this \yii\web\View */
/* @var $content string */

AppAsset::register($this);

$this->beginPage();
?><!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <title><?= Html::encode($this->title) ?></title>
    <?= Html::csrfMetaTags() ?>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>
<?php

NavBar::begin([
    'brandLabel' => Html::tag('span', '', ['class' => 'glyphicon glyphicon-home']),
    'brandUrl' => false,
    'options' => [
        'class' => 'navbar-inverse navbar-fixed-top',
    ],
]);
echo Nav::widget([
    'encodeLabels' => false,
    'options' => ['class' => 'navbar-nav'],
    'items' => Yii::$app->user->isGuest ? [] : [
        [
            'label' => Html::tag('span', '', ['class' => 'glyphicon glyphicon-cog']),
            'url' => ['index'],
        ],
        [
            'label' => Html::tag('span', '', ['class' => 'glyphicon glyphicon-list-alt']) . ' Источники',
            'url' => ['source-list']
        ],
//        [
//            'label' => 'Карусель',
//            'url' => ['carousel']
//        ],
//        [
//            'label' => 'Страницы',
//            'url' => ['page-list']
//        ],
//        [
//            'label' => 'Товары',
//            'url' => ['offer-list']
//        ],
        [
            'label' => Html::tag('span', '', ['class' => 'glyphicon glyphicon-list']) . ' Товары',
            'url' => ['product-list']
        ],
        [
            'label' => Html::tag('span', '', ['class' => 'glyphicon glyphicon-plus']) . ' Добавить товар',
            'url' => ['product-add']
        ],
    ],
]);
echo Nav::widget([
    'options' => ['class' => 'navbar-nav navbar-right'],
    'items' => Yii::$app->user->isGuest ? [] : [
        ['label' => 'Выход', 'url' => ['logout']],
    ],
]);
NavBar::end();

?>
<div class="container" style="margin-top: 56px;">
    <?php
    echo Breadcrumbs::widget([
        'homeLink' => [
            'label' => Yii::$app->params['name'],
            'url' => Yii::$app->getHomeUrl(),
            'itemprop' => 'url',
            'rel' => 'v:url',
            'property' => 'v:title',
        ],
        //'encodeLabels' => false,
        'itemTemplate' => '<li typeof="v:Breadcrumb">{link}</li>',
        'activeItemTemplate' =>
            '<li class="active" typeof="v:Breadcrumb"><span rel="v:url" property="v:title">{link}</span></li>',
        'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
        'options' => [
            'class' => 'breadcrumb',
            'itemscope itemtype' => 'http://data-vocabulary.org/Breadcrumb',
            'xmlns:v' => 'http://rdf.data-vocabulary.org/#',
//            'rel' => 'v:url',
//            'property' => 'v:title',
        ],
    ]);
    ?>

    <?php if ($error = Yii::$app->session->getFlash('error', '', true)): ?>
        <div class="alert alert-danger"><?= join("<br>\n", $error) ?></div>
    <?php endif; ?>
    <?php if ($success = Yii::$app->session->getFlash('success', '', true)): ?>
        <div class="alert alert-success"><?= join("<br>\n", $success) ?></div>
    <?php endif; ?>

    <?= $content ?>

</div> <!-- /container -->

<footer class="footer">
    <div class="container">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <p>&copy; <?= Yii::$app->params['name'] ?>, 2011-<?= date('Y') ?>. Все права защищены.</p>
        </div>
    </div>
</footer>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>