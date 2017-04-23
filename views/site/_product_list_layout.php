<?php
use yii\helpers\Url;

/* @var $count int */
/*
    <meta itemprop="numberOfItems" content="<?php//echo $count; ?>" />
    {sorter}
    {summary}
 */

?>
<div itemscope itemtype="http://schema.org/ItemList">
    <link itemprop="url" href="<?= Url::current() ?>" />
    {items}
</div>
{pager}