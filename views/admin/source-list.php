<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\widgets\ActiveForm;

/** @var $this \yii\web\View */
/** @var $source \app\models\admin\Source */
/** @var $filter \app\models\admin\SourceFilter */
/** @var $dataProvider \yii\data\ActiveDataProvider */


$this->params['breadcrumbs'] = [
    ['label' => 'Панель управления', 'url' => ['admin/index']],
    'Источники',
];

$form = ActiveForm::begin([
    'action' => Url::current(),
    'method' => 'post',
    'options' => ['class' => 'jumbotron' /*'style' => 'margin: 20px 0'*/],
]);

if ($source->hasErrors()) {
    echo Html::tag('div', print_r($source->getErrors(), true), ['class' => 'alert alert-danger']);
}
?>
    <div class="row">
        <div class="col-sm-4">
            <?= Html::activeLabel($source, 'url') ?>
            <?= Html::activeTextInput($source, 'url', ['class' => 'form-control']) ?>
        </div>
        <div class="col-sm-4">
            <?= Html::activeLabel($source, 'pattern') ?>
            <?= Html::activeTextInput($source, 'pattern', ['class' => 'form-control']) ?>
        </div>
        <div class="col-sm-2">
            <?= Html::activeLabel($source, 'blocked') ?>
            <?= Html::activeDropDownList($source, 'blocked', [0 => 'Нет', 1 => 'Блок.'], ['class' => 'form-control']) ?>
        </div>
        <div class="col-sm-2">
            <?= Html::label("&nbsp;") . "<br>" ?>
            <?= Html::submitButton('', ['class' => 'btn btn-primary glyphicon glyphicon-ok']) ?>
            <?= Html::a('', ['source-list'], ['class' => 'btn btn-default glyphicon glyphicon-remove']) ?>
        </div>
    </div>
<?php
$form->end();

// Список источников

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
                    return Html::a('<i class="glyphicon glyphicon-play"></i>', $url,
                        ['class' => 'btn-parse', 'title' => 'Запуск парсера']);
                },
                'source-edit' => function ($url, $model, $key) {
                    return Html::a('<i class="glyphicon glyphicon-pencil"></i>', ['source-edit', 'id' => $key],
                        ['title' => 'Редактировать']);
                },
                'source-delete' => function ($url, $model, $key) {
                    return Html::a('<i class="glyphicon glyphicon-trash"></i>', $url, ['title' => 'Удалить']);
                },
            ]
        ],
    ]
]);

echo $this->render('_dialog_parser');