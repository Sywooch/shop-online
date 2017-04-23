<?php
//use yii\helpers\Html;
//
//echo Html::beginTag('div', ['class' => 'btn-scroll-up']) .
//    Html::button(
//        Html::tag('span', $this->context->label) .
//        Html::tag('span', '', ['class' => 'glyphicon glyphicon-arrow-up']),
//        ['class' => 'btn btn-danger']
//    ).
//    Html::endTag('div');
?>
<div class="btn-scroll-up">
    <button class="btn btn-warning">
        <span><?= $this->context->label ?></span>
        <span class="glyphicon glyphicon-arrow-up"></span>
    </button>
</div>
