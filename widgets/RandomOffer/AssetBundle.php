<?php

namespace app\widgets\RandomOffer;

use yii\web\JqueryAsset;

class AssetBundle extends \yii\web\AssetBundle
{
    public $sourcePath = '@app/widgets/RandomOffer/assets';

    public $css = [
        'RandomOffer.css',
    ];

    public $js = [
//        'RandomOffer.js',
    ];

    public function init()
    {
        $this->depends = [ JqueryAsset::className() ];

        parent::init();
    }
}