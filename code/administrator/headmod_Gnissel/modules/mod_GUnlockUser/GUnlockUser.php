<?php

require_once PATH_INCLUDE . '/Module.php';

class GUnlockUser extends Module {

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

		require_once 'AdminGUnlockUserProcessing.php';
		require_once 'AdminGUnlockUserInterface.php';
		
		$unlockUserInterface = new AdminGUnlockUserInterface($this->relPath);
		$unlockUserProcessing = new AdminGUnlockUserProcessing($unlockUserInterface);

		if (isset($_GET['action'])) {
			switch ($_GET['action']) {
				case 'unlockUser':
					$unlockUserProcessing->unlockUser ($_POST['uid']);
				break;
				
				default:
					;
				break;
			}
		}
		
		if ('POST' == $_SERVER['REQUEST_METHOD'] && isset($_POST['card_ID'])) {
			$uid = $unlockUserProcessing->CheckCard($_POST['card_ID']);
			$userData = $unlockUserProcessing->GetUserData($uid);
			$unlockUserInterface->ShowUnlockUser($uid,$userData);
		}
		else{
			$unlockUserInterface->CardId();
		}
	}
}

?>