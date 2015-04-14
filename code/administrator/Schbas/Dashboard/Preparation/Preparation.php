<?php

namespace administrator\Schbas\Dashboard\Preparation;

require_once PATH_ADMIN . '/Schbas/Dashboard/Dashboard.php';

class Preparation extends \administrator\Schbas\Dashboard\Dashboard {

	/////////////////////////////////////////////////////////////////////
	//Constructor
	/////////////////////////////////////////////////////////////////////

	/////////////////////////////////////////////////////////////////////
	//Methods
	/////////////////////////////////////////////////////////////////////

	public function execute($dataContainer) {

		$this->entryPoint($dataContainer);
		$data = $this->fetchData();
		dieJson($data);
	}

	/////////////////////////////////////////////////////////////////////
	//Implements
	/////////////////////////////////////////////////////////////////////

	protected function fetchData() {

		$prepSchoolyearData = $this->fetchPreparationSchoolyearData();
		$schbasClaimStatus = $this->fetchSchbasClaimStatus();
		return [
			'prepSchoolyear' => $prepSchoolyearData,
			'schbasClaimStatus' => $schbasClaimStatus
		];
	}

	protected function fetchPreparationSchoolyearData() {

		$preparationSchoolyearId = $this->_em
			->getRepository('DM:SystemGlobalSettings')
			->findOneByName('schbasPreparationSchoolyearId')
			->getValue();
		$stmt = $this->_em->getConnection()->prepare(
			'SELECT sy.ID AS id, sy.label AS label,
				COUNT(usb.id) as assignmentCount
			FROM SystemSchoolyears sy
			LEFT JOIN SchbasUsersShouldLendBooks usb
				ON sy.ID = usb.schoolyearId
			GROUP BY sy.ID
		');
		$stmt->execute();
		$schoolyears = $stmt->fetchAll();
		$prepData = array();
		foreach($schoolyears as $schoolyear) {
			if($schoolyear['id'] == $preparationSchoolyearId) {
				$prepData['active'] = [
					'id' => $schoolyear['id'],
					'name' => $schoolyear['label'],
					'entriesExist' => ($schoolyear['assignmentCount'] > 0)
				];
			}
			else {
				$prepData['alternatives'][] = [
					'id' => $schoolyear['id'],
					'name' => $schoolyear['label'],
					'entriesExist' => ($schoolyear['assignmentCount'] > 0)
				];
			}
		}
		return $prepData;
	}

	protected function fetchSchbasClaimStatus() {

		$status = $this->_em->getRepository('DM:SystemGlobalSettings')
			->findOneByName('isSchbasClaimEnabled')
			->getValue();
		return $status != 0;
	}

	/////////////////////////////////////////////////////////////////////
	//Attributes
	/////////////////////////////////////////////////////////////////////

}

?>