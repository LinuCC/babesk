<?php

require_once PATH_INCLUDE . '/Module.php';
require_once PATH_ADMIN . '/headmod_Gnissel/Gnissel.php';

class GChangePassword extends Gnissel {

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

		require_once 'AdminGChangePasswordProcessing.php';
		require_once 'AdminGChangePasswordInterface.php';

		$changePasswordInterface = new AdminGChangePasswordInterface($this->relPath);
		$changePasswordProcessing = new AdminGChangePasswordProcessing($changePasswordInterface);

		if (isset($_GET['action'])) {
			switch ($_GET['action']) {
				case 'changePassword':
					$changePasswordProcessing->pwChange ($_POST ['newPassword'], $_POST ['newPasswordRepeat'],$_POST['uid']);
				break;

				default:
					;
				break;
			}
		}

		if ('POST' == $_SERVER['REQUEST_METHOD'] && isset($_POST['card_ID'])) {
			$uid = $changePasswordProcessing->CheckCard($_POST['card_ID']);
			$userData = $changePasswordProcessing->GetUserData($uid);
			$changePasswordInterface->ShowChangePassword($uid,$userData);
		}
		else{
			$changePasswordInterface->CardId();
		}
	}
}

?>
