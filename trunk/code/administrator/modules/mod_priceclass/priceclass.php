<?php

	/**
	 * @file priceclass.php 
	 * Adding, changing and deleting the priceclass-mySQLtable in an easy
	 * way for the admin (with forms etc)
	 */
	//no direct access
	defined('_AEXEC') or die("Access denied");
	
	require_once 'priceclass_functions.php';
	global $smarty;
	
	if ('POST' == $_SERVER['REQUEST_METHOD']) {

		$action = $_GET['action'];
		if($action == '1'){
			new_priceclass();
		}
		else if($action == '2'){
			show_priceclasses();
		}
		else if($action == '3'){
			delete_priceclass($_GET['where']);
		}
		else if($action == '4'){
			change_priceclass($_GET['where']);
		}
		else {
			die_error(ERR_VAR_GET);
		}
	}
	else {
		$smarty->display(PATH_SMARTY_ADMIN_MOD.'/mod_priceclass/priceclass_menu.tpl');
	}
?>