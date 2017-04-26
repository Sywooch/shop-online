<?php

namespace app\commands;

use app\models\Link;
use app\models\Product;
use app\models\Source;
use yii\console\Controller;
use yii\console\Exception;

class ProductController extends Controller
{

    public function actionIndex()
    {

    }

    public function actionCreate($url = null)
    {
        
    }

    public function actionUpdate($url = null)
    {
        
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

}
