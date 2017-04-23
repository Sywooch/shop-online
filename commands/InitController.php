<?php

namespace app\commands;

use app\models\City;
use yii\console\Controller;
use yii\console\Exception;

class InitController extends Controller
{

    public function actionIndex($country = 'kz')
    {

        $this->actionRegions($country);
    }

    public function actionRegions($country)
    {
        $regions = [];
        switch ($country) {
            case 'ru':
                $regions = [];
                break;
            case 'kz':
                $regions =[
                    ['name' => 'Казахстан', 'v' => 'Казахстане', 'po' => 'Казахстану', 'url' => 'kazakhstan'],
                    ['name' => 'Астана', 'v' => 'Астане', 'po' => 'Астане', 'url' => 'astana'],
                    ['name' => 'Алматы', 'v' => 'Алматы', 'po' => 'Алматы', 'url' => 'almaty'],
                    ['name' => 'Актобе', 'v' => 'Актобе', 'po' => 'Актобе', 'url' => 'aktobe'],
                    ['name' => 'Актау', 'v' => 'Актау', 'po' => 'Актау', 'url' => 'aktau'],
                    ['name' => 'Атырау', 'v' => 'Атырау', 'po' => 'Атырау', 'url' => 'atyrau'],
                    ['name' => 'Караганда', 'v' => 'Караганде', 'po' => 'Караганде', 'url' => 'karaganda'],
                    ['name' => 'Семей', 'v' => 'Семее', 'po' => 'Семею', 'url' => 'semey'],
                    ['name' => 'Тараз', 'v' => 'Таразе', 'po' => 'Таразу', 'url' => 'taraz'],
                    ['name' => 'Павлодар', 'v' => 'Павлодаре', 'po' => 'Павлодару', 'url' => 'pavlodar'],
                    ['name' => 'Петропавловск', 'v' => 'Петропавловске', 'po' => 'Петропавловску', 'url' => 'petropavlovsk'],
                    ['name' => 'Уральск', 'v' => 'Уральске', 'po' => 'Уральску', 'url' => 'uralsk'],
                    ['name' => 'Усть-Каменогорск', 'v' => 'Усть-Каменогорске', 'po' => 'Усть-Каменогорску', 'url' => 'ust-kamenogorsk'],
                    ['name' => 'Кокшетау', 'v' => 'Кокшетау', 'po' => 'Кокшетау', 'url' => 'kokshetau'],
                    ['name' => 'Шымкент', 'v' => 'Шымкенте', 'po' => 'Шымкенту', 'url' => 'shymkent']
                ];
                break;
            default:
                throw new Exception("Error!");
        }

        $transaction = \Yii::$app->db->beginTransaction();
        try {
            City::deleteAll();
            foreach ($regions as $region) {
                $model = new City();
                $model->attributes = $region;
                if (!$model->save()) {
                    throw new Exception("Ошибка при сохранении региона!");
                }
            }
            $transaction->commit();

            echo "Init regions: OK" . PHP_EOL;

        } catch (Exception $e) {
            $transaction->rollBack();
            echo $e->getMessage();
        }
    }

}
