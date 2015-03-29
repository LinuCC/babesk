<?php

require_once PATH_INCLUDE . '/Module.php';
require_once PATH_ADMIN . '/Schbas/Schbas.php';

class Retour extends Schbas {


	/////////////////////////////////////////////////////////////////////
	//Constructor
	/////////////////////////////////////////////////////////////////////

	public function __construct($name, $display_name, $path) {
		parent::__construct($name, $display_name, $path);
	}

	/////////////////////////////////////////////////////////////////////
	//Methods
	/////////////////////////////////////////////////////////////////////

	public function execute($dataContainer) {

		$this->entryPoint($dataContainer);

		require_once 'AdminRetourInterface.php';
		require_once 'AdminRetourProcessing.php';

		$RetourInterface = new AdminRetourInterface($this->relPath);
		$RetourProcessing = new AdminRetourProcessing(
			$RetourInterface, $dataContainer
		);

		if ('GET' == $_SERVER['REQUEST_METHOD'] && isset($_GET['inventarnr'])
		) {
			try {
				$res = $RetourProcessing->RetourBook(
					urldecode($_GET['inventarnr']), $_GET['uid']
				);
			} catch (Exception $e) {
				die(
					'Konnte das Buch nicht zurückgeben. Möglicherweise '.
					'falsch eingescannt?'
				);
				die();
			}
			if(!$res) {
				$RetourInterface->RetourEmpty();
			}
			else {
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

	/////////////////////////////////////////////////////////////////////
	//Implements
	/////////////////////////////////////////////////////////////////////

	protected function entryPoint($dataContainer) {

		parent::entryPoint($dataContainer);
		$this->initSmartyVariables();
	}

	/////////////////////////////////////////////////////////////////////
	//Attributes
	/////////////////////////////////////////////////////////////////////


}

?>
