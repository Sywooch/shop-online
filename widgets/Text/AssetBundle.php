<?php

namespace app\widgets\Text;

use yii\web\JqueryAsset;

class AssetBundle extends \yii\web\AssetBundle
{
    public $sourcePath = '@app/widgets/Text/assets';

    public $css = [
        'Text.css',
    ];

    public $js = [
        'Text.js',
    ];

    public function init()
    {
        $this->depends = [ JqueryAsset::className() ];

        parent::init();
    }
}