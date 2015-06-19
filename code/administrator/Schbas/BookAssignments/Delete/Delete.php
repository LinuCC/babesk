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
		$schoolyearId = filter_input(INPUT_GET, 'schoolyearId');
		$bookAssignmentId = filter_input(INPUT_POST, 'bookAssignmentId');
		if($schoolyearId) {
			$schoolyear = $this->_em->getReference(
				'DM:SystemSchoolyears', $schoolyearId
			);
			$this->deleteAssignmentsOfSchoolyear($schoolyear);
		}
		else if($bookAssignmentId) {
			$this->deleteSingleAssignment($bookAssignmentId);
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

	protected function deleteSingleAssignment($bookAssignmentId) {

		try {
			$bookAssignment = $this->_em->find(
				'DM:SchbasUserShouldLendBook', $bookAssignmentId
			);
			if(!$bookAssignment) {
				dieHttp('Buchzuweisung nicht gefunden', 400);
			}
			$this->_em->remove($bookAssignment);
			$this->_em->flush();
			die('Buchzuweisung erfolgreich gelöscht.');

		} catch(\Exception $e) {
			$this->_logger->logO('Could not delete a single book-assignment',
				['sev' => 'error', 'moreJson' => $e->getMessage()]);
			dieHttp('Konnte die Buchzuweisung nicht löschen', 500);
		}
	}

	/////////////////////////////////////////////////////////////////////
	//Attributes
	/////////////////////////////////////////////////////////////////////

}

?>