<?php
	require "Smarty/Smarty.class.php";
	
	$smarty = new Smarty;
	
	$smarty->setTemplateDir(PATH_SMARTY.'/templates');
	$smarty->setCompileDir(PATH_SMARTY.'/templates_c');
	$smarty->setCacheDir(PATH_SMARTY.'/cache');
	$smarty->setConfigDir(PATH_SMARTY.'/config');
	
	//$smarty->testInstall();
?>