<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\widgets\ActiveForm;

use \app\controllers\SiteController;

/** @var $this \yii\web\View */
/** @var $product \app\models\Product */
/** @var $filter \app\models\admin\ProductFilter */
/** @var $dataProvider \yii\data\ActiveDataProvider */


$this->params['breadcrumbs'] = [
    ['label' => 'Панель управления', 'url' => ['admin/index']],
    'Список товаров',
];

echo Html::a("Добавить", ['product-add'], ['class' => 'btn btn-primary pull-right']);

echo GridView::widget([
    'filterModel' => $filter,
    'dataProvider' => $dataProvider,
//    'rowOptions' => function ($model, $index, $widget, $grid) {
//        return !$model->moderated ? ['class' => 'bg-warning'] : [];
//    },
    'columns' => [
        [
            'contentOptions' => ['style' => 'width: 100px;'],
            'label' => 'Фото',
            'format' => 'raw',
            'value' => function ($model) {
                return Html::img($model->image, ['width' => 100]);
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
                return Html::a(Html::encode($model->name), ['admin/product-edit', 'id' => $model->id]);
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
        [
            'contentOptions' => ['style' => 'width: 100px; text-align: center;'],
            'attribute' => 'created',
        ],
        [
            'contentOptions' => ['style' => 'width: 60px; text-align: center;'],
            'attribute' => 'moderated',
            'filter' => [0 => 'Нет', 1 => 'Да'],
            'format' => 'raw',
            'value' => function ($model) {
                return $model->moderated
                    ? '<i class="label label-success">Да</i>'
                    : '<i class="label label-warning">Нет</i>';
            }
        ],
        [
            'class' => ActionColumn::className(),
            'contentOptions' => ['style' => 'width: 90px;'],
            'template' => '{product-parse} {product-edit} {product-delete} {product-view}',
            'buttons' => [
                'product-parse' => function ($url, $model, $key) {
                    return Html::a('<i class="glyphicon glyphicon-play"></i>', $url,
                        ['class' => 'btn-parse', 'title' => 'Обновить цену']);
                },
                'product-view' => function ($url, $model, $key) {
                    return Html::a('<i class="glyphicon glyphicon-eye-open"></i>',
                        Url::to(['site/product', 'city' => SiteController::CITY_DEFAULT,
                            'seoUrl' => $model->seo_url, 'id' => $model->id]),
                        ['title' => 'Смотреть в магазине', 'target' => '_blank']);
                },
                'product-edit' => function ($url, $model, $key) {
                    return Html::a('<i class="glyphicon glyphicon-pencil"></i>', $url, ['title' => 'Редактировать']);
                },
                'product-delete' => function ($url, $model, $key) {
                    return Html::a('<i class="glyphicon glyphicon-trash"></i>', $url, ['title' => 'Удалить',
                        'data-confirm' => 'Товар будет безвозвратно удален. Продолжить?']);
                },
            ],
            'visibleButtons' => [
                'product-view' => function ($model, $key, $index) {
                    return $model->moderated;
                }
            ]
        ],
    ]
]);

echo $this->render('_dialog_parser');