<?php
use yii\helpers\Url;

/* @var $count int */
/*
    <meta itemprop="numberOfItems" content="<?php//echo $count; ?>" />
    {summary}
 */

?>
<noindex>
    <div class="sorter_block">Критерии сортировки: {sorter}</div>
</noindex>

<div itemscope itemtype="http://schema.org/ItemList">
    <link itemprop="url" href="<?= Url::current() ?>"/>
    {items}
</div>

<noindex>
    {pager}
</noindex>