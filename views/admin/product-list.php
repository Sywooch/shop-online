<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\widgets\ActiveForm;

use \app\controllers\SiteController;

/** @var $this \yii\web\View */
/** @var $product \app\models\Product */
/** @var $filter \app\models\ProductFilter */
/** @var $dataProvider \yii\data\ActiveDataProvider */


$this->params['breadcrumbs'] = [
    ['label' => 'Панель управления', 'url' => ['admin/index']],
    'Список товаров',
];

echo Html::a("Добавить", ['product-add'], ['class' => 'btn btn-primary pull-right']);

echo GridView::widget([
    'filterModel' => $filter,
    'dataProvider' => $dataProvider,
    'columns' => [
        [
            'contentOptions' => ['style' => 'width: 100px;'],
            'label' => 'Фото',
            'format' => 'raw',
            'value' => function ($model) {
                return Html::img(
                    $model->image,
                    [/*'max-width' => 100, 'max-height' => 100, 'height' => 100*/ 'width' => 100,]
                );
            }
        ],
        [
            'contentOptions' => ['style' => 'width: 60px;'],
            'attribute' => 'id',
        ],
        [
            'attribute' => 'name',
            'format' => 'raw',
            'value' => function ($model) {
                return Html::a($model->name, ['site/product', 'city' => SiteController::CITY_DEFAULT,
                    'seoUrl' => $model->seo_url, 'id' => $model->id]);
            }
        ],
        [
            'attribute' => 'tag',
            'filter' => Html::activeTextInput($filter, 'tag', ['class' => 'form-control']),
            'format' => 'raw',
            'value' => function ($model) {
                $links = [];
                foreach ($model->tags as $tag) {
                    $links[] = Html::a($tag->name, ['', 'ProductFilter[tag]' => $tag->name]);
                }
                return join(', ', $links);
            }
        ],
        'created',
        [
            'contentOptions' => ['style' => 'width: 60px;'],
            'attribute' => 'moderated',
            'filter' => [0 => 'Нет', 1 => 'Да'],
            'format' => 'raw',
            'value' => function ($model) {
                return $model->moderated ? "Да" : "Нет";
            }
        ],
        [
            'class' => ActionColumn::className(),
            'contentOptions' => ['style' => 'width: 90px;'],
            'template' => '{product-edit} {product-delete}',
            'buttons' => [
//                'parse' => function ($url, $model, $key) {
//                    return Html::a('', $url, ['class' => 'glyphicon glyphicon-play', 'title' => 'Запуск парсера']);
//                },
                'product-edit' => function ($url, $model, $key) {
                    return Html::a('', $url,
                        ['class' => 'glyphicon glyphicon-pencil', 'title' => 'Переименовать']);
                },
                'product-delete' => function ($url, $model, $key) {
                    return Html::a('', $url, ['class' => 'glyphicon glyphicon-trash', 'title' => 'Удалить',
                        'data-confirm' => 'Товар будет безвозвратно удален. Продолжить?']);
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