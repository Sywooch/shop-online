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
                    return Html::a('', $url,
                        ['class' => 'btn-parse glyphicon glyphicon-play', 'title' => 'Запуск парсера']);
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


$js = <<<js

    $('.btn-parse').click(function() {
    console.log('click');
        // модальный диалог
        var dlgParse = $('#dlgParse');
        dlgParse.find('.loading').show();
        dlgParse.find('pre').text("").hide();
        dlgParse.dialog({title: "Идет парсинг, ждите...", width: 500, modal: true});

        // запуск парсера и ожидание результата
        $.post(this.href, function(response) {
            dlgParse.dialog('option', 'title', 'Парсинг завершён!');
            dlgParse.find('.loading').hide();
            dlgParse.find('pre').text(response).show();
        });

        return false;
    });

js;
$this->registerJs($js, $this::POS_END);
?>
<div id="dlgParse" style="display: none;">
    <div class="loading text-center">
        <img src="/images/loading.gif">
    </div>
    <pre></pre>
</div>