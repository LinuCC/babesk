<?php

namespace administrator\Schbas\Dashboard\Statistics;

require_once PATH_ADMIN . '/Schbas/Dashboard/Dashboard.php';

class Statistics extends \administrator\Schbas\Dashboard\Dashboard {

	/////////////////////////////////////////////////////////////////////
	//Constructor
	/////////////////////////////////////////////////////////////////////

	/////////////////////////////////////////////////////////////////////
	//Methods
	/////////////////////////////////////////////////////////////////////

	public function execute($dataContainer) {

		$this->entryPoint($dataContainer);
		dieJson([
			'gradelevelLendStatistics' => $this->gradelevelLendFetch(),
			'subjectLendStatistics' => $this->subjectLendFetch()
		]);
	}


	/////////////////////////////////////////////////////////////////////
	//Implements
	/////////////////////////////////////////////////////////////////////

	protected function subjectLendFetch() {

		$query = $this->_em->createQuery(
			'SELECT s.name AS label, COUNT(s) AS value
			FROM DM:SchbasBook b
			INNER JOIN b.exemplars e
			INNER JOIN e.lending l
			INNER JOIN b.subject s
			GROUP BY s
		');
		$data = $query->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
		return $data;
	}

	protected function gradelevelLendFetch() {

		$query = $this->_em->createQuery(
			'SELECT g.gradelevel AS label, COUNT(g) AS value
			FROM DM:SystemUsers u
			INNER JOIN u.attendances a
			INNER JOIN a.schoolyear sy WITH sy.active = 1
			INNER JOIN a.grade g
			INNER JOIN u.bookLending b
			GROUP BY g.gradelevel
		');
		$data = $query->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
		return array_map(function($row) {
			$row['label'] = 'Jahrgang ' . $row['label'];
			return $row;
		}, $data);
	}

	/////////////////////////////////////////////////////////////////////
	//Attributes
	/////////////////////////////////////////////////////////////////////

}

?>