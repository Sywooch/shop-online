<?php

namespace app\widgets\Share42;

use yii\web\JqueryAsset;

class AssetBundle extends \yii\web\AssetBundle
{
    public $sourcePath = '@app/widgets/Share42/assets';

    public $css = [
        'Share42.css',
    ];

    public $js = [
        'Share42.js',
    ];

    public function init()
    {
        $this->depends = [ JqueryAsset::className() ];

        parent::init();
    }
}