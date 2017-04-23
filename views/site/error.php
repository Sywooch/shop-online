<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $name string */
/* @var $message string */
/* @var $exception Exception */

$this->title = $name;
?>
<div class="site-error">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="alert alert-danger">
        <?= nl2br(Html::encode($message)) ?>
    </div>

    <p>
        Не обращайте внимания! Это всего лишь произошла какая-то маленькая ошибочка :-)
    </p>
    <p>
        Наши техники уведомлены и уже работают над её устранением.
    </p>

    <h3>Как Вам вот это?</h3>
    <?= \app\widgets\RandomOffer\RandomOffer::widget(['count' => 6]) ?>

</div>
