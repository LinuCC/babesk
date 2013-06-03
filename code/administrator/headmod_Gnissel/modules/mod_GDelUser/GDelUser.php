<?php

require_once PATH_INCLUDE . '/Module.php';

class GDelUser extends Module {

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

		require_once 'AdminGDelUserProcessing.php';
		require_once 'AdminGDelUserInterface.php';
		
		$DelUserInterface = new AdminGDelUserInterface($this->relPath);
		$DelUserProcessing = new AdminGDelUserProcessing($DelUserInterface);

		if (isset($_GET['action'])) {
			switch ($_GET['action']) {
				case 'delUser':
					$DelUserProcessing->delUser ($_POST['uid']);
				break;
				
				case 'delPdf':
					$DelUserProcessing->deletePdf();
				break;
				
				default:
					;
				break;
			}
		}
		
		if ('POST' == $_SERVER['REQUEST_METHOD'] && isset($_POST['card_ID'])) {
			$uid = $DelUserProcessing->CheckCard($_POST['card_ID']);
			$userData = $DelUserProcessing->GetUserData($uid);
			$DelUserInterface->ShowDelUser($uid,$userData);
		}
		else if(!isset($_GET['action'])){
			$DelUserInterface->CardId();
		}
	}
}

?>