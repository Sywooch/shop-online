<?php
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ListView;
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
$this->registerMetaTag(['name' => 'og:image', 'content' => Yii::$app->params['siteUrl'] . 'fb.jpg']);
$this->title = $title;


?>
<div class="row">
    <div class="col-xs-12 col-sm-12 col-md-12">
        <?= \app\widgets\ProductSearch\ProductSearch::widget(['model' => $filter]) ?>

        <script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
        <!-- адаптивный -->
        <ins class="adsbygoogle"
             style="display:block"
             data-ad-client="ca-pub-3550073859494126"
             data-ad-slot="1629332681"
             data-ad-format="auto"></ins>
        <script>
            (adsbygoogle = window.adsbygoogle || []).push({});
        </script>

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
                'negativeMargin' => 1000,
                'triggerOffset' => 1000,
                'delay' => 100,
                'triggerText' => Html::button("Ещё больше товаров", ['class' => 'btn btn-lg btn-primary']),
                'noneLeftText' => '',
            ],
        ]);
        \yii\widgets\Pjax::end();
        ?>
    </div>
<!--    <div class="hidden-xs col-sm-3 col-md-3"></div>-->
</div>
<?php
$js = <<<js
$(".product_item__info__link").click(function() {
    var search = $("input[name=CatalogueFilter\\\[query\\\]]");
    if (typeof search != 'undefined') {
        search.val(this.innerText);
        search.closest('form').submit();
    }
    return false;
});

js;
$this->registerJs($js, $this::POS_END);
?>
