<?php

namespace app\widgets\ProductSearch;

use yii\web\JqueryAsset;

class AssetBundle extends \yii\web\AssetBundle
{
    public $sourcePath = '@app/widgets/ProductSearch/assets';

    public $css = [
        'ProductSearch.css',
    ];

    public $js = [
        'ProductSearch.js',
    ];
        
    public function init()
    {
        $this->depends = [ JqueryAsset::className() ];

        parent::init();
    }
}