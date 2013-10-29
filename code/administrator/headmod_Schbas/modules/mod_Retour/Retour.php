<?php

require_once PATH_INCLUDE . '/Module.php';
require_once PATH_ADMIN . '/headmod_Schbas/Schbas.php';

class Retour extends Schbas {

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

		defined('_AEXEC') or die('Access denied');

		require_once 'AdminRetourInterface.php';
		require_once 'AdminRetourProcessing.php';

		$RetourInterface = new AdminRetourInterface($this->relPath);
		$RetourProcessing = new AdminRetourProcessing($RetourInterface);

		if ('GET' == $_SERVER['REQUEST_METHOD'] && isset($_GET['inventarnr'])) {
			if (!$RetourProcessing->RetourBook(urldecode($_GET['inventarnr']),$_GET['uid'])) {
				$RetourInterface->RetourEmpty();
			} else {
			$RetourProcessing->RetourTableDataAjax($_GET['card_ID']);
			}
		}
		else if ('POST' == $_SERVER['REQUEST_METHOD'] && isset($_POST['card_ID'])) {
			$RetourProcessing->RetourTableData($_POST['card_ID']);
		}
		else{
			$RetourInterface->CardId();
		}

	}
}

?>
