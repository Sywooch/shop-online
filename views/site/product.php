<?php
use yii\helpers\Html;
use yii\helpers\Url;
use app\widgets\RandomOffer\RandomOffer;
use app\widgets\Share42\Share42;
use app\widgets\Text\Text;
use app\components\Currency;

/* @var $this yii\web\View */
/* @var $product \app\models\Product */
/* @var $title string */
/* @var $description string */

$this->registerMetaTag(['name' => 'og:type', 'content' => 'website']);
$this->registerMetaTag(['name' => 'og:title', 'content' => $title]);
$this->registerMetaTag(['name' => 'og:site_name', 'content' => Yii::$app->params['siteName']]);
$this->registerMetaTag(['name' => 'og:description', 'content' => $description]);
$this->title = $title;

$this->params['breadcrumbs'] = [Html::decode($product->name)];

?>

<div class="offer" itemscope itemtype="http://schema.org/Product">
    <div class="row">
        <div class="col-sm-12">
            <h1 class="product__name" itemprop="name">
                <?= Html::decode($product->name) ?>
            </h1>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-5">
            <img class="product__image img-thumbnail" itemprop="image" src="<?= $product->image ?>"
                 alt="<?= Html::encode($product->name) ?>"/>
            <?php
            $items = [];
            foreach ($product->pictures as $picture) {
                $items[] = [
                    'url' => $picture->src,
                    'src' => $picture->src, // small
                    'alt' => Html::encode($product->name),
                    'options' => ['title' => Html::encode($product->name),],
                    'clientOptions' => ['title' => Html::encode($product->name), 'class' => 'img-thumbnail',],
                ];
            }
            echo \dosamigos\gallery\Gallery::widget(['items' => $items]);
            ?>
        </div>
        <div class="col-sm-7">

            <?= $this->render('_google_adsense') ?>

            <?= $product->description ? "<h2>Краткое описание</h2>" .
                Text::widget(['content' => Html::decode($product->description)]) : "" ?>

            <table class="table table-responsive">
                <tr>
                    <th><i class="glyphicon glyphicon-ok-circle"></i> Наличие товара</th>
                    <td><span class="label label-success">Есть</span></td>
                </tr>
                <tr>
                    <th><i class="glyphicon glyphicon-globe"></i> Доставка
                        в <?= Html::encode($this->context->city->name) ?></th>
                    <td><span class="label label-success">Есть</span></td>
                </tr>
                <tr>
                    <th><i class="glyphicon glyphicon-tags"></i> Теги</th>
                    <td>
                        <?php foreach ($product->tags as $tag): ?>
                            <span class="label label-info"><?= Html::encode($tag['name']) ?></span>
                        <?php endforeach; ?>
                    </td>
                </tr>
                <tr>
                    <th><i class="glyphicon glyphicon-usd"></i> Розничная цена</th>
                    <td>
                        <div itemprop="offers" itemscope itemtype="http://schema.org/Offer">
                            <span itemprop="price" content="<?= Currency::kzt($product->price, $product->currency) ?>">
                                <?= Currency::kzt($product->price, $product->currency) ?>
                            </span>
                            <span itemprop="priceCurrency" content="KZT">тенге</span> (минимум)
                            <link itemprop="availability" href="http://schema.org/InStock"/>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th><!--Лучший поставщик--></th>
                    <td>
                        <a class="btn btn-danger" rel="nofollow" itemprop="url"
                           href="<?= Yii::$app->params['admitad'] . urlencode($product->url) ?>">
                            <span class="glyphicon glyphicon-shopping-cart"></span> Купить
                        </a>
                    </td>
                </tr>
            </table>

            <?= $this->render('_google_adsense') ?>

            <?= Share42::widget([
                'title' => $title,
                'description' => $description,
                'url' => Url::toRoute([
                    'site/product',
                    'city' => $this->context->city->url,
                    'seoUrl' => $product->seo_url,
                    'id' => $product->id,
                ]),
            ]) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12" itemprop="description">
            <h2>Характеристики</h2>
            <table class="table table-striped ">
                <tbody>
                <?php foreach ($product->properties as $property): ?>
                    <tr>
                        <th><?= Html::encode($property->name) ?></th>
                        <td><?= Html::encode($property->value) ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>

            <?= $this->render('_google_adsense') ?>

            <div style="text-align: center;">
                <a class="btn btn-lg btn-danger" rel="nofollow" itemprop="url"
                   href="<?= Yii::$app->params['admitad'] . urlencode($product->url) ?>">
                    <span class="glyphicon glyphicon-shopping-cart"></span> Купить
                </a>
            </div>
        </div>
    </div>

    <?php if (count($product->comments) > 0) : ?>
        <div class="row">
            <div class="col-sm-12">
                <h2>Отзывы покупателей с AliExpress</h2>
                <?php foreach ($product->comments as $comment): ?>
                    <div class="row">
                        <div class="col-xs-8">
                            <blockquote class="product__comment">
                                <?= $comment->text ?>
                                <footer><?= $comment->date . ", " . $comment->buyer ?></footer>
                            </blockquote>
                        </div>
                        <div class="col-xs-4">
                            <?php
                            $items = [];
                            foreach (explode(';', $comment->photos) as $photo) {
                                $items[] = [
                                    'url' => $photo,
                                    'src' => $photo,
                                    'clientOptions' => ['class' => 'product__comment__img img-thumbnail'],
                                ];
                            }
                            echo \dosamigos\gallery\Gallery::widget(['items' => $items,
                                'templateOptions' => ['id' => "comment_" . $comment->id]]);
                            ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>

    <hr>

    <div class="row">
        <div class="col-sm-12">
            <h2>Посмотрите так же похожие модели</h2>

            <?= $this->render('_google_adsense') ?>

            <?= RandomOffer::widget(['productId' => $product->id,
                'tags' => array_map(function ($item) {
                    return $item->name;
                }, $product->tags)]) ?>

            <?= $this->render('_google_adsense') ?>
        </div>
    </div>
</div>
