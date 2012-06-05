<?php
require_once "Smarty/Smarty.class.php";

$smarty = new Smarty;

$smarty->setTemplateDir(__DIR__ . '/templates');
$smarty->setCompileDir(__DIR__ . '/templates_c');
$smarty->setCacheDir(__DIR__ . '/cache');
$smarty->setConfigDir(__DIR__ . '/config');

$smarty->error_reporting = E_ALL & ~E_NOTICE;

//$smarty->testInstall();
?>