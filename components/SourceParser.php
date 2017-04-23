<?php

namespace app\components;

use app\models\Source;
use Yii;
use yii\base\Component;
use yii\base\Exception;

class SourceParser extends Component
{
    private $_source = null;

    public function init()
    {

    }

    protected function getNextSource()
    {
        $this->_source = Source::getNextSource(true);
        if (!$this->_source) {
            throw new Exception();
        }
    }

}