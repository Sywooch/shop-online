<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\LoginForm */

$this->title = 'Login';
//$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-login">
    <div class="col-sm-4"></div>
    <div class="col-sm-4">
        <?php $form = ActiveForm::begin([
            'options' => ['class' => 'form-horizontal'],
            'fieldConfig' => [
                'template' => '{label}<div class="col-xs-10">{input}</div>',
                'labelOptions' => ['class' => 'col-xs-2 control-label'],
            ],
        ]); ?>

        <?= $form->field($model, 'username') ?>
        <?= $form->field($model, 'password')->passwordInput() ?>

        <div class="form-group text-center">
            <?= $form->field($model, 'rememberMe')->checkbox() ?>
            <?= Html::submitButton('Войти', ['class' => 'btn btn-primary col-xs-offset-3 col-xs-6']) ?>
        </div>

        <?php ActiveForm::end(); ?>
    </div>
    <div class="col-sm-4"></div>
</div>
