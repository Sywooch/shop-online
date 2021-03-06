<?php
use app\components\Currency;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $model \app\models\Product */

?>

<div class="product_item" itemprop="itemListElement" itemscope itemtype="http://schema.org/Product">
    <div class="row">
        <div class="col-xs-12 col-sm-5 col-md-5">
            <div class="product_item__image">
                <img class=" thumbnail" itemprop="image" src="<?= $model->image ?>"
                     title="<?= Html::encode($model->name) ?>"
                     alt="Фото <?= Html::encode($model->name) ?> в <?= Html::encode($this->context->city->v) ?>"/>
            </div>
        </div>

        <div class="col-xs-12 col-sm-7 col-md-7">
            <a data-pjax="0" class="product_item__url" itemprop="url" title="Нажмите, что бы узнать больше!"
               href="<?= Url::toRoute([
                   'site/product',
                   'city' => $this->context->city->url,
                   'seoUrl' => $model->seo_url,
                   'id' => $model->id,
               ]) ?>">
                <h2 class="product_item__name" itemprop="name"><?= Html::decode($model->name) ?></h2>
            </a>

            <div class="product_item__offer bg-primary" itemprop="offers" itemscope itemtype="http://schema.org/Offer">
                <a data-pjax="0" class="btn btn-lg btn-danger pull-right" rel="nofollow"
                   onclick="ga('send', 'event', 'catalog', 'buy'); return true;"
                   href="<?= Yii::$app->params['admitad'] . urlencode($model->url) ?>"
                >
                    <i class="glyphicon glyphicon-shopping-cart"></i> Купить
                </a>
                <div class="product_item__price_block">
                    <span class="product_item__price" itemprop="price"
                          content="<?= Currency::kzt($model->price, $model->currency) ?>">
                        <?= Currency::kzt($model->price, $model->currency) ?>
                    </span>
                    <span class="product_item__currency" itemprop="priceCurrency" content="KZT">₸</span>
                </div>
                <?php if ($model->rating) : ?>
                    <div class="product_item__rating" itemprop="aggregateRating" itemscope itemtype="http://schema.org/AggregateRating">
                        <i class="glyphicon glyphicon-star-empty"></i>
                        Рейтинг <span itemprop="ratingValue"><?= $model->rating ?></span> / 5
                        <!-- based on <span itemprop="reviewCount">11</span> customer reviews -->
                    </div>
                <?php endif; ?>
                <div class="product_item__available">
                    <link itemprop="availability" href="http://schema.org/InStock"/>
                    <i class="glyphicon glyphicon-ok-circle"></i> Есть в наличии
                </div>
                <div class="product_item__delivery">
                    <i class="glyphicon glyphicon-ok-circle"></i> Бесплатная доставка
                </div>
            </div>

            <table class="product_item__properties">
                <tbody>
                <?php foreach (array_slice($model->properties, 0, 6) as $property): ?>
                    <tr>
                        <th><?= Html::encode($property->name) ?></th>
                        <td><?= Html::encode($property->value) ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>

            <div class="product_item__show_more pull-right">
                <a data-pjax="0" class="product_item__show_more_link" title="Нажмите, что бы узнать больше!"
                   href="<?= Url::toRoute([
                       'site/product',
                       'city' => $this->context->city->url,
                       'seoUrl' => $model->seo_url,
                       'id' => $model->id,
                   ]) ?>">Смотреть все характеристики >></a>
            </div>

        </div>
    </div>
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="product_item__info">
                <div class="pull-right">
                    <i class="glyphicon glyphicon-time"></i> <?= (new DateTime($model->created))->format("H:i d.m.Y") ?>
                    г.
                </div>
                <div>
                    <i class="glyphicon glyphicon-tags"></i>
                    <?php foreach ($model->tags as $tag) {
                        echo Html::a(Html::encode($tag['name']), "#", ['data-pjax' => 0,
                                'class' => 'product_search_filter_tag_link product_item__info__link',]) . PHP_EOL;
                    } ?>
                </div>
            </div>
        </div>
    </div>
</div>