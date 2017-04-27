<?php
/** @var $this \yii\web\View */

$js = <<<js

    $('.btn-parse').click(function() {
    console.log('click');
        // модальный диалог
        var dlgParse = $('#dlgParse');
        dlgParse.find('.loading').show();
        dlgParse.find('pre').text("").hide();
        dlgParse.dialog({title: "Идет парсинг, ждите...", width: 500, modal: true});

        // запуск парсера и ожидание результата
        $.post(this.href, function(response) {
            dlgParse.dialog('option', 'title', 'Парсинг завершён!');
            dlgParse.find('.loading').hide();
            dlgParse.find('pre').text(response).show();
        });

        return false;
    });

js;
$this->registerJs($js, $this::POS_END);
?>
<div id="dlgParse" style="display: none;">
    <div class="loading text-center">
        <img src="/images/loading.gif">
    </div>
    <pre></pre>
</div>