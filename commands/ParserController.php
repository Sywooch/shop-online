<?php

namespace app\commands;

use app\models\Link;
use app\models\Source;
use yii\console\Controller;
use yii\console\Exception;

class ParserController extends Controller
{

    public function actionIndex()
    {

    }

    public function actionGetlinks()
    {

        $source = Source::getNextSource(true);
        if (!$source) {
            echo "Нет URL для парсинга";
            exit;
        }

        $data = file_get_contents($source->url);
        if (!$data) {
            echo "Нет данных для парсинга";
            exit;
        }

        $matches = [];
        if (!preg_match_all($source->pattern, $data, $matches) || empty($matches)) {
            echo "Нет ссылок";
            exit;
        }

        foreach ($matches[0] as $match) {
            $url = urldecode($match);
            $link = new Link();
            $link->url = $url;
            $link->source_id = $source->id;
            if ($link->save()) {
                echo '+';
            } else {
                echo '-';
            }
            echo $url . PHP_EOL;
        }

//        $transaction = \Yii::$app->db->beginTransaction();
//        try {
//
//            $transaction->commit();
//            echo "Init regions: OK" . PHP_EOL;
//
//        } catch (Exception $e) {
//            $transaction->rollBack();
//            echo $e->getMessage();
//        }
    }

    protected function unpack($link)
    {
        return file_get_contents($link);
    }
}
