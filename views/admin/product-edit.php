<?php
use app\models\Product;
use app\models\Tag;
use dosamigos\ckeditor\CKEditor;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/** @var $product \app\models\Product */
/** @var $properties \app\models\Property[] */


$this->params['breadcrumbs'] = [
    ['label' => 'Панель управления', 'url' => ['admin/index']],
    ['label' => 'Товары', 'url' => ['admin/product-list']],
    'Редактирование товара',
];

?>

<?php $form = ActiveForm::begin(['action' => Url::current(), 'method' => 'post',]); ?>
<?= $form->field($product, 'url')->hiddenInput()->label(false) ?>
<?= $form->field($product, 'image')->hiddenInput()->label(false) ?>

<div class="row">
    <div class="col-sm-6"><?= Html::img($product->image, ['class' => 'image img-responsive']) ?></div>
    <div class="col-sm-6">
        <ul class="product__gallery">
            <?php foreach ($product->pictures as $picture): ?>
                <li class="product__gallery__item">
                    <img class="product__gallery__image thumbnail" src="<?= $picture->src ?>">
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
</div>


<div class="row">
    <div class="col-sm-12">
        <h3>Характеристики</h3>
        <?php foreach ($properties as $index => $property): ?>
            <div class="row">
                <div class="col-sm-6">
                    <?= $form->field($property, "[$index]name")->textInput()->label(false) ?>
                </div>
                <div class="col-sm-6">
                    <?= $form->field($property, "[$index]value")->textInput()->label(false) ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>


<div class="row">
    <div class="col-sm-12"><?= $form->field($product, "url")->textInput(['disabled' => true]) ?></div>
</div>
<div class="row">
    <div class="col-sm-12"><?= $form->field($product, "seo_url")->textInput() ?></div>
</div>
<div class="row">
    <div class="col-sm-6"><?= $form->field($product, "price")->textInput() ?></div>
    <div class="col-sm-6"><?= $form->field($product, 'currency')
            ->dropDownList(Product::getCurrencyList(), ['disabled' => true]) ?></div>
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
    <div class="col-sm-12"><?= $form->field($product, 'moderated')->dropDownList([0 => 'Нет', 1 => 'Да']) ?></div>
</div>

<div class="text-center">
    <?= Html::submitButton('Сохранить', ['class' => 'btn btn-primary']) ?>
    <?= Html::a('Удалить', ['product-delete', 'id' => $product->id],
        ['class' => 'btn btn-danger', 'data-confirm' => 'Товар будет безвозвратно удален. Продолжить?']) ?>
</div>
<?php $form->end(); ?>
