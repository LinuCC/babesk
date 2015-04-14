<?php

namespace administrator\Schbas\BookAssignments\Delete;

require_once PATH_ADMIN . '/Schbas/BookAssignments/BookAssignments.php';

class Delete extends \administrator\Schbas\BookAssignments\BookAssignments {

	/////////////////////////////////////////////////////////////////////
	//Constructor
	/////////////////////////////////////////////////////////////////////

	/////////////////////////////////////////////////////////////////////
	//Methods
	/////////////////////////////////////////////////////////////////////

	public function execute($dataContainer) {

		$this->entryPoint($dataContainer);
		$id = filter_input(INPUT_GET, 'schoolyearId');
		if($id) {
			$schoolyear = $this->_em->getReference(
				'DM:SystemSchoolyears', $id
			);
			$this->deleteAssignmentsOfSchoolyear($schoolyear);
		}
		else {
			dieHttp('Fehlende Parameter', 400);
		}
	}

	/////////////////////////////////////////////////////////////////////
	//Implements
	/////////////////////////////////////////////////////////////////////

	protected function deleteAssignmentsOfSchoolyear($schoolyear) {

		try {
			$query = $this->_em->createQuery(
				'DELETE FROM DM:SchbasUserShouldLendBook usb
				WHERE usb.schoolyear = :schoolyear
			');
			$query->setParameter('schoolyear', $schoolyear);
			$query->getResult();
			die('Die Buchzuweisungen wurden erfolgreich gelöscht');
		}
		catch(Exception $e) {
			$this->_logger->logO('Could not delete assignments of schoolyear',
				['sev' => 'error', 'moreJson' => ['msg' => $e->getMessage(),
					'id' => $schoolyear->getId()]]);
			dieHttp('Konnte die Buchzuweisungen nicht löschen', 500);
		}
	}

	/////////////////////////////////////////////////////////////////////
	//Attributes
	/////////////////////////////////////////////////////////////////////

}

?>