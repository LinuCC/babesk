<?php

	/**
	 * @file priceclass.php 
	 * Adding, changing and deleting the priceclass-mySQLtable in an easy
	 * way for the admin (with forms etc)
	 */
	//no direct access
	defined('_AEXEC') or die("Access denied");
	
	require_once 'AdminPriceclassInterface.php';
	require_once 'AdminPriceclassProcessing.php';
	
	$pcProcessing = new AdminPriceclassProcessing();
	$pcInterface = new AdminPriceclassInterface();
	
	if ('POST' == $_SERVER['REQUEST_METHOD']) {

		switch($_GET['action']) {
			case 1:
				$pcProcessing->NewPriceclass();
				break;
			case 2:
				$pcProcessing->ShowPriceclasses();
				break;
			case 3:
				$pcProcessing->DeletePriceclass($_GET['where']);
				break;
			case 4:
				$pcProcessing->ChangePriceclass($_GET['where']);
				break;
			default:
				$pcInterface->ShowError('Wrong value of action');
		}
	}
	else {
		$pcInterface->Menu();
	}
?>