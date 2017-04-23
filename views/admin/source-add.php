<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/* @var $category \app\models\Category*/


$this->params['breadcrumbs'] = [
    ['label' => 'Панель управления', 'url' => ['admin/index']],
    ['label' => 'Товары', 'url' => ['admin/product-list']],
    'Создание товара',
];

$form = ActiveForm::begin([
    'action' => Url::current(),
    'method' => 'post',
]);

if ($category->hasErrors()) {
    echo Html::tag('div', join("<br>", $category->getErrors("name")), ['class' => 'alert alert-danger']);
}

echo Html::beginTag('div', ['class' => 'row']);
echo Html::activeHiddenInput($category, 'parent_id') .
    Html::tag(
        'div',
        Html::activeLabel($category, 'name') .
        Html::activeTextInput($category, 'name', ['class' => 'form-control']),
        ['class' => 'col-sm-10']
    ) .
    Html::tag(
        'div',
        Html::label(".") . "<br>" .
        Html::submitButton('Сохранить', ['class' => 'btn btn-primary']),
        ['class' => 'col-sm-2']
    );
echo Html::endTag('div');

$form->end();
