<?php

require_once PATH_INCLUDE . '/Module.php';
require_once PATH_ADMIN . '/headmod_Gnissel/Gnissel.php';

class GChangeCard extends Gnissel {

	////////////////////////////////////////////////////////////////////////////////
	//Attributes

	////////////////////////////////////////////////////////////////////////////////
	//Constructor
	public function __construct($name, $display_name, $path) {
		parent::__construct($name, $display_name, $path);
	}

	////////////////////////////////////////////////////////////////////////////////
	//Methods
	public function execute($dataContainer) {
		//no direct access
		defined('_AEXEC') or die("Access denied");

		require_once 'AdminGChangeCardProcessing.php';
		require_once 'AdminGChangeCardInterface.php';

		$changeCardInterface = new AdminGChangeCardInterface($this->relPath);
		$changeCardProcessing = new AdminGChangeCardProcessing($changeCardInterface);

		if (isset($_GET['action'])) {
			switch ($_GET['action']) {
				case 'changeCard':
					$changeCardProcessing->cardChange ($_POST ['newCard'],$_POST['uid']);
				break;

				default:
					;
				break;
			}
		}

		if ('POST' == $_SERVER['REQUEST_METHOD'] && isset($_POST['username'])) {
			$uid = $changeCardProcessing->GetUserID($_POST['username']);
			$userData = $changeCardProcessing->GetUserData($uid['ID']);
			$changeCardInterface->ShowChangeCard($uid['ID'],$userData);
		}
		else{
			$changeCardInterface->Username();
		}
	}
}

?>
