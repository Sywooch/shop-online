<?php 
$I = new AcceptanceTester($scenario);
$I->wantTo('выполнить проверку страницы каталога');

$I->amOnPage("/");
$I->see("Shop-online.kz");

