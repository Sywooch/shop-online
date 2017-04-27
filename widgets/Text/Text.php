<?php

namespace app\widgets\Text;

use yii\base\Widget;

class Text extends Widget
{
    public $content;

    public $height = "40px";

    public $readMore = "Читать далее";

    public function run()
    {
        AssetBundle::register($this->view);

        return $this->render('default');
    }
}