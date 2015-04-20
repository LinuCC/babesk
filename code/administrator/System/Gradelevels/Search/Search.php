<?php

namespace administrator\System\Gradelevels\Search;

require_once PATH_ADMIN . '/System/Gradelevels/Gradelevels.php';

class Search extends \administrator\System\Gradelevels\Gradelevels {

	/////////////////////////////////////////////////////////////////////
	//Constructor
	/////////////////////////////////////////////////////////////////////

	/////////////////////////////////////////////////////////////////////
	//Methods
	/////////////////////////////////////////////////////////////////////

	public function execute($dataContainer) {

		$this->entryPoint($dataContainer);
		$gradelevel = filter_input(INPUT_GET, 'gradelevel');
		if($gradelevel !== false) {
			dieJson($this->searchGradelevel($gradelevel, 20));
		}
		else {
			dieHttp('Such-parameter fehlt', 400);
		}
	}

	/////////////////////////////////////////////////////////////////////
	//Implements
	/////////////////////////////////////////////////////////////////////

	protected function searchGradelevel($gradelevel, $entryCount) {

		try {
			$query = $this->_em->createQuery(
				'SELECT g FROM DM:SystemGrades g
				WHERE g.gradelevel LIKE :gradelevel
				GROUP BY g.gradelevel
			');
			$query->setParameter('gradelevel', "%$gradelevel%");
			$query->setMaxResults($entryCount);
			$grades = $query->getResult();
			$gradelevelArray = [];
			if(count($grades)) {
				foreach($grades as $grade) {
					$gradelevelArray[] = [
						'gradelevel' => $grade->getGradelevel()
					];
				}
			}
			return $gradelevelArray;
		}
		catch(\Exception $e) {
			$this->_logger->logO('Could not search the gradelevels', [
				'sev' => 'error', 'moreJson' => ['gradelevel' => $gradelevel,
				'msg' => $e->getMessage()]]);
			dieHttp('Konnte nicht nach der Klassenstufe suchen', 500);
		}
	}

	/////////////////////////////////////////////////////////////////////
	//Attributes
	/////////////////////////////////////////////////////////////////////
}
?>