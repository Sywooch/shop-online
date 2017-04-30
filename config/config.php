<?php
Yii::setAlias('@app', realpath(__DIR__ . "/.."));
//Yii::setAlias('@webroot', realpath(__DIR__ . "/../web"));

$is_console = PHP_SAPI == 'cli' || (!isset($_SERVER['DOCUMENT_ROOT']) && !isset($_SERVER['REQUEST_URI']));

$config = [
    'id' => 'shop-onlinekz',
    'language' => 'ru',
    'basePath' => Yii::getAlias("@app"),
    'bootstrap' => ['log'],
    'components' => [
        'cache' => $is_console ? null : [
            'class' => 'yii\caching\FileCache',
        ],
        'user' => $is_console ? null : [
            'identityClass' => 'app\models\admin\User',
            'enableAutoLogin' => true,
        ],
        'errorHandler' => $is_console ? null : [
            'errorAction' => 'site/error',
        ],
        'db' => file_exists(__DIR__ . '/db.php') ? require(__DIR__ . '/db.php') : [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=localhost;dbname=dbname',
            'username' => 'dbuser',
            'password' => '123',
            'charset' => 'utf8',
        ],
        'request' => $is_console ? null : [
            'cookieValidationKey' => '8HUiodb4rczebnpqpwuerfgcfbaCY748',
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
//                'webshell'=>'webshell',
//                'webshell/<controller:\w+>'=>'webshell/<controller>',
//                'webshell/<controller:\w+>/<action:\w+>'=>'webshell/<controller>/<action>',
                //
                '/admin' => 'admin/index',
                '/admin/<action:[\w-]+>' => 'admin/<action>',

                '/<city:[\w-]+>' => 'site/index',
                '/<city:[\w-]+>/<page:[\d]+>/<per-page:[\d]+>' => 'site/index',
                '/<city:[\w-]+>/<seoUrl:[\w_-]+>-<id:[\d]+>.html' => 'site/product',
//                '/<id:[\d]+>-<seoUrl:[\w_-]+>.html' => 'site/stub',

                '/<name:[\w-]+>.html' => 'site/page',
                '/<file:[\w_-]+>.xml' => 'site/sitemap',

                '<controller:\w+>/<action:\w+>' => '<controller>/<action>',
            ],
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
    ],
    'modules' => [
//        'webshell' => [
//            'class' => 'samdark\webshell\Module',
//            'yiiScript' => __DIR__ . '/yii',
//            'allowedIPs' => ['127.0.0.1', '::1', '*'],
//            'checkAccessCallback' => function (\yii\base\Action $action) {
//                // return true if access is granted or false otherwise
//                return true;
////                return Yii::$app->getUser()->id ? true : false;
//            }
//        ],
    ],
    'params' => [
        'adminEmail' => 'admin@shop-online.kz',
        'name' => 'Shop-online.KZ',
        'siteName' => 'Только лучшие товары по ценам AliExpress, без переплаты',
        'siteUrl' => 'http://shop-online.kz/', // https

        'admitad' => 'https://alitems.com/g/1e8d114494745ac730c816525dc3e8/?ulp=',

        // todo добавить автопостинг в twitter, сейчас настроен экспорт в твиттер из ВКонтакте
        'twitter' => file_exists(__DIR__ . '/twitter.php') ? require(__DIR__ . '/twitter.php') : null,
        'vk' => file_exists(__DIR__ . '/vk.php') ? require(__DIR__ . '/vk.php') : null,
    ],
];

// fix for console app
if ($is_console) {
    $config['controllerNamespace'] = 'app\commands';
}

if (YII_DEBUG) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = ['class' => 'yii\debug\Module', 'allowedIPs' => ['192.168.1.*', '127.0.0.1', '::1']];

    // not used in this project
//    $config['bootstrap'][] = 'gii';
//    $config['modules']['gii'] = 'yii\gii\Module';
}

return $config;