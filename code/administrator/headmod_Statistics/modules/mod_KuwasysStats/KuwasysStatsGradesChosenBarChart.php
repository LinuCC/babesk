<?php

require_once PATH_STATISTICS_CHART . '/StatisticsBarChart.php';

class KuwasysStatsGradesChosenBarChart extends StatisticsBarChart {

	/////////////////////////////////////////////////////////////////////
	//Constructor
	/////////////////////////////////////////////////////////////////////

	public function __construct() {

		parent::__construct();
	}

	/////////////////////////////////////////////////////////////////////
	//Methods
	/////////////////////////////////////////////////////////////////////

	public function imageDraw($externalData = NULL) {

		$this->_heading['text'] = 'Wahlen in Klassen aufgeteilt';
		parent::imageDraw();
	}

	/////////////////////////////////////////////////////////////////////
	//Implements
	/////////////////////////////////////////////////////////////////////

	protected function dataFetch() {

		$this->_gradeData = TableMng::query(
			'SELECT COUNT(*) AS gradeCount, CONCAT(g.gradelevel, "-", g.label) AS gradeName
			FROM Grades g
				INNER JOIN usersInGradesAndSchoolyears uigs ON uigs.gradeId = g.ID
					AND uigs.schoolyearId = @activeSchoolyear
				INNER JOIN jointUsersInClass uic ON uic.statusId = (
					SELECT ID
					FROM usersInClassStatus
					WHERE name="active"
				) AND uic.userId = uigs.userId
				INNER JOIN class c ON c.ID = uic.ClassID
					AND c.schoolyearId = @activeSchoolyear
				GROUP BY g.ID
			');
	}

	protected function dataProcess() {

		$names = array();
		$data = array();
		$this->_pData = new pData();

		foreach($this->_gradeData as $grade) {
			$names[] = $grade['gradeName'];
			$data[] = $grade['gradeCount'];
		}

		$this->_pData->addPoints($data, 'Wahlen');
		$this->_pData->addPoints($names, 'gradeNames');
		$this->_pData->setSerieDescription('gradeNames', 'Klasse');
		$this->_pData->setAbscissa('gradeNames');
	}

	/////////////////////////////////////////////////////////////////////
	//Attributes
	/////////////////////////////////////////////////////////////////////

	protected $_gradeData;
}

?>
