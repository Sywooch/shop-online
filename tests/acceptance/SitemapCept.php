<?php 
$I = new AcceptanceTester($scenario);
$I->wantTo('выполнить проверку карты сайта');

$I->amOnPage("sitemap.xml");
$I->see("shop-online.kz");

