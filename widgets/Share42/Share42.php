<?php

namespace app\widgets\Share42;

use yii\base\Widget;

class Share42 extends Widget
{
    public $url = null;

    public $title = null;

    public $description = null;

    public $image = null;

    public $cssClass = "";

    public function run()
    {
        AssetBundle::register($this->view);

        return $this->render('default');
    }
}