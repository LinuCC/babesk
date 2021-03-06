<?php

require_once PATH_INCLUDE . '/Module.php';
require_once PATH_ADMIN . '/Gnissel/Gnissel.php';

class GCardInfo extends Gnissel {

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

		require_once 'AdminCardInfoProcessing.php';
		require_once 'AdminCardInfoInterface.php';
		
		$interface = $dataContainer->getInterface();
		$cardInfoInterface = new AdminCardInfoInterface($this->relPath);
		$cardInfoProcessing = new AdminCardInfoProcessing($cardInfoInterface);
		
		if ('POST' == $_SERVER['REQUEST_METHOD'] && isset($_POST['card_ID'])) {
			$uid = $cardInfoProcessing->CheckCard($_POST['card_ID']);
			$userData = $cardInfoProcessing->GetUserData($uid);
			$cardInfoInterface->ShowCardInfo($userData,$_POST['card_ID']);				
		}
		else if (isset($_GET['lostcard'])){
			TableMng::query(sprintf("UPDATE BabeskCards SET lost=1 WHERE cardnumber = '%s'", $_GET['lostcard']));
			$interface->dieMsg("Karte wurde verloren gemeldet!");	
			$uid = $cardInfoProcessing->CheckCard($_GET['lostcard']);
			$userData = $cardInfoProcessing->GetUserData($uid);
			$cardInfoInterface->ShowCardInfo($userData,$_GET['lostcard']);		
		}
		else{
			$cardInfoInterface->CardId();				
		}
	}
}

?>
