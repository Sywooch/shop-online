<?php
use app\models\Product;
use app\models\Property;
use app\models\Tag;
use dosamigos\ckeditor\CKEditor;
use dosamigos\gallery\Gallery;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/** @var $this \yii\web\View */
/** @var $product \app\models\Product */
/** @var $properties \app\models\Property[] */


$this->params['breadcrumbs'] = [
    ['label' => 'Панель управления', 'url' => ['admin/index']],
    ['label' => 'Товары', 'url' => ['admin/product-list']],
    'Редактирование товара',
];

?>

<?php $form = ActiveForm::begin(['action' => Url::current(), 'method' => 'post', 'enableClientValidation' => false]); ?>
<?= $form->field($product, 'url')->hiddenInput()->label(false) ?>
<?= $form->field($product, 'image')->hiddenInput()->label(false) ?>

<div class="row">
    <div class="col-sm-6"><?= Html::img($product->image, ['class' => 'image img-responsive']) ?></div>
    <div class="col-sm-6">
        <?= Gallery::widget(['items' => array_map(function ($p) {
            return $p->src;
        }, $product->pictures)]) ?>
    </div>
</div>


<div class="row">
    <div class="col-sm-12">
        <h3>Характеристики</h3>
        <div class="jumbotron">
            <?php foreach ($properties as $index => $property): ?>
                <div class="row">
                    <div class="col-sm-6">
                        <?= $form->field($property, "[$index]name")->textInput()->label(false) ?>
                    </div>
                    <div class="col-sm-5">
                        <?= $form->field($property, "[$index]value")->textInput()->label(false) ?>
                    </div>
                    <div class="col-sm-1">
                        <a class="btn-property-delete btn btn-danger glyphicon glyphicon-remove"
                           href="<?= Url::to(['property-delete', 'id' => $index]) ?>"></a>
                    </div>
                </div>
            <?php endforeach; ?>
            <div class="row" id="new-property" style="display: none">
                <div class="col-sm-6">
                    <?= $form->field(new Property(), "name[]")->textInput(['disabled' => true])->label(false) ?>
                </div>
                <div class="col-sm-5">
                    <?= $form->field(new Property(), "value[]")->textInput(['disabled' => true])->label(false) ?>
                </div>
                <div class="col-sm-1">
                    <a class="btn-property-delete btn btn-danger glyphicon glyphicon-remove" disabled
                       href="<?= Url::to(['property-delete', 'id' => 0]) ?>"></a>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-offset-11 col-sm-1">
                    <a class="btn-property-add btn btn-success glyphicon glyphicon-plus"
                       href="<?= Url::to(['property-add']) ?>"></a>
                </div>
            </div>
        </div>
    </div>
</div>


<div class="row">
    <div class="col-sm-12"><?= $form->field($product, "url")->textInput(['disabled' => true]) ?></div>
</div>
<div class="row">
    <div class="col-sm-12"><?= $form->field($product, "seo_url")->textInput() ?></div>
</div>
<div class="row">
    <div class="col-sm-12"><?= $form->field($product, "name")->textarea() ?></div>
</div>

<div class="row">
    <div class="col-sm-12"><?= $form->field($product, 'description')
            ->widget(CKEditor::className(), ['options' => ['rows' => 6]]) ?></div>
</div>


<div class="row">
    <div class="col-sm-12">
        <?= $form->field($product, 'tags')->widget(Select2::className(), [
            'data' => ArrayHelper::map(Tag::find()->all(), 'id', 'name'),
            'options' => ['multiple' => true, 'placeholder' => 'Теги'],
            'pluginOptions' => ['tags' => true, 'maximumInputLength' => 255],
        ]); ?>
    </div>
</div>

<div class="row">
    <div class="col-sm-3"><?= $form->field($product, 'currency')
            ->dropDownList(Product::getCurrencyList(), ['disabled' => true]) ?></div>
    <div class="col-sm-3"><?= $form->field($product, "price")->textInput() ?></div>
    <div class="col-sm-3"><?= $form->field($product, 'moderated')->dropDownList([0 => 'Нет', 1 => 'Да']) ?></div>
    <div class="col-sm-3"></div>
</div>

<div class="row">
    <div class="col-sm-6"><?= $form->field($product, 'posting')->checkbox([
            'disabled' => PHP_MAJOR_VERSION != 5 || PHP_MINOR_VERSION != 4 // not 5.4
        ]) ?></div>
    <div class="col-sm-6">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Удалить', ['product-delete', 'id' => $product->id], ['class' => 'btn btn-danger',
            'data-method' => 'post', 'data-confirm' => 'Товар будет безвозвратно удален. Продолжить?']) ?>
    </div>
</div>
<?php $form->end(); ?>
<hr>

<?php
$js = <<<js
$(".btn-property-delete").click(function() {
    if ($(this).attr('disabled')) {
        return false;
    }

    if (confirm("Свойство товара будет удалено! Продолжить?")) {
        var _this = this;
        $.post(this.href, function() {
            $(_this).closest(".row").remove();
        });
    }

    return false;
});

$(".btn-property-add").click(function() {
    var new_property = $("#new-property");
    var _div = new_property.clone(true);
    _div.removeAttr("id");
    _div.find("input, a").removeAttr("disabled");
    _div.insertBefore(new_property);
    _div.show();

    return false;
});
js;

$this->registerJs($js, $this::POS_END)
?>
