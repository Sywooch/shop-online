<?php

// comment out the following two lines when deployed to production
defined('YII_DEBUG') or define('YII_DEBUG', true);
defined('YII_ENV') or define('YII_ENV', 'test');

//defined('YII_DEBUG') or define('YII_DEBUG', false);
//defined('YII_ENV') or define('YII_ENV', 'prod');

require(__DIR__ . '/../vendor/autoload.php');
require(__DIR__ . '/../vendor/yiisoft/yii2/Yii.php');

Yii::setAlias("@tests", __DIR__ . "/../tests");

$config = require(__DIR__ . '/../config/config.php');
$config = \yii\helpers\ArrayHelper::merge(
    require(__DIR__ . '/../config/config.php'),
    require(__DIR__ . '/../tests/config/config.php') // test
);

$application = new yii\web\Application($config);
$application->run();
