<?php

return \yii\helpers\ArrayHelper::merge(
    require(__DIR__ . '/../../config/config.php'),
    require(__DIR__ . '/config.php') // test
);