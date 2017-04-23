<?php
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\web\UploadedFile;

/* @var $this \yii\web\View */
/* @var $category \app\models\Category */
/* @var $image \app\models\UploadImage */


$this->params['breadcrumbs'] = [
    ['label' => 'Панель управления', 'url' => ['admin/index']],
    ['label' => 'Каталог корневых категорий', 'url' => ['admin/catalog']],
    'Загрузка иконки категории',
];

if (Yii::$app->session->hasFlash('error_load')) {
    echo Html::tag('div', 'error', ['class' => 'alert alert-danger']);
}

echo Html::tag('h3', Html::getAttributeValue($category, 'name'));

$form = ActiveForm::begin([
    'action' => Url::current(),
    'method' => 'post',
    'options' => ['enctype' => 'multipart/form-data'],
]);
echo Html::beginTag('div', ['class' => 'row']) .
    Html::tag('div', $form->errorSummary($image), ['class' => 'col-sm-5']) .
    Html::tag('div', $form->field($image, "file")->fileInput()->error(false), ['class' => 'col-sm-5']) .
    Html::tag('div',
        Html::label("ID: " . ArrayHelper::getValue($category, "id")) . "<br>" .
        Html::submitButton("Сохранить", ['class' => 'btn btn-primary']), ['class' => 'col-sm-2']);
echo Html::endTag('div');
$form->end();


//$imagesPath = Yii::$app->params['imagesPath'];
//
//$this->registerJs(
//<<<js
//$('body').find('img').error(
//    function() {
//        $(this).attr('src', '{$imagesPath}/nophoto.jpg');
//    }
//);
//js
//    , $this::POS_END);