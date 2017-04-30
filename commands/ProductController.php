<?php

namespace app\commands;

use app\components\AliExpressParser;
use app\models\Comment;
use app\models\Product;
use app\models\admin\Source;
use yii\console\Controller;
use yii\console\Exception;

class ProductController extends Controller
{

    public function actionIndex()
    {

    }

    /**
     * Обновление цены товара - можно запускать по крону
     * @param null $url
     */
    public function actionUpdate($url = null)
    {
        /** @var $product Product */
        $product = Product::find()
            ->where('`moderated` = 1')
            ->andFilterWhere(['url' => strip_tags($url)])
            ->orderBy(['updated' => SORT_ASC])
            ->one();
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
    }

    /**
     * Импорт товаров на модерацию - можно запускать по крону
     * - за один запуск обрабатывает один источник ссылок на товары
     */
    public function actionImport()
    {
        $source = Source::getNextSource(true);
        if (!$source) {
            die("Ошибка: нет URL для парсинга!\n");
        }

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
    }

    public function actionTest()
    {
        $url = 'https://ru.aliexpress.com/store/product/2015-new-super-wide-tire-bike-Snowmobile-ATV-26-bicycle-disc-brakes-bicycle-shock-absorbers-Russia/1803142_32344137836.html';
        $parser = new AliExpressParser();
        $parser->getProductFeedback($url);
    }
}
