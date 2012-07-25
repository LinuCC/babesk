<?php

/**
 *  @file logs.php
 *  handles the modul logs, which is an interface for the admin to get easy access to
 *  the MySQL-table
 */

require_once PATH_INCLUDE . '/Module.php';

class Logs extends Module {

	////////////////////////////////////////////////////////////////////////////////
	//Attributes
	
	////////////////////////////////////////////////////////////////////////////////
	//Constructor
	public function __construct($name, $display_name, $path) {
		parent::__construct($name, $display_name, $path);
	}
	
	////////////////////////////////////////////////////////////////////////////////
	//Methods
	public function execute() {
		//no direct access
		defined('_AEXEC') or die("Access denied");
		
		require_once 'AdminLogProcessing.php';
		require_once 'AdminLogInterface.php';
		
		$logInterface = new AdminLogInterface($this->relPath);
		$logProcessing = new AdminLogProcessing($logInterface);
		
		//the different actions the module can do
		$_chooseSev = 'choose_sev';
		$_showLogs = 'show';
		$_delLogs  = 'delete';
		
		
		if ('POST' == $_SERVER['REQUEST_METHOD'] && isset($_GET['action'])) {
			switch($_GET['action']) {
				case $_showLogs:
					$logProcessing->ShowLogs((string) trim($_GET['Category']), (string) trim($_POST['Severity']));
					break;
				case $_chooseSev:
					$logProcessing->ChooseSeverity($_POST['Category']);
					break;
				case $_delLogs:
					$logProcessing->DeleteLogs();
					break;
			}
		
		
		}
		else {
			$logProcessing->ChooseCategory();
		}
	}
}
?>