<?php

namespace app\widgets\ScrollUp;

use yii\base\Widget;

class ScrollUp extends Widget
{
    public $label = "наверх";

    public function run()
    {
        AssetBundle::register($this->view);

        return $this->render('default');
    }
}