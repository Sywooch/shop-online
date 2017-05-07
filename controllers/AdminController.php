<?php

namespace app\controllers;

use app\components\AliExpressParser;
use app\components\Currency;
use app\models\admin\LoginForm;
use app\models\admin\ProductAddForm;
use app\models\admin\ProductFilter;
use app\models\admin\Source;
use app\models\admin\SourceFilter;
use app\models\Comment;
use app\models\Product;
use app\models\Property;
use app\models\Tag;
use app\components\vkApi\Post;
use app\components\vkApi\Vk;
use Yii;
use yii\base\Exception;
use yii\db\Expression;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
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
            $source->pattern = '#\>(http(s|)\:\/\/[^\s\\\'\"\<]+)#i'; //[\s]*
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
     * Парсинг из админки
     *
     * @param $id
     * @return null
     */
    public function actionParse($id)
    {
        /** @var $source Source */
        $source = Source::findOne((int)$id);
        if (!$source) {
            die("Ошибка: нет сайта для парсинга!\n");
        }
        $source->updateAttributes(['used' => new Expression('now()')]);

        $data = file_get_contents($source->url);
        if (!$data) {
            die("Ошибка: нет данных для парсинга!\n");
        }

        echo "Парсинг {$source->url} ...\n";

        // todo надо анализировать на наличие обновлений (по дате последнего поста)... подумать!

        $matches = [];
        if (!preg_match_all($source->pattern, $data, $matches) || empty($matches)) {
            die("Ошибка: нет ссылок!\n");
        }
        foreach ($matches[1] as $match) {
            $url = urldecode($match);
            $product = Product::add($url);
            echo ($product ? '+' : '-') . " $url\n";
        }

        echo "Готово!\n";

        return null;
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
     * @return string|Response
     */
    public function actionProductAdd()
    {
        $productAddForm = new ProductAddForm();

        if ($productAddForm->load(Yii::$app->request->post()) && $productAddForm->validate()) {
            $product = Product::add($productAddForm->url);
            if ($product) {
                return $this->redirect(['product-edit', 'id' => $product->id, 'back' => 'product-add']);
            }

            Yii::$app->session->addFlash("error", 'Ошибка при добавлении товара! Вероятно, такой товар уже есть...');
        }

        $lastImport = Source::find()->max('used');
        $statistic = Product::getStatistic();

        return $this->render('product-add', [
            'productAddForm' => $productAddForm,
            'lastImport' => (new \DateTime())->diff(new \DateTime($lastImport))->i,
            'countModeratedToday' => $statistic['moderatedToday'],
            'countTotalToday' => $statistic['totalToday'],
            'countModerated' => $statistic['moderated'],
            'countTotal' => $statistic['total'],
        ]);
    }

    /**
     * Редактирование/модерирование случайного товара
     *
     * @return Response
     */
    public function actionProductEditRandom()
    {
        $product = Product::find()->where('`moderated` = 0')->orderBy(new Expression('rand()'))->one();
        if (!$product) {
            Yii::$app->session->addFlash("error", 'Нет товаров для модерации!');
            return $this->redirect(['product-add']);
        }

        return $this->redirect(['product-edit', 'id' => $product->id, 'back' => 'product-add']);
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
                            Yii::$app->session->addFlash("error",
                                "Ошибка в свойстве: {$property->name} : {$property->value}");
                        }
                    }
                }

                $dataProperty = Yii::$app->request->post('Property');
                $newPropNames = ArrayHelper::getValue($dataProperty, 'name',[]);
                $newPropValues = ArrayHelper::getValue($dataProperty, 'value',[]);
                foreach ($newPropNames as $index => $newPropName) {
                    $model = new Property();
                    $model->product_id = $product->id;
                    $model->name = $newPropName;
                    $model->value = $newPropValues[$index];
                    if (!$model->save(false)) {
                        Yii::$app->session->addFlash("error",
                            "Ошибка в свойстве: {$model->name} : {$model->value}");
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

                if ($product->moderated && $product->posting) {
                    // todo адаптировать механизм постинга под другие версии PHP (отличия в работе curl)
                    // PHP 5.4 only  :-(
                    if (PHP_MAJOR_VERSION != 5 || PHP_MINOR_VERSION != 4) {
                        Yii::$app->session->addFlash("error", 'Постинг не возможнен!');
                        return $this->redirect([$back]);
                    }

                    $text = Html::encode($product->name . ".\r\nЦена: "
                        . Currency::kzt($product->price, $product->currency) . " тенге.");
                    $link = Url::to(['site/product', 'id' => $product->id, 'seoUrl' => $product->seo_url,
                        'city' => SiteController::CITY_DEFAULT], true);

                    // Файл фотографии для отправки
                    $fileInfo = pathinfo($product->image);
                    $fileExt = ArrayHelper::getValue($fileInfo, 'extension', 'png');
                    $fileName = Yii::getAlias('@runtime') . DIRECTORY_SEPARATOR . md5(time()) . '.' . $fileExt;
                    file_put_contents($fileName, file_get_contents($product->image));

                    try {
                        $post = new Post(
                            Vk::create(Yii::$app->params['vk']['token']),
                            Yii::$app->params['vk']['user_id'],
                            Yii::$app->params['vk']['group_id']
                        );
                        $post->post($text, $fileName, $link);
                    } catch (Exception $e) {
                        if (file_exists($fileName)) {
                            unlink($fileName);
                        }
                        Yii::$app->session->addFlash("error", 'Ошибка при постинге ВК! ' . print_r($e->getMessage(), true));
                    }

                    if (file_exists($fileName)) {
                        unlink($fileName);
                    }
                }

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
                Yii::$app->session->addFlash("error", 'Ошибка при удалении товара!');
            }
        }

        return $this->redirect([$back]);
    }

    /**
     * Удаление свойства товара
     *
     * @param $id
     * @return false|int
     * @throws BadRequestHttpException
     * @throws NotFoundHttpException
     * @throws \Exception
     * @throws \Throwable
     */
    public function actionPropertyDelete($id)
    {
        if (!Yii::$app->request->isPost) {
            throw new BadRequestHttpException();
        }

        $success = true;

        /** @var $model Property */
        $model = Property::findOne((int)$id);
        if ($model) {
            $success = $model->delete();
        }

        return Json::encode(['success' => $success]);
    }

    /**
     * Обновление цены товара
     *
     * @param $id
     * @return null
     */
    public function actionProductParse($id)
    {
        /** @var $product Product */
        $product = Product::findOne((int)$id);
        if (!$product) {
            die("Ошибка: товар не найден!\n");
        }

        echo "Обновление товара #{$product->id} ...\n";

        $parser = new AliExpressParser();
        $product->attributes = $parser->getProductUpdate($product->url);
        $product->updated = null;

        if (!$product->save()) {
            die(print_r($product->errors, true));
        }

        $feedbacks = $parser->getProductFeedback($product->url);
        foreach ($feedbacks as $feedback) {
            $date = (new \DateTime($feedback['date']))->format("Y-m-d H:i:s");
            /** @var $comment Comment */
            $comment = Comment::findOne(['product_id' => $product->id, 'date' => $date]);
            if ($comment) {
                continue;
            }
            $feedback['date'] = $date;
            $feedback['photos'] = join(";", $feedback['photos']);

            Comment::add($product, $feedback);
        }

        echo "Готово!\n";

        return null;
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
