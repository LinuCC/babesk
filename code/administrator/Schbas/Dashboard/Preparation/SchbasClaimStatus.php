<?php

namespace administrator\Schbas\Dashboard\Preparation;

require_once PATH_ADMIN . '/Schbas/Dashboard/Preparation/Preparation.php';

class SchbasClaimStatus
	extends \administrator\Schbas\Dashboard\Preparation\Preparation {

	/////////////////////////////////////////////////////////////////////
	//Constructor
	/////////////////////////////////////////////////////////////////////

	/////////////////////////////////////////////////////////////////////
	//Methods
	/////////////////////////////////////////////////////////////////////

	public function execute($dataContainer) {

		$this->entryPoint($dataContainer);
		$status = filter_input(
			INPUT_GET, 'newStatus', FILTER_VALIDATE_BOOLEAN,
			['flags' => FILTER_NULL_ON_FAILURE]
		);
		if($status !== Null) {
			$this->changeStatus($status);
		}
		else {
			dieHttp('Parameter fehlen', 400);
		}
	}

	/////////////////////////////////////////////////////////////////////
	//Implements
	/////////////////////////////////////////////////////////////////////

	protected function changeStatus($newStatus) {

		$statusEntry = $this->_em->getRepository('DM:SystemGlobalSettings')
			->findOneByName('isSchbasClaimEnabled');
		if(!$statusEntry) {
			$this->_logger->logO('Could not find isSchbasClaimEnabled',
				['sev' => 'error']);
			dieHttp('Konnte Einstellung nicht finden', 500);
		}
		if($statusEntry->getValue() != $newStatus) {
			$val = ($newStatus) ? 1 : 0;
			$statusEntry->setValue($val);
			$this->_em->flush();
			die('Status wurde erfolgreich verändert');
		}
		else {
			die('Status hat gleichen Wert. Er wurde nicht verändert.');
		}
	}

	/////////////////////////////////////////////////////////////////////
	//Attributes
	/////////////////////////////////////////////////////////////////////

}

?>