<?php
use yii\bootstrap\Carousel;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ListView;
use app\models\Carousel as CarouselModel;
use app\widgets\Text\Text;

/* @var $this yii\web\View */
/* @var $filter \app\models\CatalogueFilter */
/* @var $dataProvider \yii\data\ActiveDataProvider */
/* @var $title string */
/* @var $description string */

$this->registerMetaTag(['name' => 'og:type', 'content' => 'website']);
$this->registerMetaTag(['name' => 'og:title', 'content' => $title]);
$this->registerMetaTag(['name' => 'og:site_name', 'content' => Yii::$app->params['siteName']]);
$this->registerMetaTag(['name' => 'og:description', 'content' => $description]);
$this->registerMetaTag(['name' => 'og:image', 'content' => Yii::$app->params['siteUrl'] . 'fb.jpg' ]);
$this->title = $title;


?>
<div class="row">
    <div class="col-xs-12 col-sm-12 col-md-12">
        <?= \app\widgets\ProductSearch\ProductSearch::widget(['model' => $filter]) ?>

        <?php
        $pjax = \yii\widgets\Pjax::begin();
        echo ListView::widget([
            'dataProvider' => $dataProvider,
            'layout' => $this->render('_product_list_layout', ['count' => $dataProvider->getTotalCount()]),
            'emptyText' => $this->render('_product_list_empty', []),
            'itemView' => '_product_item',
            'itemOptions' => ['class' => 'item'],
            'pager' => [
                'class' => \kop\y2sp\ScrollPager::className(),
                'negativeMargin' => 500,
                'triggerOffset' => 2,
                'delay' => 100,
//                'triggerText' => 'Load More news',
//                'noneLeftText' => '',
            ],
        ]);
        \yii\widgets\Pjax::end();
        ?>
    </div>
<!--    <div class="hidden-xs col-sm-3 col-md-3"></div>-->
</div>

