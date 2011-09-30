<?php

	/**
	 * @file dummy.php
	 * description
	 */
	//no direct access
	defined('_AEXEC') or die("Access denied");
	
	require_once 'group_functions.php';
	global $smarty;
	
	if ('POST' == $_SERVER['REQUEST_METHOD']) {

		$action = $_GET['action'];
		if($action == '1'){
			new_group();
		}
		else if($action == '2'){
			
		}
		else if($action == '3'){
			
		}
		else if($action == '4'){
			
		}
		else {
			echo 'da ist etwas falschgelaufen!';
		}
	}
	else {
		//Einbinden des Templates
		$smarty->display(PATH_SMARTY.'/templates/administrator/modules/mod_groups/group_menu.tpl');
	}


?>