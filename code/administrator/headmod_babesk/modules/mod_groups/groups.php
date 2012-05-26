<?php

	/**
	 * @file groups.php 
	 * The module offers an interface to the Admin so that he can change the group-table data
	 * without having to use programs such as phpmyadmin
	 */
	//no direct access
	defined('_AEXEC') or die("Access denied");

	require_once 'AdminGroupProcessing.php';
	require_once 'AdminGroupInterface.php';
	
	$groupProcessing = new AdminGroupProcessing();
	$groupInterface = new AdminGroupInterface();
	
	if ('POST' == $_SERVER['REQUEST_METHOD']) {

		$action = $_GET['action'];
		switch ($action) {
			case '1':
				$groupProcessing->NewGroup();
				break;
			case '2':
				$groupProcessing->ShowGroups();
				break;
			case '3':
				$groupProcessing->DeleteGroup($_GET['where']);
				break;
			case '4':
				$groupProcessing->ChangeGroup($_GET['where']);
				break;
			default:
				$groupInterface->ShowError('Wrong value of GET-variable action!');
				break;
		}
	}
	else {
		$groupInterface->Menu();
	}
?>