<?php

	/**
	 * @file groups.php 
	 * The module offers an interface to the Admin so that he can change the group-table data
	 * without having to use programs such as phpmyadmin
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
			show_groups();
		}
		else if($action == '3'){
			delete_group($_GET['where']);
		}
		else if($action == '4'){
			change_group($_GET['where']);
		}
		else {
			echo 'Da ist etwas mit der GET-Variable action schiefgelaufen!';
		}
	}
	else {
		$smarty->display(PATH_SMARTY.'/templates/administrator/modules/mod_groups/group_menu.tpl');
	}
?>