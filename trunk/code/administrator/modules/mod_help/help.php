<?php
defined('_AEXEC') or die("Access denied");

require_once 'AdminHelpProcessing.php';
require_once 'AdminHelpInterface.php';
require_once PATH_INCLUDE . '/global_settings_access.php';

$gbManager = new GlobalSettingsManager();
$helpInterface = new AdminHelpInterface();
$helpProcessing = new AdminHelpProcessing();

if ('POST' == $_SERVER['REQUEST_METHOD']) {
	try {
		switch ($_GET['action']) {
			case 1:
			//show the Help-Text
				$helpInterface->ShowHelp($gbManager->getHelpText());
				break;
			
			case 2:
			//edit the Help-Text
				if (isset($_POST['helptext'])) {
					$helpProcessing->change_help($_POST['helptext']);
				} else {
					$helpInterface->EditHelp($gbManager->getHelpText());
				}
				break;
		}
	} catch (Exception $e) {
		die_error($e->getMessage());
	}
} else {
	$helpInterface->IndexMenu();
}
?>