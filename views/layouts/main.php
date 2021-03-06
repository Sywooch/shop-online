<?php
use app\assets\AppAsset;use app\models\City;use app\widgets\ScrollUp\ScrollUp;use yii\helpers\Html;use yii\helpers\Url;use yii\widgets\Breadcrumbs;


/* @var $this \yii\web\View */
/* @var $content string */

/*
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
*/

AppAsset::register($this);

$this->registerLinkTag(['rel' => 'canonical', 'href' => Url::canonical()]);

$this->beginPage();
?><!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>" xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <title><?= Html::encode($this->title) ?></title>
    <?= Html::csrfMetaTags() ?>
    <?php $this->head() ?>

    <!-- Google Analytics -->
    <script>
        (function (i, s, o, g, r, a, m) {
            i['GoogleAnalyticsObject'] = r;
            i[r] = i[r] || function () {
                    (i[r].q = i[r].q || []).push(arguments)
                }, i[r].l = 1 * new Date();
            a = s.createElement(o),
                m = s.getElementsByTagName(o)[0];
            a.async = 1;
            a.src = g;
            m.parentNode.insertBefore(a, m)
        })(window, document, 'script', 'https://www.google-analytics.com/analytics.js', 'ga');

        ga('create', 'UA-30963562-1', 'auto');
        ga('send', 'pageview');
    </script>
    <!-- End Google Analytics -->

    <!-- Global site tag (gtag.js) - Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-30963562-1"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag() {
            dataLayer.push(arguments);
        }
        gtag('js', new Date());

        gtag('config', 'UA-30963562-1');
    </script>

    <!-- Yandex.Metrika counter -->
    <script type="text/javascript" >
        (function (d, w, c) {
            (w[c] = w[c] || []).push(function() {
                try {
                    w.yaCounter46865247 = new Ya.Metrika({
                        id:46865247,
                        clickmap:true,
                        trackLinks:true,
                        accurateTrackBounce:true,
                        webvisor:true
                    });
                } catch(e) { }
            });

            var n = d.getElementsByTagName("script")[0],
                s = d.createElement("script"),
                f = function () { n.parentNode.insertBefore(s, n); };
            s.type = "text/javascript";
            s.async = true;
            s.src = "https://mc.yandex.ru/metrika/watch.js";

            if (w.opera == "[object Opera]") {
                d.addEventListener("DOMContentLoaded", f, false);
            } else { f(); }
        })(document, window, "yandex_metrika_callbacks");
    </script>
    <noscript><div><img src="https://mc.yandex.ru/watch/46865247" style="position:absolute; left:-9999px;" alt="" /></div></noscript>
    <!-- /Yandex.Metrika counter -->
</head>
<body>
<?php $this->beginBody() ?>
<div class="header">
    <div class="container transform">&nbsp;</div>
</div>
<div class="container">
    <div class="clearfix"></div>
    <div>
        <div class="logo"><?= Html::encode(Yii::$app->params['name']) ?></div>
        <p class="text-muted"><?= Html::encode(Yii::$app->params['siteName']) ?></p>
    </div>

    <?= Breadcrumbs::widget([
        'encodeLabels' => false,
        'homeLink' => [
            'label' => Yii::$app->params['name'] . " " . $this->context->city->name,
            'url' => Url::toRoute(['site/index', 'city' => $this->context->city->url]), //Yii::$app->getHomeUrl(),
            'itemprop' => 'url',
            'rel' => 'v:url',
            'property' => 'v:title',
        ],
        'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
        'itemTemplate' => '<li typeof="v:Breadcrumb">{link}</li>',
        'activeItemTemplate' =>
            '<li class="active" typeof="v:Breadcrumb"><span rel="v:url" property="v:title">{link}</span></li>',
        'options' => [
            'class' => 'breadcrumb',
            'itemscope itemtype' => 'http://data-vocabulary.org/Breadcrumb',
            'xmlns:v' => 'http://rdf.data-vocabulary.org/#',
            //            'rel' => 'v:url',
            //            'property' => 'v:title',
        ],
    ]) ?>

    <?= $content ?>

</div> <!-- /container -->

<footer class="footer">
    <div class="container">
        <div class="row">
            <!-- col 1 -->
            <div class="col-sm-4 col-md-4">
                <a class="twitter-timeline" data-height="500" data-theme="light" data-link-color="#018ccd"
                   href="https://twitter.com/shoponline_kz">Tweets by olc_kz</a>
                <script async src="//platform.twitter.com/widgets.js" charset="utf-8"></script>
            </div>

            <!-- col 2 -->
            <div class="col-sm-4 col-md-4">
                <!--
                <h4>Интернет-магазин</h4>
                <ul>
                    <li><a href="/kak-sdelat-zakaz.html"
                           title="Как сделать заказ в интернет-магазине">Как сделать заказ?</a></li>
                    <li><a href="/dostavka-i-oplata.html" title="Доставка и оплата товара">Доставка и оплата</a></li>
                </ul>
                -->

                <h4>Ваш регион доставки</h4>
                <ul>
                    <?php
                    $url = [];
                    if ($this->context->product) {
                        $url['seoUrl'] = $this->context->product->seo_url;
                        $url['id'] = $this->context->product->id;
                    }

                    foreach (City::find()->all() as $city) {
                        $url[0] = trim($this->context->route, '\\\/');
                        if (in_array($url[0], ['site/error', 'site/page',])) {
                            $url[0] = 'site/index';
                        }
                        $url['city'] = $city->url;

                        echo Html::tag('li', Html::a($city->name, $url), []);
                    }
                    ?>
                </ul>

                <p>
                    &copy; <?= Yii::$app->params['name'] ?>, 2011-<?= date('Y') ?>. <br>Все права защищены.
                </p>
            </div>

            <!-- col 3 -->
            <div class="col-sm-4 col-md-4">
                <script type="text/javascript" src="//vk.com/js/api/openapi.js?124"></script>
                <!-- VK Widget -->
                <div id="vk_groups"></div>
                <script type="text/javascript">
                    VK.Widgets.Group(
                        "vk_groups",
                        {
                            redesign: 1,
                            mode: 4,
                            wide: 0,
                            height: "500",
                            width: "auto",
                            color1: 'FFFFFF',
                            color2: '000000',
                            color3: '337ab7'
                        },
                        33325383
                    );
                </script>
            </div>
        </div>
    </div>
</footer>

<?= ScrollUp::widget() ?>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>


