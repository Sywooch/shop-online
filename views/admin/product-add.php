<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/** @var $productAddForm \app\models\admin\ProductAddForm */
/** @var $countLink integer */
/** @var $countModerated integer */
/** @var $countTotal integer */
/** @var $countModeratedToday integer */
/** @var $countTotalToday integer */


$this->params['breadcrumbs'] = [
    ['label' => 'Панель управления', 'url' => ['admin/index']],
    ['label' => 'Список товаров', 'url' => ['admin/product-list']],
    'Добавление товара',
];
?>

<div class="jumbotron">
    <div class="row">
        <?php $form = ActiveForm::begin(['action' => Url::current(), 'method' => 'post',]); ?>
        <div class="col-xs-8 col-sm-9 col-md-10">
            <?= Html::activeTextInput($productAddForm, 'url', ['class' => 'form-control']) ?>
            <?= Html::error($productAddForm, 'url', ['class' => 'text-danger']) ?>
        </div>
        <div class="col-xs-4 col-sm-3 col-md-2">
            <?= Html::submitButton('', ['class' => 'btn btn-primary glyphicon glyphicon-search', 'title' => 'Найти']) ?>
            <?= Html::a('', ['product-add', 'rand' => 1],
                ['class' => 'btn btn-default glyphicon glyphicon-random', 'title' => 'Случайный']) ?>
        </div>
        <?php ?>
        <?php $form->end(); ?>
    </div>
</div>

<div class="row">
    <div class="col-sm-6 col-md-4">
        <h4>Статистика</h4>
        <p class="bg-danger text-danger">Ссылок на товары: <span class="pull-right"><?= $countLink ?></span></p>
        <p class="bg-info text-info">Всего товаров в базе: <span class="pull-right"><?= $countTotal ?></span></p>
        <p class="bg-success text-success">Доступно покупателям:
            <span class="pull-right"><?= $countModerated ?></span></p>
    </div>
    <div class="col-sm-6 col-md-4">
        <h4>Динамика наполнения</h4>
        <p class="bg-warning text-warning">Товаров на редактировании:
            <span class="pull-right"><?= $countTotal - $countModerated ?></span></p>
        <p class="bg-info text-info">Добавлено сегодня:
            <span class="pull-right"><?= $countTotalToday ?></span></p>
        <p class="bg-success text-success">Отредактировано сегодня:
            <span class="pull-right"><?= $countModeratedToday ?></span></p>
    </div>
    <div class="col-sm-12 col-md-4">
        <h4>М - мотивация ;-)</h4>
        <blockquote>
            Единственный способ сделать выдающуюся работу — искренне любить то, что делаешь.
            <footer>Стив Джобс (Steve Jobs)</footer>
        </blockquote>
    </div>
</div>

<hr>