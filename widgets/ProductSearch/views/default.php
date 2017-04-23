<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/** @var $this \yii\web\View */
/** @var $tags \app\models\Tag[] */
/** @var $this ->context->model CatalogueFilter */
?>
<div id="<?= $this->context->id ?>" class="product_search form_panel">
    <?php
    $form = ActiveForm::begin(['enableClientValidation' => false]);
    ?>
    <div style="display: table; width: 100%;">
        <div class="product_search_block">
            <div class="product_search_block_label">
                <label class="product_search_label" for="<?= $this->context->id ?>_product_search_input">Поиск</label>
            </div>
            <div class="product_search_block_query">
                <?= Html::activeTextInput($this->context->model, 'query', [
                    'class' => "product_search_input form_input",
                    'id' => $this->context->id . "_product_search_input",
                    'placeholder' => "Поиск по названию, описанию и тегам",
                ]) ?>
                <button type="submit" class="product_search_button"><i class="glyphicon glyphicon-search "></i></button>
            </div>
        </div>
    </div>

    <div class="product_search_filter_block">
        <input class="product_search_filter_handler" id="<?= $this->context->id ?>_product_search_filter_handler"
            <?= $this->context->model->filter ? "checked" : "" ?>
               type="checkbox" name="<?= $this->context->model->formName() . '[filter]' ?>">
        <div class="product_search_filter">
            <div style="display: table; width: 100%;">
                <div style="display: table-row;">
                    <label class="product_search_block_label">Цена(₸)</label>
                    <div style="display: table-cell; width: 190px;">
                        <?= Html::activeTextInput($this->context->model, 'priceLow', [
                            'class' => "product_search_filter_priceLow form_input",
                            'id' => $this->context->id . "_product_search_filter_priceLow",
                            'placeholder' => "от",
                        ]) ?>
                        <?= Html::activeTextInput($this->context->model, 'priceHigh', [
                            'class' => "product_search_filter_priceHigh form_input",
                            'id' => $this->context->id . "_product_search_filter_priceHigh",
                            'placeholder' => "до",
                        ]) ?>
                    </div>

                    <div style="display: table-cell;">
                        <label class="product_search_block_label" style="width: 100px;">Теги TOP-5</label>
                        <div style="display: table-cell;">
                            <?php
                            foreach ($tags as $tag) {
                                echo Html::a(Html::encode($tag['name']), Url::current(),
                                    [
                                        'class' => 'product_search_filter_tag_link',
                                        'data-method' => 'post',
                                        'onclick' => new \yii\web\JsExpression('$(".product_search_input").val("' .
                                            Html::encode($tag['name']) . '");'),
                                    ]
                                ) . PHP_EOL;
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>

            <div style="overflow: hidden;">
                <div class="product_search_filter_checkboxes">
                    <?php // todo  добавить работающий функционал для этой бутафории ?>
                    <input id="<?= $this->context->id ?>_product_search_filter_available"
                           type="checkbox" checked disabled />
                    <label for="<?= $this->context->id ?>_product_search_filter_available">Есть в наличии</label>


                    <input id="<?= $this->context->id ?>_product_search_filter_delivery"
                           type="checkbox" checked disabled />
                    <label for="<?= $this->context->id ?>_product_search_filter_delivery">Бесплатная доставка</label>
                </div>

                <div class="pull-right">
                    <?= Html::a('<i class="glyphicon glyphicon-remove"></i> Сбросить', ['/'],
                        ['class' => 'btn btn-default'/*, 'data-method' => 'post', 'data-params' => 'myParam=anyValue'*/]) ?>
                    <button type="submit" class="btn btn-warning">
                        <i class="glyphicon glyphicon-ok"></i> Применить
                    </button>
                </div>
            </div>
        </div>

        <div class="product_search_filter_handlers">
            <label class="product_search_filter_handler_label product_search_filter_handler_show"
                   for="<?= $this->context->id ?>_product_search_filter_handler">
                <i class="glyphicon glyphicon-download"></i>&nbsp;Развернуть
            </label>
            <label class="product_search_filter_handler_label product_search_filter_handler_hide"
                   for="<?= $this->context->id ?>_product_search_filter_handler">
                <i class="glyphicon glyphicon-upload"></i>&nbsp;Свернуть
            </label>
        </div>

    </div>

    <?php
    $form::end();
    ?>
</div>