<?php
use yii\helpers\Html;


echo Html::beginTag('div', [
        'id' => $this->context->id,
        'class' => 'text',
        'style' => $this->context->height ? "height:{$this->context->height}" : ""
    ]) .

    $this->context->content . // не кодировать для вывода HTML

    Html::tag(
        'div',
        Html::tag('span', $this->context->readMore, ['class' => 'read-more']),
        ['class' => 'read-more-block']
    ) .

    Html::tag('div', '', ['class' => 'text-fade']) .

    Html::endTag('div');