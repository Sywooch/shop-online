<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/** @var $productAddForm \app\models\admin\ProductAddForm */


$this->params['breadcrumbs'] = [
    ['label' => 'Панель управления', 'url' => ['admin/index']],
    ['label' => 'Список товаров', 'url' => ['admin/product-list']],
    'Добавление товара',
];
?>

<div class="jumbotron">
    <div class="row">
        <?php $form = ActiveForm::begin(['action' => Url::current(), 'method' => 'post',]); ?>
        <div class="col-sm-10">
            <?= Html::activeTextInput($productAddForm, 'url', ['class' => 'form-control']) ?>
            <?= Html::error($productAddForm, 'url', ['class' => 'text-danger']) ?>
        </div>
        <div class="col-sm-2">
            <?= Html::submitButton('', ['class' => 'btn btn-primary glyphicon glyphicon-search', 'title' => 'Найти']) ?>
            <?= Html::a('', ['product-add', 'rand' => 1],
                ['class' => 'btn btn-default glyphicon glyphicon-random', 'title' => 'Случайный']) ?>
        </div>
        <?php ?>
        <?php $form->end(); ?>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        {Статистика по товарам на модерацию}
        {Статистика по товарам на модерацию}
    </div>
    <div class="col-md-6">
        {Статистика по товарам на модерацию}
        {Статистика по товарам на модерацию}
    </div>
</div>