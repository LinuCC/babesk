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
			FROM SystemGrades g
				INNER JOIN SystemUsersInGradesAndSchoolyears uigs ON uigs.gradeId = g.ID
					AND uigs.schoolyearId = @activeSchoolyear
				INNER JOIN KuwasysUsersInClassesAndCategories uicc
					ON uicc.statusId = (
						SELECT ID
						FROM KuwasysUsersInClassStatuses
						WHERE name="active"
				) AND uicc.userId = uigs.userId
				INNER JOIN KuwasysClasses c ON c.ID = uicc.ClassID
					AND c.schoolyearId = @activeSchoolyear
					AND c.isOptional = 0
				GROUP BY g.ID
				ORDER BY g.gradelevel, g.label
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
