<?php

namespace administrator\Schbas\Dashboard\Preparation;

require_once PATH_ADMIN . '/Schbas/Dashboard/Preparation/Preparation.php';

/**
 * Handles the ajax-requests for the SchbasPreparationSchoolyear
 */
class Schoolyear
	extends \administrator\Schbas\Dashboard\Preparation\Preparation {

	/////////////////////////////////////////////////////////////////////
	//Constructor
	/////////////////////////////////////////////////////////////////////

	/////////////////////////////////////////////////////////////////////
	//Methods
	/////////////////////////////////////////////////////////////////////

	public function execute($dataContainer) {

		$this->entryPoint($dataContainer);

		if(!isset($_GET['schoolyearId']) || !isset($_GET['action'])) {
			dieHttp('Parameter fehlen', 400);
		}

		switch($_GET['action']) {
			case 'change':
				$this->preparationSchoolyearChange($_GET['schoolyearId']);
			default:
				dieHttp('Unbekannte Action-value', 400);
		}
	}

	/////////////////////////////////////////////////////////////////////
	//Implements
	/////////////////////////////////////////////////////////////////////

	protected function preparationSchoolyearChange($id) {

		$schoolyear = $this->_em->find('DM:SystemSchoolyears', $id);

		if(!$schoolyear) {
			$this->_logger->log('Could not find the schoolyear',
				['sev' => 'error', 'moreJson' => ['id' => $id]]);
			dieHttp('Das Schuljahr wurde nicht gefunden', 422);
		}

		$configEntry = $this->_em->getRepository('DM:SystemGlobalSettings')
			->findOneByName('schbasPreparationSchoolyearId');

		if(!$configEntry) {
			$this->_logger->log('Could not find the ' .
				'schbasPreparationSchoolyearId', 'error');
			dieHttp('Die Einstellung wurde nicht gefunden', 500);
		}

		$configEntry->setValue($schoolyear->getId());
		$this->_em->persist($configEntry);
		$this->_em->flush();
		die('Schuljahr erfolgreich verändert.');
	}

	/////////////////////////////////////////////////////////////////////
	//Attributes
	/////////////////////////////////////////////////////////////////////

}

?>