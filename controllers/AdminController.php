<?php

namespace app\controllers;

use app\components\AliExpressParser;
use app\models\Link;
use app\models\admin\LoginForm;
use app\models\admin\ProductAddForm;
use app\models\admin\ProductFilter;
use app\models\admin\SourceFilter;
use app\models\Product;
use app\models\Property;
use app\models\Source;
use app\models\Tag;
use Yii;
use yii\base\Exception;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\web\Response;
use yii\web\UploadedFile;

class AdminController extends Controller
{
    public $layout = "admin";

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'update-link' => ['post'],
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    public function actions()
    {
        return [
            'error' => ['class' => 'yii\web\ErrorAction'],
        ];
    }

    /*
     * Главная страница админки
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    /**
     * Источники данных для парсинга
     *
     * @param null $id
     * @return string
     */
    public function actionSourceList($id = null)
    {
        /** @var $source Source */
        $source = Source::findOne((int)$id);
        if (!$source) {
            $source = new Source();
        }

        if ($source->load(Yii::$app->request->post()) && $source->validate()) {
            if ($source->save()) {
                return $this->redirect(['source-list']);
            }
        }

        $filter = new SourceFilter();
        $dataProvider = $filter->search(Yii::$app->request->get());

        return $this->render('source-list', ['source' => $source, 'filter' => $filter, 'dataProvider' => $dataProvider]);
    }

    /**
     * Удаление URL
     *
     * @param $id
     * @return Response
     * @throws \Exception
     * @throws \Throwable
     */
    public function actionSourceDelete($id)
    {
        /** @var $source Source */
        $source = Source::findOne((int)$id);
        if ($source) {
            if ($source->delete()) {
                Yii::$app->session->addFlash("success", 'URL удалён!');
            } else {
                Yii::$app->session->addFlash("error", 'Ошибка удаления URL!');
            }
        }
        return $this->redirect(['source-list']);
    }

    /**
     * Список товаров
     *
     * @return string
     */
    public function actionProductList()
    {
        $filter = new ProductFilter();
        $dataProvider = $filter->search(Yii::$app->request->get());

        return $this->render('product-list', ['filter' => $filter, 'dataProvider' => $dataProvider]);
    }

    /**
     * Добавление товара
     *
     * @param bool $rand
     * @return string|Response
     */
    public function actionProductAdd($rand = false)
    {
        $productAddForm = new ProductAddForm();

        if ($rand) {
            $link = Link::getNextLink();
            if (!$link) {
                Yii::$app->session->addFlash("error", 'Нет товаров для модерации!');
                return $this->redirect(['product-add']);
            }
            $productAddForm->url = ArrayHelper::getValue($link, 'url');
        }

        if ($productAddForm->load(Yii::$app->request->post()) && $productAddForm->validate()) {

            Link::deleteAll('url = :url', [':url' => $productAddForm->url]);

            $product = Product::add($productAddForm->url);
            if ($product) {
                return $this->redirect(['product-edit', 'id' => $product->id, 'back' => 'product-add']);
            }

            Yii::$app->session->addFlash("error", 'Ошибка при добавлении товара! Вероятно, такой товар уже есть...');
        }

        $countLink = Link::find()->count();
        $statistic = Product::getStatistic();

        return $this->render('product-add', [
            'productAddForm' => $productAddForm,
            'countLink' => $countLink,
            'countModeratedToday' => $statistic['moderatedToday'],
            'countTotalToday' => $statistic['totalToday'],
            'countModerated' => $statistic['moderated'],
            'countTotal' => $statistic['total'],
        ]);
    }

    /**
     * Редактирование товара
     *
     * @param $id
     * @param string $back
     * @return string|Response
     * @throws \yii\db\Exception
     */
    public function actionProductEdit($id, $back = 'product-list')
    {
        /** @var Product $product */
        $product = Product::findOne((int)$id);
        if (!$product) {
            Yii::$app->session->addFlash("error", 'Товар не найден. Выберите другой из списка!');
            return $this->redirect(['product-list']);
        }

        $properties = Property::find()->andWhere(['product_id' => $product->id])->indexBy('id')->all();

        if ($product->load(Yii::$app->request->post()) && $product->validate()) {
            $transaction = Yii::$app->db->beginTransaction();
            try {
                if (!$product->save()) {
                    throw new Exception();
                }

                if (
                    Property::loadMultiple($properties, Yii::$app->request->post())
                    && Property::validateMultiple($properties)
                ) {
                    foreach ($properties as $property) {
                        if (!$property->save(false)) {
                            throw new Exception();
                        }
                    }
                }

                $product->unlinkAll('tags', true);

                $tags = ArrayHelper::getValue(Yii::$app->request->post($product->formName()), 'tags', []);
                if (!empty($tags)) {
                    foreach ($tags as $tag) {
                        $product->link('tags', Tag::add($tag));
                    }
                }

                $transaction->commit();
                Yii::$app->session->addFlash("success", 'Товар обновлён!');
                return $this->redirect([$back]);

            } catch (Exception $e) {
                $transaction->rollBack();
                Yii::$app->session->addFlash("error", 'Ошибка добавления товара! ' . print_r($e->getMessage(), true));
            }
        }

        return $this->render('product-edit', ['product' => $product, 'properties' => $properties]);
    }

    /**
     * Удаление товара
     *
     * @param $id
     * @param string $back
     * @return Response
     * @throws BadRequestHttpException
     * @throws \Exception
     * @throws \Throwable
     */
    public function actionProductDelete($id, $back = 'product-list')
    {
        if (!Yii::$app->request->isPost) {
            throw new BadRequestHttpException();
        }

        /** @var $model Product */
        $model = Product::findOne((int)$id);
        if ($model) {
            if ($model->delete()) {
                Yii::$app->session->addFlash("success", 'Товар успешно удалён!');
            } else {
                Yii::$app->session->addFlash("error", 'Ошибка при добавлении товара! Вероятно, такой товар уже есть...');
            }
        }

        return $this->redirect([$back]);
    }


    /**
     * Вход в админку
     *
     * @return string|Response
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->redirect(['admin/index']);
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }

        return $this->render('login', ['model' => $model]);
    }

    /**
     * Выход
     *
     * @return Response
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();
        return $this->goHome();
    }

    /**
     * Примитивный контроль доступа
     *
     * @param \yii\base\Action $action
     * @return bool|Response
     * @throws \yii\web\BadRequestHttpException
     */
    public function beforeAction($action)
    {
        if (Yii::$app->user->isGuest && $action->id != "login") {
            return $this->redirect(['login']);
        }
        return parent::beforeAction($action);
    }

}
