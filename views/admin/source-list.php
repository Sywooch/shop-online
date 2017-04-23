<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\widgets\ActiveForm;

/** @var $this \yii\web\View */
/** @var $source \app\models\Source */
/** @var $filter \app\models\SourceFilter */
/** @var $dataProvider \yii\data\ActiveDataProvider */


$this->params['breadcrumbs'] = [
    ['label' => 'Панель управления', 'url' => ['admin/index']],
    'Источники',
];

//echo Html::a("Добавить", ['source-add'], ['class' => 'btn btn-primary pull-right']);

$form = ActiveForm::begin([
    'action' => Url::current(),
    'method' => 'post',
    'options' => ['class' => 'jumbotron' /*'style' => 'margin: 20px 0'*/],
]);

if ($source->hasErrors()) {
    echo Html::tag('div', print_r($source->getErrors(), true), ['class' => 'alert alert-danger']);
}

echo Html::beginTag('div', ['class' => 'row']);
echo
//    Html::tag(
//        'div',
//        Html::label("&nbsp;") . "<br>" .
//        Html::a('Стереть', ['source-list'], ['class' => 'btn btn-default']),
//        ['class' => 'col-sm-1']
//    ) .
    Html::tag(
        'div',
        Html::activeLabel($source, 'url') .
        Html::activeTextInput($source, 'url', ['class' => 'form-control']),
        ['class' => 'col-sm-4']
    ) .
    Html::tag(
        'div',
        Html::activeLabel($source, 'pattern') .
        Html::activeTextInput($source, 'pattern', ['class' => 'form-control']),
        ['class' => 'col-sm-4']
    ) .
    Html::tag(
        'div',
        Html::activeLabel($source, 'blocked') .
        Html::activeDropDownList($source, 'blocked', [0 => 'Нет', 1 => 'Блок.'], ['class' => 'form-control']),
        ['class' => 'col-sm-2']
    ) .
    Html::tag(
        'div',
        Html::label("&nbsp;") . "<br>" .
        Html::submitButton('', ['class' => 'btn btn-primary glyphicon glyphicon-ok'])
        . Html::a('', ['source-list'], ['class' => 'btn btn-default glyphicon glyphicon-remove']),
        ['class' => 'col-sm-2']
    );
echo Html::endTag('div');

$form->end();

//if (Yii::$app->session->hasFlash('error')) {
//    echo Html::tag('div', join(Yii::$app->session->getFlash('error')), ['class' => 'alert alert-danger']);
//}
//if (Yii::$app->session->hasFlash('success')) {
//    echo Html::tag('div', join(Yii::$app->session->getFlash('success')), ['class' => 'alert alert-success']);
//}

echo GridView::widget([
    'filterModel' => $filter,
    'dataProvider' => $dataProvider,
    'columns' => [
        'id',
        'used',
        'url',
        'pattern',
        [
//            'contentOptions' => ['style' => 'width: 300px;'],
            'attribute' => 'blocked',
            'filter' => [0 => 'Нет', 1 => 'Блок.'],
            'format' => 'raw',
            'value' => function ($model) {
                return $model->blocked ? "Блок." : "";
            }
        ],
        [
            'class' => ActionColumn::className(),
            'contentOptions' => ['style' => 'width: 90px;'],
            'template' => '{parse} {source-edit} {source-delete}',
            'buttons' => [
                'parse' => function ($url, $model, $key) {
                    return Html::a('', $url, ['class' => 'glyphicon glyphicon-play', 'title' => 'Запуск парсера']);
                },
                'source-edit' => function ($url, $model, $key) {
                    return Html::a('', ['', 'id' => $key],
                        ['class' => 'glyphicon glyphicon-pencil', 'title' => 'Переименовать']);
                },
                'source-delete' => function ($url, $model, $key) {
                    return Html::a('', $url, ['class' => 'glyphicon glyphicon-trash', 'title' => 'Удалить']);
                },
            ]
        ],
    ]
]);


//$imagesPath = Yii::$app->params['imagesPath'];
//$this->registerJs(
//<<<js
//$('img').error(
//    function() {
//        $(this).attr('src', '{$imagesPath}/nophoto.jpg');
//    }
//);
//js
//    , $this::POS_END);