<?php

namespace app\widgets\ScrollUp;

use yii\web\JqueryAsset;

class AssetBundle extends \yii\web\AssetBundle
{
    public $sourcePath = '@app/widgets/ScrollUp/assets';

    public $css = [
        'ScrollUp.css',
    ];

    public $js = [
        'ScrollUp.js',
    ];

    public function init()
    {
        $this->depends = [ JqueryAsset::className() ];

        parent::init();
    }
}