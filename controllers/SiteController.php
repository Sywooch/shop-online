<?php
namespace app\controllers;

use app\components\Currency;
use app\models\CatalogueFilter;
use app\models\Category;
use app\models\City;
use app\models\Offer;
use app\models\OfferFilter;
use app\models\Product;
use app\models\Vendor;
use Yii;
use yii\base\Exception;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;

class SiteController extends Controller
{

    const CITY_DEFAULT = 'kazakhstan';

    /**
     * Регион для SEO
     * @var City
     */
    public $city;

    /**
     * Товар для SEO
     * @var Product
     */
    public $product;


    /**
     * Страница ошибки
     *
     * @return null|string
     * @throws Exception
     */
    public function actionError()
    {
        /** @var $exception Exception */
        $exception = Yii::$app->errorHandler->exception;
        if ($exception === null) {
            return null;
        }

        return $this->render('error', [
            'exception' => $exception,
            'message' => $exception->getMessage(),
            'name' => "Ошибка " . $exception->statusCode,
        ]);
    }


    /**
     * Главная страница
     *
     * @param string $city - текущий регион
     * @return string
     * @throws Exception
     */
    public function actionIndex($city = self::CITY_DEFAULT)
    {
        if ($_SERVER['REQUEST_URI'] == "/") {
            $this->redirect(["/" . $city]);
        }

        $this->city = City::find()->where(['url' => strip_tags($city)])->one();
        if (!$this->city) {
            $this->city = City::find()->where(['url' => 'kazakhstan'])->one();
        }


        $filter = new CatalogueFilter();
        $dataProvider = $filter->search(Yii::$app->request->post());

        $title = Yii::$app->params['siteName'] . " с доставкой по {$this->city->po} на " . Yii::$app->params['name'];

        return $this->render('index', [
            'filter' => $filter,
            'dataProvider' => $dataProvider,
            'title' => $title,
            'description' => $title,
        ]);
    }


    /**
     * Страница товара
     *
     * @param $city
     * @param $id
     * @return string
     * @throws Exception
     */
    public function actionProduct($city, $id)
    {
        /* @var City */
        $this->city = City::findOne(['url' => strip_tags($city)]);
        if (!$this->city) {
            $this->city = City::findOne(['url' => self::CITY_DEFAULT]);
        }

        /* @var Product */
        $this->product = Product::find()->andWhere("moderated=1 AND id=:id", [':id' => $id])->one();
        if (!$this->product) {
            throw new NotFoundHttpException("Товар не определен!");
        }

        /* SEO */
        $price = Currency::kzt($this->product->price, $this->product->currency);
        $description = "Купить {$this->product->name} за {$price} тенге с доставкой по {$this->city->po}";
        $title = "{$this->product->name} по цене {$price} тенге продаётся на " .
            Yii::$app->params['name'] . " с бесплатной доставкой по {$this->city->po}";

        return $this->render('product', [
            'title' => $title,
            'description' => $description,
            'product' => $this->product,
        ]);
    }


    /**
     * Карта сайта
     *
     * @param $file
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionSitemap($file)
    {
        $lastProduct = Product::find()->orderBy(['created' => SORT_DESC])->one();

        switch ($file) {
            case 'sitemap':
                $sitemaps = [
                    [
                        'loc' => Yii::$app->params['siteUrl'] . 'main.xml',
                        'lastmod' => $lastProduct->created,
                    ],
                ];
                foreach (City::find()->all() as $city) {
                    $sitemaps[] = [
                        'loc' => Yii::$app->params['siteUrl'] . $city->url . '.xml',
                        'lastmod' => $lastProduct->created,
                    ];
                }
                return $this->renderPartial('xml_sitemapindex', ['sitemaps' => $sitemaps]);
                break;

            case 'main':
                $items = [];
                foreach (City::find()->all() as $city) {
                    $items[] = [
                        'loc' => trim(Yii::$app->params['siteUrl'], '\\\/') . Url::toRoute([
                                'site/index',
                                'city' => $city->url,
                            ]),
                        'changefreq' => 'daily',
                        'lastmod' => $lastProduct->created,
                        'priority' => '1',
                    ];
                }
//                return $this->renderPartial('xml_sitemap', ['items' => $items]);
                break;


            default:
                $this->city = City::findOne(['url' => strip_tags($file)]);
//                if (!$this->city) {
//                    $this->city = City::findOne(['url' => self::CITY_DEFAULT]);
//                }
                if (!$this->city) {
                    throw new NotFoundHttpException();
                }
                $items = [];
                foreach (Product::find()->andWhere(['moderated' => true])->all() as $product) {
                    $items[] = [
                        'loc' => trim(Yii::$app->params['siteUrl'], '\\\/') . Url::toRoute([
                                'site/product',
                                'city' => $this->city->url,
                                'seoUrl' => $product->seo_url,
                                'id' => $product->id,
                            ]),
                        'changefreq' => 'daily',
                        'lastmod' => $product->created,
                        'priority' => '1',
                    ];
                }
        }

        return $this->renderPartial('xml_sitemap', ['items' => $items]);
    }


    /**
     * Статические страницы сайта
     *
     * @param $name
     * @return string
     * @throws Exception
     * @throws NotFoundHttpException
     */
    public function actionPage($name)
    {
        $this->city = City::find()->one();
        if (!$this->city) {
            throw new Exception("Город не определён!");
        }

        $name = preg_replace("#[^\w-]#i", '', $name);
        $isSystemPage = in_array($name, ['about', 'garanty', 'delivery', 'payments', 'contacts']);
        $pageViewPath = Yii::getAlias("@app") . "/views/site/page/";

        if (!$isSystemPage && !file_exists($pageViewPath . $name . ".php")) {
            throw new NotFoundHttpException("Страница не найдена!");
        }

        switch ($name) {
//            case 'contacts':
//                $model = new ContactForm();
//                if ($model->load(Yii::$app->request->post()) && $model->contact(Yii::$app->params['adminEmail'])) {
//                    Yii::$app->session->setFlash('contactFormSubmitted');
//                    return $this->refresh();
//                } else {
//                    return $this->render($param, ['model' => $model]);
//                }
//                break;

            default:
                // next
        }

        return $this->render("page/" . $name, []);
    }


//    public function actionFeedback()
//    {
//        $model = new ContactForm();
//
//        if ($model->load(Yii::$app->request->post()) && $model->contact(Yii::$app->params['adminEmail'])) {
//            Yii::$app->session->setFlash('contactFormSubmitted');
//            return $this->refresh();
//        }
//
//        return $this->render('feedback', ['model' => $model]);
//    }
}
